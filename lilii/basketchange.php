<?php
require_once 'user.php';
require_once 'link.php';
require_once 'category.php';
require_once 'basket.php';
require_once 'posit.php';
require_once 'login.php';
session_start();
$sesid = session_id();

$file = 'serialized/'.$sesid.'.txt';
$usr;
if (file_exists($file))
{
	$s = file_get_contents($file);
	$usr = unserialize($s);
}
else
{
	$dsn = "mysql:host=$db_hostname;dbname=$db_database;charset=utf8";
	$usr = new user($sesid, $dsn, $db_username, $db_password);
	$s = serialize($usr);
	file_put_contents($file,$s);
	$usr = unserialize($s);
}

if (isset($_POST['type']))
{
	//print_r($_POST);
	if ($_POST['type'] == 'undo')
		$usr->undo_from_basket($_POST['id']);
	else $usr->change_basket_posit($_POST['id'],$_POST['type'],$_POST['val']);
	echo 'success';
}
else echo 'no data';
?>