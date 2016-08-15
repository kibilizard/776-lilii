<?php
function startsession($pdo,&$usid,&$uslogin,&$uslog,$sesid)
{
	/*echo $sesid.' это функция из подключенного файла'.'<br />';*/
	$stmt = $pdo->prepare('SELECT id, login, logged FROM users WHERE sesid = ?');
	$stmt->execute(array($sesid));
	$row = $stmt->fetch();
	$usid = $row['id'];
	if (!$usid)
	{	
		$stmt = $pdo->prepare('INSERT INTO users (sesid) values (?)');
		$stmt->execute(array($sesid));
		$stmt = $pdo->prepare('SELECT id FROM users WHERE sesid = ?');
		$stmt->execute(array($sesid));
		$row = $stmt->fetch();
		$usid = $row['id'];
		$uslogin = '';
		$uslog = 'off';
		echo 'новый: '.$usid.' '.$uslog. ' '.$uslogin.'<br />';
	}
	else
	{
		$uslog = $row['logged'];
		if ($uslog == 'on')
		{
			$uslogin = $row['login'];
		}
		else {$uslogin = '';}
		echo $usid.' '.$uslog. ' '.$uslogin.' - вернул'.'<br />';	
		$stmt = $pdo->prepare('UPDATE users SET sestime = CURRENT_TIMESTAMP WHERE id = ?');
		$stmt->execute(array($usid));
	}
}
function login($pdo,&$usid,&$uslogin,&$uslog, $sesid)
{
	/*форма для ввода логина и пароля*/
	$log = '1234';
	$pas = '5678';
	$stmt = $pdo->prepare('SELECT id, password FROM users WHERE login = ?');
	$stmt->execute(array($log));
	$row = $stmt->fetch();
	if ($pas == $row['password'])
	{
		echo 'вошел! '.'<br />';
		$id_n = $row['id'];
		$uslog = 'on';
		$stmt = $pdo->prepare('UPDATE users SET sesid = ?, logged = ? WHERE id = ?');
		$vals = array($sesid,$uslog,$id_n);
		$stmt->execute($vals);
		echo 'обновил юзера! '.'<br />';
		$stmt = $pdo->prepare('UPDATE orders SET usid = ? WHERE usid = ?');
		$vals = array($id_n,$usid);
		$stmt->execute($vals);
		echo 'обновил заказы! '.'<br />';
		if ($usid!=$id_n)
		{
			$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
			$stmt->execute(array($usid));
			echo 'удалил юзера! '.'<br />';
			$usid = $id_n;
		}
		$uslogin = $log;
	}
	else
	{echo 'неправильный пароль'.'<br />';}
}
/*function regist($pdo,&$usid,&$uslogin,&$uslog, $sesid)
{
	/*форма для регистрации*/
	/*получаем переменную $usrinfo*/
	/*$usrinfo = array(
		'log' => '',
		'pas' => '',
		'ses' => $sesid,
		'logged' => 'on',
		'name' => '',
		'srname' => '',
		'lstname' => '',
		'addr' => '',
		'post' => '',
		'phone' => 0,
		'email' => '');
	$stmt = $pdo->prepare('INSERT INTO users (login, password, sesid, logged, name, surname, secondname, addr, postid, phone, email, regdate) 
							VALUES (:log, :pas, :ses, :logged, :name, :srname, :lstname, :addr, :post, :phone, :email, DATE())');
	$stmt->execute($usrinfo);
	$stmt = $pdo->prepare('SELECT id FROM users WHERE sesid = ? AND logged = ?');
	$stmt->execute(array($sesid, 'on'));
	$row = $stmt->fetch();
	$id_n = $row['id'];
	$stmt = $pdo->prepare('UPDATE orders SET usid = ? WHERE usid = ?');
	$vals = array($id_n,$usid);
	$stmt->execute($vals);
	$usid = $id_n;
	$uslogin = $usrinfo['log'];
	$uslog = 'on';
}*/
function logout($pdo,&$usid,&$uslogin,&$uslog, $sesid)
{
	$uslog = 'off';
	$stmt = $pdo->prepare('UPDATE users SET sesid = NULL, logged = ? WHERE id = ?');
	$vals = array($uslog,$usid);
	$stmt->execute($vals);
	$stmt = $pdo->prepare('INSERT INTO users (sesid) values (?)');
	$stmt->execute(array($sesid));
	$stmt = $pdo->prepare('SELECT id FROM users WHERE sesid = ?');
	$stmt->execute(array($sesid));
	$row = $stmt->fetch();
	$usid = $row['id'];
	$uslogin = '';
}
function test(&$testar)
{
	$testar = array (1,2,3,array('a','b','c'),5);
}
function getlib($pdo, $libid, &$sublibs, &$positions,&$poscount)
{
	$stmt = $pdo->prepare('SELECT downid, category.name, category.catdesc FROM sub JOIN category ON (downid = category.id) WHERE upid = ?');
	$stmt->execute(array($libid));
	$i = 0;
	while($row = $stmt->fetch())
	{
		$sublibs[$i] = array(
		'id' => $row['downid'],
		'name' => $row['name'],
		'desc' => $row['catdesc']);
		echo $i.'--'.$row['downid'].'<br />';
		$i++;
	}
	$stmt = $pdo->prepare('SELECT posid, position.posdesc, position.fotopath, position.cost, position.count FROM catpos JOIN position ON (posid = position.id) WHERE catid = ?');
	$stmt->execute(array($libid));
	$i = 0;
	while($row = $stmt->fetch())
	{
		$positions[$i] = array(
		'id' => $row['posid'],
		'desc' => $row['posdesc'],
		'fotopath' => $row['fotopath'],
		'cost' => $row['cost'],
		'count' => $row['count']);
		$i++;
	}
	$poscount = $i;
}
function openposition($pdo,$posid,&$sizes,&$colors)
{
	$stmt = $pdo->prepare('SELECT id, val, sdesc FROM size WHERE posid = ?');
	$stmt->execute(array($posid));
	$i = 0;
	while ($row = $stmt->fetch())
	{
		$sizes[] = array(
		'id' => $row['id'],
		'val' => $row['val'],
		'desc' => $row['sdesc']);
		$i++;
	}
	$stmt = $pdo->prepare('SELECT colid, fotoid, foto.path FROM pfc JOIN foto ON (fotoid = foto.id) WHERE posid = ? ORDER BY colid');
	$stmt->execute(array($posid));
	$i =-1;
	$color = '111';
	while ($row = $stmt->fetch())
	{
		if ($color != $row['colid'])
		{
			++$i;
			$colors[$i] = array(
			'color' => $row['colid'],
			'fotos' => array(array(
				'id' => $row['fotoid'],
				'path' =>$row['path'])));
			$color = $row['colid'];
		}
		else
		{
			$colors[$i]['fotos'][] = array(
				'id' => $row['fotoid'],
				'path' =>$row['path']);
		}
	}
}
function addtobasket($pdo,$usid,$posit) //передаем структуру: id,color,foto,size,count.
{
	$stmt = $pdo->prepare('INSERT INTO orders (usid, posid, colid, fid, sid, status, count) VALUES (?,?,?,?,?,?,?)');
	$val = array($usid,$posit['id'],$posit['color'],$posit['foto'],$posit['size'], 0, $posit['count']);
	$stmt->execute($val);
}
function getbasket($pdo,$usid)
{
	$stmt = $pdo->prepare('SELECT prodid, position.posdesc, position.cost, colid, foto.path, size.val, size.sdesc, orders.count FROM orders JOIN position ON (posid = position.id) JOIN foto ON (fid = foto.id) JOIN size ON (sid = size.id) WHERE usid = ? AND status < 1');
	$stmt->execute(array($usid));
	while ($row = $stmt->fetch())
	{
		echo $row['prodid'].' '.$row['posdesc'].' '.$row['cost'].' '.$row['colid'].' '.$row['path'].' '.$row['val'].' '.$row['sdesc'].' '.$row['count'].'<br />';
	} 
}
function undofrombasket($pdo,$posit)
{
	$stmt = $pdo->prepare('DELETE FROM orders WHERE prodid = ?');
	$stmt->execute(array($posit));
}
function changecount($pdo,$posit,$nc)
{
	$stmt = $pdo->prepare('UPDATE orders SET count = ? WHERE prodid = ?');
	$stmt->execute(array($nc,$posit));
}
/*function pay($pdo,&$log,&$usid,&$uslogin,$sesid,$ordids)
{
	$usrinfo;
	if($log == 'on') 
	{
		$stmt = $pdo->prepare('SELECT name, surname, secondname, addr, postid, phone, email FROM users WHERE id = ?');
		$stmt->execute(array($usid));
		$row = $stmt->fetch();
		$usrinfo = array(
			'name' => $row['name'],
			'surname' => $row['surname'],
			'lastname' => $row['secondname'],
			'addr' => $row['addr'],
			'postid' => $row['postid'],
			'phone' => $row['phone'],
			'email' => $row['email'],
			'id' => $usid);
		/*форма для оплаты*/
		/*загружаем новое инфо юзера*/
		/*$stmt = $pdo->prepare('UPDATE users SET name = :name, surname = :surname, secondname = :lastname, addr = :addr, postid = :postid, phone = :phone, email = :email WHERE id = :id');
		$stmt->execute($usrinfo);
	}
	else 
	{
		/*приглашаем войти или зарегаться, получаем ответ $ans*/
		/*if ($ans == 1)
		{
			login($pdo,$log,$usid,$uslogin,$sesid);
			$stmt = $pdo->prepare('SELECT name, surname, secondname, addr, postid, phone, email FROM users WHERE id = ?');
			$stmt->execute(array($usid));
			$row = $stmt->fetch();
			$usrinfo = array(
				'name' => $row['name'],
				'surname' => $row['surname'],
				'lastname' => $row['secondname'],
				'addr' => $row['addr'],
				'postid' => $row['postid'],
				'phone' => $row['phone'],
				'email' => $row['email'],
				'id' => $usid);
			/*форма для оплаты*/
			/*загружаем новое инфо юзера*/
			/*$stmt = $pdo->prepare('UPDATE users SET 
									name = :name, 
									surname = :surname, 
									secondname = :lastname, 
									addr = :addr, 
									postid = :postid, 
									phone = :phone, 
									email = :email 
									WHERE id = :id');
			$stmt->execute($usrinfo);
		}
		else if ($ans == 2)
		{
			regist($pdo,$log,$usid,$uslogin,$sesid);
			/*форма для оплаты*/
			/*загружаем новое инфо юзера*/
			/*$stmt = $pdo->prepare('UPDATE users SET 
									name = :name, 
									surname = :surname, 
									secondname = :lastname, 
									addr = :addr, 
									postid = :postid, 
									phone = :phone, 
									email = :email 
									WHERE id = :id');
			$stmt->execute($usrinfo);
		}
		else
		{
			$usrinfo = array(
				'login' => 'tmpusr',
				'pass' => 'tmppas',
				'name' => '',
				'surname' => '',
				'lastname' => '',
				'addr' => '',
				'postid' => '',
				'phone' => '',
				'email' => '',
				'id' => $usid);
			/*форма для оплаты*/
			/*загружаем новое инфо юзера*/
			/*$stmt = $pdo->prepare('UPDATE users SET 
									login = :login, 
									password = :pass, 
									name = :name, 
									surname = :surname, 
									secondname = :lastname, 
									addr = :addr, 
									postid = :postid, 
									phone = :phone, 
									email = :email 
									WHERE id = :id');
			$stmt->execute($usrinfo);
		}
	$stmt = $pdo->prepare('UPDATE orders SET status = 1 WHERE prodid = ?')
	foreach ($ordids as $d)
	{
		$stmt->execute(array($d));
	}		
}*/
?>