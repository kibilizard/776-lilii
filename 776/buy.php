<?php
	$db_hostname = 'localhost';
	$db_database = 'ck74682_776';
	$dsn = "mysql:host=$db_hostname;dbname=$db_database;charset=utf8";
	$opt = array(
		PDO::ATTR_ERRMODE 			 => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
    $pdo = new PDO($dsn, 'ck74682_776', 'fynjy776', $opt);
	
	$arr = $_POST;
	$stmt = $pdo->prepare('INSERT INTO `orders`(`fio`, `adr`, `city`, `region`, `pindex`, `country`, `phone`, `email`, `summ`, `prid`, `sid`,`status`,`color`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,0)');
$stmt->execute(array($arr['fio'],$arr['adr'],$arr['cyt'],$arr['reg'],$arr['ind'],$arr['cnt'],$arr['phn'],$arr['eml'],(int)$arr['cst'],(int)$arr['prd'],(int)$arr['siz'],$arr['col']));
$stmt = $pdo->prepare('SELECT id FROM orders WHERE fio = ? AND summ = ? AND prid = ? AND sid = ? AND status = 0');
$stmt->execute(array($arr['fio'],(int)$arr['cst'],(int)$arr['prd'],(int)$arr['siz']));
$row = $stmt->fetch();
$oid = $row['id'];
echo $oid;
?>