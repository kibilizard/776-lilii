<?php
	$db_hostname = 'localhost';
	$db_database = '***';
	$dsn = "mysql:host=$db_hostname;dbname=$db_database;charset=utf8";
	$opt = array(
		PDO::ATTR_ERRMODE 			 => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
    $pdo = new PDO($dsn, '****', '*****', $opt);
	
	$arr = $_POST['info'];
	$n = count($_POST['info']);
	
	$stmt = $pdo->prepare('UPDATE positions SET style = ?, cost = ?, colors = ? WHERE id = ?');
	for ($i = 0; $i<$n; $i++)
	{
		if ($arr[$i] != '')
		{
			$stmt->execute(array($arr[$i]['css'],(int)$arr[$i]['cst'],$arr[$i]['col'],$i));
			$fot = $arr[$i]['fot'];
			$fn = count($fot);
			$stm2 = $pdo->prepare('UPDATE fotos SET style = ? WHERE id = ?');
			for ($j=0; $j<$fn; $j++)
			{
				if($fot[$j] != '')
				{
					$stm2->execute(array($fot[$j]['css'],$j));
				}
			}
			$siz = $arr[$i]['siz'];
			$sn = count($siz);
			$stm3 = $pdo->prepare('UPDATE sizes SET value = ?, descript = ? WHERE id = ?');
			for ($k=0; $k<$sn; $k++)
			{
				if($siz[$k] != '')
				{
					$stm3->execute(array($siz[$k]['val'],$siz[$k]['desc'],$k));
				}
			}
		}
	}
	echo 'sucess';
?>