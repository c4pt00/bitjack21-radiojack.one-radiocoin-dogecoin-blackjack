<?php

require_once "jsonRPCClient.php";

define('CHIPSPERBTC', 1);
$sessionsalt = 'rakdsayratdsa';

$mail_headers = 'X-Mailer: RadioJack.one' . "\r\n" .
    'Reply-To: radiojack@gmail.com' . "\r\n" .
    'Errors-To: radiojack@gmail.com';
// ADMIN: Put your email address in the 2 lines above


function mail_payout($email, $user, $amount, $wallet, $txid)
{
   global $mail_headers;

   $message = "Hello,\n\nThis is a RADC withdrawal notification from http://www.radiojack.one for $user.\n\n$amount RADC has just been sent to your Bitcoin wallet $wallet\n\nYou can track this payout on blockexplorer.com using the transaction ID $txid as per below:\n\nhttp://blockexplorer.com/tx/$txid\n\nThanks for playing!\n\n--\nradiojack.one";

   mail($email, "[RadioJack] Withdrawal Notification", $message, $mail_headers);
}

function mail_manual_payout($email, $user, $amount, $wallet)
{
   global $mail_headers;

   $message = "Hello,\n\nThis is a notice that you have a pending RADC withdrawal from http://www.radiojack.one for $user.\n\nA withdrawal of $amount RADC is now queued for manual processing and will be sent within 24 hours to address $wallet\n\nThanks for playing!\n\n--\nradiojack.one";

   $time = date("Y-m-d H:i:s +0000");

   mail($email, "[RadioJack] Pending Withdrawal Notification", $message, $mail_headers);
   mail('ADMIN_EMAIL_ADDY@gmail.com', '[RadioJack] MANUAL PAYOUT REQUEST', "USER=$user\nAMOUNT=$amount\nWALLET=$wallet\nTIME=$time", $mail_headers);
}

function mail_error($msg)
{
   global $mail_headers;
   mail('radiojack@gmail.com', '[RadioJack] ERROR', $msg, $mail_headers);
}

function drawmenu()
{
  echo '<ul id="a">';
  if(isset($_SESSION['username']))
  {
    echo <<< DONE
    <li class="mb1"><a href="index.php">Home</a></li>
    <li class="mb1"><a href="play.php">Play</a></li>
    <li class="mb1"><a href="withdraw.php">Deposit/Withdraw</a></li>
    <li class="mb1"><a href="rules.php">Rules</a></li>
    <li class="mb1"><a href="contact.php">Contact</a></li>
    <li class="mb1"><a href="logout.php">Log Out</a></li>
DONE;
  }
  else
  {
    echo <<< DONE
    <li class="mb2"><a href="index.php">Home</a></li>
    <li class="mb2"><a href="register.php">Register</a></li>
    <li class="mb2"><a href="rules.php">Rules</a></li>
    <li class="mb2"><a href="contact.php">Contact</a></li>
DONE;
  }
  echo '</ul>';
}


function hash_password($password, $nonce) {
  $site_key = '12qwerty56';
  return hash_hmac('sha512', $password . $nonce, $site_key);
}

function generateSalt($max = 15) {
	$characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$i = 0;
	$salt = "";
	do {
		$salt .= $characterList{mt_rand(0,strlen($characterList)-1)};
		$i++;
	} while ($i <= $max);
	return $salt;
}


function connectBitcoin()
{


          $bitcoin = new jsonRPCClient('http://root:rpcpassword@127.0.0.1:9332/');

	  return $bitcoin;
}

function connectDB()
{

$sql = new mysqli('localhost', 'root', 'yourpassword-here', 'radiojack');
 
if($sql->connect_errno > 0){
    die('Unable to connect to database [' . $sql->connect_error . ']');
}
 
$con = mysqli_connect("localhost","root","yourpassword-here","radiojack") or die("Some error occurred during connection " . mysqli_error($con));


  return $con;


}

function getBTCBalance($user=null)
{
  if(is_null($user) && !isset($_SESSION['username']))
  {
    return 0;
  }
  if(is_null($user))
  {
    $user = $_SESSION['username'];
  }
  $bal = null;
  $bitcoin = connectBitcoin();
  try {
    $bal = ($bitcoin->getbalance($user));
  } catch (Exception $e) {
    return null;
  }
  return BTCRound($bal);
}


function BTCRound($value)
{
//  return floor(($value) * 1e8 + .5) * .00000001;
//  return floor(($value) * 1e8 + .5) * .00000001;
return number_format((float)$value, 0, '.', '');
}

function chipRound($value)
{
//    return round($value * 1e8);
//  return floor(($value) * 1e6 + .5) * .000001;
//  return round(($value) * 1e6 ) * .000001;
return number_format((float)$value, 0, '.', '');

}

function getBalance()
{
  $bitcoin = connectBitcoin();
  $bal = null;
  try {
    $bal = ($bitcoin->getbalance($_SESSION['username']))*CHIPSPERBTC;
  } catch (Exception $e) {
    return null;
  }
  return chipRound($bal);
}

function getFlooredBalance()
{
  return /*floor*/(getBalance());
//  return floor(getBalance();
}

/***********************************************************************************/

function SHA512_encode($str)
{
  return base64_encode(bin2hex(hash('sha512',$str)));
}

function validate_session()
{
    global $sessionsalt;

    if(!isset($_SESSION)) {
    session_start();}

    if (!isset($_SESSION['SERVER_GENERATED_SID']))
    {
      reset_session();
    }

    if(isset($_SESSION['G5X27CIAKGB']) && $_SESSION['G5X27CIAKGB'] !== SHA512_encode($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$sessionsalt))
    {
      reset_session();
    }

    ++$_SESSION['loads'];

    if(isset($_SESSION['username']))
    {
	$u = $_SESSION['username'];
	$s = session_id();

	$con = connectDB();
	if(!$con)
	{
	    reset_session();
	}
	else
	{
	    $ok = false;

	    if ($query = mysqli_query($con, "SELECT EXISTS(SELECT 1 FROM users WHERE username = '$u' AND session = '$s' LIMIT 1)"))
	    {    
		$result = mysqli_fetch_row($query);
		$ok =  (bool)($result[0]);
	    }
	    else
	    {
		$ok =  false;
	    }
	  
	    if(!$ok)
	    {
		reset_session();
	    }
	    else
	    {
		mysqli_query($con, "UPDATE users set lastactive=NOW() where username='$u'");
	    }
	}
    }
}
/******************************************************************************/



function allow_session()
{
  if(!isset($_SESSION['username']))
  {
    return false;
  }
  $user = $_SESSION['username'];
  $ses  = session_id();

  $con = connectDB();
  if(!$con)
  {
    return false;
  }

  $result = mysqli_query($con, "UPDATE users set session='$ses' where username='$user'");
   if(!$result) { return false; }

  return true;
}

function reset_session()
{
global $sessionsalt;
 /* if(isset($_COOKIE[session_name()]))
  {
    // Kill the cookie assocated to the session.
    if (ini_get("session.use_cookies"))
    {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]);
    }
  }

  session_start();
   //Destroy the session itself.
   session_regenerate_id();
   //session_destroy();
   unset($_SESSION);
   $_SESSION = array();
session_destroy();
   session_start();*/


if(!isset($_SESSION)) {
session_start();}
//session_regenerate_id();
//session_destroy();
unset($_SESSION);
session_destroy();
session_start();
session_regenerate_id();
   $_SESSION['SERVER_GENERATED_SID'] = true;

  $_SESSION['G5X27CIAKGB'] = SHA512_encode($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$sessionsalt);

  // Set number of loads to 0 in the session
  $_SESSION['loads'] = 0;


}


?>
