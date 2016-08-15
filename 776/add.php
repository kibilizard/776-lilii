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
	$sarr = array(
  "XS" => 0,
  "S" => 1,
  "M" => 2,
  "L" => 3,
  "XL" => 4,
  "XXL" => 5,
  "XXXL" => 6,
  "ONE SIZE" => 7);
  
	switch ($arr['type'])
	{
		case 'pos':{
			$stmt = $pdo->prepare('INSERT INTO positions(foto,cost,style,colors,crdate) values (?,?,?,?,NOW())');
			$stmt->execute(array($arr['inf']['path'],(int)$arr['inf']['cost'],$arr['stl'],$arr['col']));
			$stmt = $pdo->prepare('SELECT id FROM positions WHERE foto = ? AND cost = ?');
			$stmt->execute(array($arr['inf']['path'],(int)$arr['inf']['cost']));
			$row = $stmt->fetch();
			foreach($arr['siz'] as $s)
			{
				$stmt = $pdo->prepare('INSERT INTO sizes(posid, value, orderid, descript) VALUES (?,?,?,?)');
              $stmt->execute(array($row['id'],$s['val'],$sarr[$s['val']],$s['desc']));
			}
			foreach($arr['fot'] as $f)
			{
				$stmt = $pdo->prepare('INSERT INTO fotos(posid, path, style) VALUES (?,?,?)');
				$stmt->execute(array($row['id'],$f,$arr['stl']));
			}
			break;}
		case 'siz':{
			$stmt = $pdo->prepare('INSERT INTO sizes(posid, value, orderid, descript) VALUES (?,?,?,?)');
			$stmt->execute(array($arr['pid'],$arr['val'],$sarr[$arr['val']],$arr['dsc']));
			break;}
		case 'fot':{
          $s=explode("/",$arr['path']);
			$n=count($s);
			for($i=0;$i<$n;$i++)
			{
				if ($s[$i]=='uploads')
					$path=$s[$i].'/'.$s[$i+1];
				echo $s[$i].' ';
			}
			$stmt = $pdo->prepare('INSERT INTO fotos(posid, path) VALUES (?,?)');
			$stmt->execute(array($arr['pid'],$path));
			break;}
	}
	echo 'sucess';
?>