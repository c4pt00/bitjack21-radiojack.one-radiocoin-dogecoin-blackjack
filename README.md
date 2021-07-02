# bitjack21 - radiojack.one (radiocoin blackjack)
* could be a docker image but its not
 
* changed chips from a floating decimal only to a round number as a balance or playable balance or withdraw-able balance
* to do, change 1,5,10 chips to 10,50,100 via png edit
-> done * readd deposit by QR code 

bitjack21.com
```
code has pre-existing graphs in graph_profits.php
code uses pre-existing crude admin.php panel 
must change if user != c4pt to admin username for site

admin.php:if(!isset($_SESSION['username']) || ($_SESSION['username'] != 'c4pt' && $_SESSION['username'] != 'c4pt'))
admin.php:  $q1 = mysqli_query($con, "select sum(netGain)/-100 as gain from games where player != 'c4pt'");
```

game play logic and min / max bet in control.php
```
define('MAX_BET', 1000);
define('MIN_BET', 100);

```

requires radiocoin, mysql, httpd, phpmyadmin (or managing the database manually with mysql)
* https://github.com/c4pt000/radiocoin/releases/download/linux/radiocoin-6.0.1_radiocoin-2.x86_64.rpm

```
  yum install nano sudo wget git-core httpd ufw mysql-server mysql phpMyAdmin.noarch -y
  yum install radio*rpm -y
  rpm -e --nodeps firewalld firewalld-filesystem
  echo "IPV6=no" >> /etc/ufw/ufw.conf
  systemctl enable ufw
  ufw enable
  ufw allow in 80
  ufw allow out 53
  ufw allow out 80
  ufw allow out 443
  ufw allow out 9332
  ufw allow out 9333
  ufw allow out 3306
  ufw status verbose
  ufw status numbered
  ufw show listening
```

disable phpmyadmin (if not in use or safe guarded) will be visible and accessible from outside WAN
```
phpMyAdmin.conf goes in /etc/httpd/conf.d/
welcome.conf goes in /etc/httpd/conf.d/
radiocoin.conf goes in /root/.radiocoin/radiocoin.conf
fix-perm.sh to set permissions for /var/www/html
setenforce 0 (disable selinux, at your own risk)
```

```
install mariadb (formerly mysql) + httpd (and or phpmyadmin)
 systemctl start mariadb
 systemctl enable mariadb

systemctl enable httpd
systemctl start httpd
copy * to /var/www/html
sh fix-perm.sh
systemctl restart httpd
mysql_secure_installation 
create database "radiojack" or change database name accordingly in common.php
install database template
mysql -u root -p radiojack < bitjack21_DB.sql
```
navigate to 127.0.0.1/phpMyAdmin            where 127.0.0.1 is your WAN ip

use your own security guidelines this is just a template
set mysql connection in common.php change yourpassword-here to the root password (or user password) that can access "radiojack"
```
function connectDB()
{

$sql = new mysqli('localhost', 'root', 'yourpassword-here', 'radiojack');
 
if($sql->connect_errno > 0){
    die('Unable to connect to database [' . $sql->connect_error . ']');
}
 
$con = mysqli_connect("localhost","root","yourpassword-here","radiojack") or die("Some error occurred during connection " . mysqli_error($con));


  return $con;


}
```

also in common.php the radiocoin.conf connection (must match radiocoin.conf rpcuser/rpcpassword)
rpcallowip=127.0.0.1 in radiocoin.conf only allows connections from your local LAN (no WAN access) to safe guard rpc commands remotely
must also keep paytxfee=1.00 for a minimum to remove from the site wallet balance in order to be able to send
```
function connectBitcoin()
{


          $bitcoin = new jsonRPCClient('http://root:rpcpassword@127.0.0.1:9332/');

	  return $bitcoin;
}
```

![s1](https://raw.githubusercontent.com/c4pt000/bitjack21-radiojack.one-radiocoin-blackjack/master/radiojack-deposit.png)
![s1](https://raw.githubusercontent.com/c4pt000/bitjack21-radiojack.one-radiocoin-blackjack/master/radiojack-deposit-detect1.png)
![s1](https://raw.githubusercontent.com/c4pt000/bitjack21-radiojack.one-radiocoin-blackjack/master/radiojack-sent-manual-withdrawl.png)
![s1](https://raw.githubusercontent.com/c4pt000/bitjack21-radiojack.one-radiocoin-blackjack/master/radiojack-in-game-1.png)
![s1](https://raw.githubusercontent.com/c4pt000/bitjack21-radiojack.one-radiocoin-blackjack/master/radiojack-in-game-2.png)
