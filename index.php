<?php
require_once 'common.php';
validate_session();
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--<meta http-equiv="refresh" content="3" /> uncoment this for auto refresh-->
<meta name="keywords" content="radiocoin blackjack, radiocoin, blackjack, radiocoin casino, radiocoin game, RADC blackjack, RADC, radiocoin game, radiocoin gambling" />
<meta name="description" content="radiojack.one - Radiocoin Blackjack" /> 
<link rel="Shortcut Icon" href="images/favicon.ico">
<title>RadioJack.one - Radiocoin Blackjack</title> 
<base target="_self" />
	
<!--Stylesheets-->
<link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" charset="utf-8"> 

<!--Javascript--> 

	<!--[if IE]>
		<script src="js/html5.js"></script>
	<![endif]-->

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/jquery.backgroundPosition.js_6.js"></script>
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript">
$(function() {
  $('#loginform').submit(dologin);
});

function dologin()
{
    $('#logmein').hide();
    $('#result').empty();
    $.post("/login.php", { "username":$('#u').val(), "password":$('#p').val()}, updateState,"json");
    return false;
}

function updateState(data)
{
  if(data.error == 0)
  {
    $('#result').css('font-size','110%').css('color','rgb(255,0,0)').html("Logging in...");
    window.location="http://radiojack.one/";
  }
  else
  {
    $('#result').css('font-size','110%').css('color','rgb(255,0,0)').html(data.error);
    $('#logmein').show();
    setTimeout(function(){
	$('#result').html("");
    }, 1200);
  }
}
</script>

</head>
<body>
<div id="wrapper"> 
<div id="header"></div>
<div id="menubar"><?php drawmenu(); ?></div>

<div id="columns">
<div id="side1">


<?php
if(isset($_SESSION['username']))
{
  $fail = 0;
  $con = connectDB();
  if (!$con)
  {
    $fail = 1;
  }
  $result = mysqli_query($con, "SELECT * from users where username = '".$_SESSION['username']."'");

  if(mysqli_num_rows($result) ==  1)
  {
    $myuser = mysqli_fetch_array($result);
  }  
  else
  {
    $fail = 1;
  }
  if($fail == 1)
  {
    echo "<span>Temporary Error: Database is down</span>";
  }
  else
  {
    $fail = 0;

    $btcbal = getBTCBalance();

//    $RADCbal = getRADCBalance();
    $chipbal = getBalance();
    if(is_null($btcbal) || is_null($chipbal))
    {
       echo "<span>Temporary Error: bitcoin daemon is down</span>";
    }
    else
    {
      echo '<table border="1" style="margin-left:auto;margin-right:auto;">';
      echo '<tr><td>Username</td><td>'.$myuser['username'].'</td></tr>';
      echo '<tr><td>Balance (RADC)</td><td>'.getBTCBalance().' RADC</td></tr>';
      echo '<tr><td>Balance (chips)</td><td>'.getBalance().' chips</td></tr>';
      echo '<tr><td>Deposit Address</td><td>'.$myuser['deposit'].'</td></tr></table>';
    }
  }
}
else
{
  $login_html = <<<TEST
Please login:<br>
<form id="loginform">
<table>
<tr><td>Username:</td><td><input id="u" type="text" size="20" name="username"></td></tr>
<tr><td>Password:</td><td><input id="p" type="password" size="20" name="password"></td></tr>
</table><input type="submit" value="Login"></form>
<br>
<span id="result" ></span>
TEST;
  echo $login_html;
}

?>
<div id="side2">
<h1>Welcome to RadioJack.one !</h1><br>

The "house edge" with this ruleset is razor thin, less than 0.5%, enjoy :)
<br><br>

</div>
</div>

<div id="footer"><span>&copy; 2011 original code by Mr. Sizlak</span></div>
</div>

</body>
</html>
