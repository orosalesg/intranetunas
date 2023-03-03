<?php
$username = "root";
$password = "";
$hostname = "localhost";

//connection to the database
/*$dbhandle = new mysqli($hostname, $username, $password,"unassalo_intranet") 
 or die("Unable to connect to MySQL");*/

$dsn = "mysql:host={$hostname};port=3306;dbname=unassalo_intranet;charset=utf8";

$dbhandle =  new PDO($dsn, $username, $password, [
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_STRINGIFY_FETCHES => false
]);

//select a database to work with
/*$selected = mysql_select_db("unassalo_intranet",$dbhandle) 
  or die("Could not select DB");*/

//close the connection
//mysql_close($dbhandle);
?>