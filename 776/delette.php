<?php
	$db_hostname = 'localhost';
	$db_database = '***';
	$dsn = "mysql:host=$db_hostname;dbname=$db_database;charset=utf8";
	$opt = array(
		PDO::ATTR_ERRMODE 			 => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
    $pdo = new PDO($dsn, '****', '*****', $opt);
	
	$arr = $_POST;
	switch ($arr['type'])
	{
		case 'pos':{
			$stmt = $pdo->prepare('DELETE FROM sizes WHERE posid = ?');
			$stmt->execute(array($arr['id']));
			$stmt = $pdo->prepare('DELETE FROM fotos WHERE posid = ?');
			$stmt->execute(array($arr['id']));
			$stmt = $pdo->prepare('DELETE FROM oplink WHERE positid = ?');
			$stmt->execute(array($arr['id']));
			$stmt = $pdo->prepare('DELETE FROM positions WHERE id = ?');
			$stmt->execute(array($arr['id']));
			break;}
		case 'siz':{
			$stmt = $pdo->prepare('DELETE FROM sizes WHERE id = ?');
			$stmt->execute(array($arr['id']));
			break;}
		case 'fot':{
			$stmt = $pdo->prepare('DELETE FROM fotos WHERE id = ?');
			$stmt->execute(array($arr['id']));
			break;}
	}
	echo 'sucess';
?>