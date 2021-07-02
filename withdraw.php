<?php
require_once 'common.php';
validate_session();
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--<meta http-equiv="refresh" content="3" /> uncoment this for auto refresh-->
	<meta name="keywords" content="blackjack, radiocoin" />
	<meta name="description" content="RadioJack - Radiocoin Blackjack - Withdraw" /> 
	<link rel="Shortcut Icon" href="images/favicon.ico">
	<title>RadioJack - Radiocoin Blackjack - Withdraw</title> 
	<base target="_self" />
	
<!--Stylesheets--> 
	<link href="css/layout.css" rel="stylesheet" type="text/css" media="screen" /> 
	
<!--Javascript--> 

	<!--[if IE]>
		<script src="js/html5.js"></script>
	<![endif]-->

<?php
if(!isset($_SESSION['username']))
{
  echo <<<DONE
<title>Logging Out</title>
<meta http-equiv="REFRESH" content="1;url=index"></HEAD>
<BODY>
Your session has ended, logging out...
</BODY>
</HTML>
DONE;
exit();
}
?>


<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/jquery.backgroundPosition.js_6.js"></script>
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript">

$(function() {
  $('#sendcoins').click(function() {
    $('#sendcoins').hide();
    $('#result').empty();
    $.post("wd.php", { "password":$('#pw').val(), "amount":$('#amt').val(), "wallet":$('#wallet').val()}, updateState,"json");
  });
});

function updateState(data)
{
  if(data.error == 0)
  {
    $('#result').css('font-size','160%').css('color','rgb(255,0,0)').html("Coins sent!<br>Transaction ID: "+data.txid);
    $('#bal').fadeOut('fast').html('Refresh to see').fadeIn('fast');
    $('#sendcoins').show();
  }
  else if(data.error == 1)
  {
    $('#result').css('font-size','160%').css('color','rgb(255,0,0)').html("Your withdrawl has been queued and will complete within 24 hours.<br><br>Most withdrawals are immediate, but due to security reasons only a limited number of radiocoins are kept on the server.  The amount of your withdrawal exceeds the number of radiocoins currently available on the server.  Your transaction will be manually processed within 24 hours.  Thank you.");
    $('#bal').fadeOut('fast').html('Refresh to see').fadeIn('fast');
    $('#sendcoins').show();
  }
  else if(data.error == 2)
  {
    $('#result').css('font-size','160%').css('color','rgb(255,0,0)').html("Password incorrect.");
    $('#sendcoins').show();
    setTimeout(function(){
      $('#result').empty();
    }, 1300);
  }
  else
  {
    $('#result').css('font-size','160%').css('color','rgb(255,0,0)').html(data.error);
    $('#sendcoins').show();
  }
}


</script>

</head>
 
<body> 
<div id="wrapper">
<div id="header"></div>
<div id="menubar"><?php drawmenu(); ?></div>
<div id="singlecolumn">


<?php

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
  echo <<<DONE
Temporary error: Database is down (-494)
</div>
</div>
</BODY>
</HTML>
DONE;
exit();
  }
  else
  {
    $mybal = getBTCBalance();
    if(is_null($mybal))
    {
      echo <<<DONE2
Temporary error: bitcoin daemon is down (-4943)
</div>
</div>
</body>
</html>
DONE2;
    }
    echo '<br><br><p style="text-align:center">To deposit radiocoins, send them to your deposit address listed below.  One confirmation is required before the radiocoins will appear in your account.</p>';
    echo '<table border="1" style="text-align:left;margin-left:auto;margin-right:auto;">';
    echo '<tr><td>Username</td><td>'.$myuser['username'].'</td></tr>';
    echo '<tr><td>Balance (RADC)</td><td id="bal">'.$mybal.'</td></tr>';
    echo '<tr><td>Deposit Address</td><td>'.$myuser['deposit'].'</td></tr></table>';
      echo '<h4>QR code deposit address</h4>';
      echo '<img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$myuser['deposit'].'" title="deposit" />';



  }
?>

<p>Withdraw radiocoins:</p>
<p>NOTE: There is a 1.00 RADC transaction fee for all withdraws.</p>
<table style="text-align:left;margin-left:auto;margin-right:auto;">
<tr><td>Password:</td><td><input id="pw" type="password" size="40" name="password"></td></tr>
<tr><td>Amount to withdraw (RADC):</td><td><input id="amt" type="text" size="40" name="btcamount"></td></tr>
<tr><td>Radiocoin address to send coins to:</td><td><input id="wallet" type="text" size="40" name="btcaddress"></td></tr>
</table>
<div style="text-align: center;"><br><button id="sendcoins" type="button">Send Coins</button><br><br>
<span id="result" ></span></div>
</div>

</div>

</body>

</html>
