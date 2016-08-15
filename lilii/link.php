<?php

require_once 'category.php';
require_once 'basket.php';
require_once 'posit.php';
class link
{
    private $pdo;
    private $dsn, $username, $password;
	private $stmt;
    
    public function __construct($dsn, $username, $password)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->connect();
		$this->stmt = NULL;
    }
    
    private function connect()
    {
		$opt = array(
			PDO::ATTR_ERRMODE 			 => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			);
        $this->pdo = new PDO($this->dsn, $this->username, $this->password, $opt);
    }
    
    public function __sleep()
    {
		$this->pdo = NULL;
		$this->stmt = NULL;
        return array('dsn', 'username', 'password');
    }
    
    public function __wakeup()
    {
        $this->connect();
    }
	
	protected function start(&$usid,&$uslogin,&$uslog,&$usinfo, $sesid)
	{
		/*echo $sesid.' это функция из подключенного файла'.'<br />';*/
		$this->stmt = $this->pdo->prepare('SELECT id, login, logged, name, surname, secondname, addr, postid, phone, email, regdate FROM users WHERE sesid = ?');
		$this->stmt->execute(array($sesid));
		$row = $this->stmt->fetch();
		$usid = $row['id'];
		if (!$usid)
		{	
			$this->stmt = $this->pdo->prepare('INSERT INTO users (sesid) values (?)');
			$this->stmt->execute(array($sesid));
			$this->stmt = $this->pdo->prepare('SELECT id FROM users WHERE sesid = ?');
			$this->stmt->execute(array($sesid));
			$row = $this->stmt->fetch();
			$usid = $row['id'];
			$uslogin = '';
			$uslog = 'off';
			$usinfo = array();
		}
		else
		{
			$uslog = $row['logged'];
			if ($uslog == 'on')
			{
				$uslogin = $row['login'];
				$usinfo = array(
					'name' => $row['name'],
					'surname' => $row['surname'],
					'lastname' => $row['secondname'],
					'addr' => $row['addr'],
					'post' => $row['postid'],
					'phone' => $row['phone'],
					'email' => $row['email'],
					'rdate' => $row['regdate']);
			}
			else 
			{
				$uslogin = '';
				$usinfo = array();
			}	
			$this->stmt = $this->pdo->prepare('UPDATE users SET sestime = CURRENT_TIMESTAMP WHERE id = ?');
			$this->stmt->execute(array($usid));
		}
	}
	
	protected function login(&$usid,&$uslogin,&$uslog,&$usinfo,$sesid)
	{
		/*форма для ввода логина и пароля*/
		$log = '1234';
		$pas = '5678';
		$this->stmt = $this->pdo->prepare('SELECT id, password, name, surname, secondname, addr, postid, phone, email, regdate FROM users WHERE login = ?');
		$this->stmt->execute(array($log));
		$row = $this->stmt->fetch();
		if ($pas == $row['password'])
		{
			$id_n = $row['id'];
			$usinfo = array(
				'name' => $row['name'],
				'surname' => $row['surname'],
				'lastname' => $row['secondname'],
				'addr' => $row['addr'],
				'post' => $row['postid'],
				'phone' => $row['phone'],
				'email' => $row['email'],
				'rdate' => $row['regdate']);
			$uslog = 'on';
			$this->stmt = $this->pdo->prepare('UPDATE users SET sesid = ?, logged = ? WHERE id = ?');
			$vals = array($sesid,$uslog,$id_n);
			$this->stmt->execute($vals);
			$this->stmt = $pdo->prepare('UPDATE orders SET usid = ? WHERE usid = ?');
			$vals = array($id_n,$usid);
			$this->stmt->execute($vals);;
			if ($usid!=$id_n)
			{
				$this->stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
				$this->stmt->execute(array($usid));
				$usid = $id_n;
			}
			$uslogin = $log;
		}
		else
		{echo 'неправильный пароль'.'<br />';}
	}
	
	protected function regist(&$usid,&$uslogin,&$uslog,&$usinfo,$sesid)
	{
		echo 'в разработке <br />';
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
		$this->stmt = $this->pdo->prepare('INSERT INTO users (login, password, sesid, logged, name, surname, secondname, addr, postid, phone, email, regdate) 
								VALUES (:log, :pas, :ses, :logged, :name, :srname, :lstname, :addr, :post, :phone, :email, DATE())');
		$this->stmt->execute($usrinfo);
		$this->stmt = $this->pdo->prepare('SELECT id FROM users WHERE sesid = ? AND logged = ?');
		$this->stmt->execute(array($sesid, 'on'));
		$row = $this->stmt->fetch();
		$id_n = $row['id'];
		$this->stmt = $this->pdo->prepare('UPDATE orders SET usid = ? WHERE usid = ?');
		$vals = array($id_n,$usid);
		$this->stmt->execute($vals);
		$usid = $id_n;
		$uslogin = $usrinfo['log'];
		$uslog = 'on';*/
	}
	
	protected function logout(&$usid,&$uslogin,&$uslog,&$usinfo,$sesid)
	{
		$uslog = 'off';
		$this->stmt = $this->pdo->prepare('UPDATE users SET sesid = NULL, logged = ? WHERE id = ?');
		$vals = array($uslog,$usid);
		$this->stmt->execute($vals);
		$this->stmt = $this->pdo->prepare('INSERT INTO users (sesid) values (?)');
		$this->stmt->execute(array($sesid));
		$this->stmt = $this->pdo->prepare('SELECT id FROM users WHERE sesid = ?');
		$this->stmt->execute(array($sesid));
		$row = $this->stmt->fetch();
		$usid = $row['id'];
		$uslogin = '';
		$usinfo = array();
	}
	
	protected function getlib(&$lib, $id)
	{
		$lib = array();
		$stm = $this->pdo->prepare('SELECT downid, category.name, category.catdesc FROM sub JOIN category ON (downid = category.id) WHERE upid = ?');
		if ($stm->execute(array($id)))
		{
			if ($id == 1)
			{
				$open = 1;
			}
			else
			{
				$open = 0;
			}
			$lib = array();
			while($row = $stm->fetch())
			{
				$tmp = array();
				$this->getlib($tmp,$row['downid']);
				$lib[] = array(
				'id' => $row['downid'],
				'name' => $row['name'],
				'desc' => $row['catdesc'],
				'open' => $open,
				'sub' => $tmp);
			}
		}
	}
	
	protected function getcategory(&$category, $libid, $libdescription)
	{
		$this->stmt = $this->pdo->prepare('SELECT posid, position.name, position.fotopath, position.cost, position.pdesc, position.compos
											FROM catpos JOIN position ON (posid = position.id) 
											WHERE catid = ?');
		if (!$this->stmt->execute(array($libid)))
		{
			$category = new category(0);
		}
		else
		{
			$positions = array();
			$i = 0;
			while($row = $this->stmt->fetch())
			{
				$positions[$i] = array(
				'id' => $row['posid'],
				'name' => $row['name'],
				'foto' => $row['fotopath'],
				'cost' => $row['cost'],
				'desc' => $row['pdesc'],
				'compos' => $row['compos'],
				'care' => array(),
				'compable' =>array());
				$i++;
			}
			$poscount = $i;
			$tmpproducts = array();
			foreach ($positions as &$pos)
			{
				$this->stmt = $this->pdo->prepare('SELECT compid, compable.color, color.colname, cpath, compable.foto, position.name, position.pdesc, position.cost 
													FROM compable 
													JOIN position ON (compid = position.id) 
													JOIN color ON (compable.color = color.val) 
													WHERE mainid = ?');
				$this->stmt->execute(array($pos['id']));
				while ($row = $this->stmt->fetch())
				{
					$stm2 = $this->pdo->prepare('SELECT id, val, sdesc FROM size WHERE posid = ?');
					$stm2->execute(array($row['compid']));
					$s = array();
					while ($r1 = $stm2->fetch())
					{
						$s[] = array(
						'id' => $r1['id'],
						'val' => $r1['val'],
						'desc' => $r1['sdesc']);
					}
					$pos['compable'][] = array(
					'id' => $row['compid'],
					'path' => $row['cpath'],
					'foto' => $row['foto'],
					'color' => $row['color'],
					'cname' => $row['colname'],
					'name' => $row['name'],
					'desc' => $row['pdesc'],
					'cost' => $row['cost'],
					'sises'  => $s);
				}
				$this->stmt = $this->pdo->prepare('SELECT care.foto, care.type, care.caredesc FROM poscare JOIN care ON (careid = care.id) WHERE posid = ?');
				$this->stmt->execute(array($pos['id']));
				while ($row = $this->stmt->fetch())
				{
					$pos['care'][] = array(
					'foto' => $row['foto'],
					'type' => $row['type'],
					'desc' => $row['caredesc']);
				}
				$tmps = array();
				$this->stmt = $this->pdo->prepare('SELECT id, val, sdesc FROM size WHERE posid = ?');
				$this->stmt->execute(array($pos['id']));
				while ($row = $this->stmt->fetch())
				{
					$tmps[] = array(
					'id' => $row['id'],
					'size' => $row['val'],
					'desc' => $row['sdesc']);
				}
				$tmpc = array();
				$this->stmt = $this->pdo->prepare('SELECT pfc.id, colid, color.colname, fotoid, foto.path, main, mcol FROM pfc JOIN foto ON (fotoid = foto.id) JOIN color ON (colid = color.val) WHERE posid = ? ORDER BY colid');
				$this->stmt->execute(array($pos['id']));
				$color = '111';
				$i = -1;
				while ($row = $this->stmt->fetch())
				{
					$main = 0;
					$mcol = 0;
					if ($color != $row['colid'])
					{
						if ($row['mcol'])
							$mcol = 1;
						if ($row['main'])
							$main = $row['path'];
						++$i;
						$tmpc[$i] = array(
						'id' => $i,
						'val' => $row['colid'],
						'name' => $row['colname'],
						'main' => $main,
						'mcol' => $mcol,
						'fotos' => array(array(
								'id' => $row['fotoid'],
								'pfcid' => $row['id'],
								'path' => $row['path'])));
						$color = $row['colid'];
					}
					else
					{
						if ($row['main'])
							$tmpc[$i]['main'] = $row['path'];
						$tmpc[$i]['fotos'][] = array(
								'id' => $row['fotoid'],
								'pfcid' => $row['id'],
								'path' => $row['path']);
					}
				}
				$tmpcount = array();
				$this->stmt = $this->pdo->prepare('SELECT count FROM countlink WHERE size = ? AND color = ?');
				foreach ($tmps as $s)
				{
					foreach($tmpc as $x)
					{
						foreach($x['fotos'] as $c)
						{
							if ($this->stmt->execute(array($s['id'],$c['pfcid'])))
							{
								$row = $this->stmt->fetch();
								$tmpcount[] = array(
								'sid' => $s['id'],
								'cid' => $c['pfcid'],
								'count' => $row['count']);
								break;
							}
						}
					}
				}
				$src = array(
				'info' => $pos,
				'sizes' => $tmps,
				'colors' => $tmpc,
				'counts' => $tmpcount);
				$tmpproducts[] = new product($src);
			}
			$src = array(
			'id' => $libid,
			'desc' => $libdescription,
			'prod' => $tmpproducts,
			'count' => $poscount);
			$category = new category($src);
		}
	}
	
	/*protected function openposition($posid,&$sizes,&$colors)
	{
		$this->stmt = $this->pdo->prepare('SELECT id, val, sdesc FROM size WHERE posid = ?');
		$this->stmt->execute(array($posid));
		$i = 0;
		while ($row = $this->stmt->fetch())
		{
			$sizes[] = array(
			'id' => $row['id'],
			'val' => $row['val'],
			'desc' => $row['sdesc']);
			$i++;
		}
		$this->stmt = $this->pdo->prepare('SELECT !!!!pfc.id!!!!!colid, fotoid, foto.path FROM pfc JOIN foto ON (fotoid = foto.id) WHERE posid = ? ORDER BY colid');
		$this->stmt->execute(array($posid));
		$i =-1;
		$color = '111';
		while ($row = $this->stmt->fetch())
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
	}*/
	
	protected function addtobasket($usid,&$bask,$posit,$cpath) //передаем структуру: ['id']['pfcid']['color']['fid']['size']['count']. из класса category
	{
		$this->stmt = $this->pdo->prepare('INSERT INTO orders (usid, posid, pfcid, colid, fid, sid, status, count, cpath) VALUES (?,?,?,?,?,?,?,?,?)');
		$val = array($usid,$posit['id'],$posit['pfcid'],$posit['color'],$posit['fid'],$posit['size'], 0, $posit['count'],$cpath);
		$this->stmt->execute($val);
		$this->stmt = $this->pdo->prepare('SELECT orderid, position.name, position.cost, color.colname, foto.path, size.val, size.sdesc, orders.count 
											FROM orders JOIN position ON (posid = position.id) 
											JOIN color ON (colid = color.val)
											JOIN foto ON (fid = foto.id) 
											JOIN size ON (sid = size.id) 
											WHERE usid = ? AND orders.posid = ? AND colid = ? AND sid = ? AND fid = ?');
		$this->stmt->execute(array($usid,$posit['id'],$posit['color'],$posit['size'],$posit['fid']));
		$row = $this->stmt->fetch();
		$pos = array(
			'id' => $row['orderid'],
			'pid' => $posit['id'],
			'desc' => $row['name'],
			'cost' => $row['cost'],
			'pfcid' => $posit['pfcid'],
			'color' => $row['colname'],
			'foto' => $row['path'],
			'sid' => $posit['size'],
			'size' => $row['val'],
			'sdesc' => $row['sdesc'],
			'count' => $row['count']);
		$tmps = array();
		$this->stmt = $this->pdo->prepare('SELECT id, val, sdesc FROM size WHERE posid = ?');
		$this->stmt->execute(array($pos['pid']));
		while ($row = $this->stmt->fetch())
		{
			$tmps[] = array(
			'id' => $row['id'],
			'size' => $row['val'],
			'sdesc' => $row['sdesc']);
		}
		$tmpc = array();
		$this->stmt = $this->pdo->prepare('SELECT pfc.id, colid, color.colname, fotoid, foto.path FROM pfc JOIN foto ON (fotoid = foto.id) JOIN color ON (colid = color.val) WHERE posid = ? ORDER BY colid');
		$this->stmt->execute(array($pos['pid']));
		$color = '111';
		while ($row = $this->stmt->fetch())
		{
			if ($color != $row['colid'])
			{
				$tmpc[] = array(
				'id' => $row['id'],
				'color' => $row['colid'],
				'name' => $row['colname'],
				'fid' => $row['fotoid'],
				'foto' => $row['path']);
				$color = $row['colid'];
			}
		}
		$tmpcount = array();
		$this->stmt = $this->pdo->prepare('SELECT count FROM countlink WHERE size = ? AND color = ?');
		foreach ($tmps as $s)
		{
			foreach($tmpc as $c)
			{
				if ($this->stmt->execute(array($s['id'],$c['id'])))
				{
					$row = $this->stmt->fetch();
					$tmpcount[] = array(
					'sid' => $s['id'],
					'cid' => $c['id'],
					'count' => $row['count']);
				}
			}
		}
		$src = array(
		'pos' => $pos,
		'sizes' => $tmps,
		'colors' => $tmpc,
		'maxcounts' => $tmpcount);
		$bask->addposit($src);
	}
	
	protected function repeatadd($ordid)
	{
		$this->stmt = $this->pdo->prepare('SELECT count FROM orders WHERE orderid = ?');
		$this->stmt->execute(array($ordid));
		$row = $this->stmt->fetch();
		$this->stmt = $this->pdo->prepare('UPDATE orders SET count=? WHERE orderid = ?');
		$this->stmt->execute(array(($row['count']+1),$ordid));
	}
	
	protected function getbasket($usid,&$bask)
	{
		$pos = array();
		$this->stmt = $this->pdo->prepare('SELECT orderid, orders.posid, position.name, position.cost, pfcid, colid, color.colname, foto.path, size.id, size.val, size.sdesc, orders.count, orders.cpath
											FROM orders JOIN position ON (posid = position.id) 
											JOIN color ON (colid = color.val)
											JOIN foto ON (fid = foto.id) 
											JOIN size ON (sid = size.id) 
											WHERE usid = ? AND status < 1');
		if($this->stmt->execute(array($usid)))
		{
			$i = 0;
			$sum = 0;
			while ($row = $this->stmt->fetch())
			{
				$pos[$i] = array(
				'id' => $row['orderid'],
				'pid' => $row['posid'],
				'desc' => $row['name'],
				'cost' => $row['cost'],
				'pfcid' => $row['pfcid'],
				'colid' => $row['colid'],
				'color' => $row['colname'],
				'foto' => $row['path'],
				'sid' => $row['id'],
				'size' => $row['val'],
				'sdesc' => $row['sdesc'],
				'count' => $row['count'],
				'cpath' => $row['cpath']);
				$i++;
				$sum = $sum + $row['cost']*$row['count'];
			}
			$positons = array();
			foreach ($pos as $p)
			{
				$tmps = array();
				$this->stmt = $this->pdo->prepare('SELECT id, val, sdesc FROM size WHERE posid = ?');
				$this->stmt->execute(array($p['pid']));
				while ($row = $this->stmt->fetch())
				{
					$tmps[] = array(
					'id' => $row['id'],
					'size' => $row['val'],
					'sdesc' => $row['sdesc']);
				}
				$tmpc = array();
				$this->stmt = $this->pdo->prepare('SELECT pfc.id, colid, color.colname, fotoid, foto.path FROM pfc JOIN foto ON (fotoid = foto.id) JOIN color ON (colid = color.val) WHERE posid = ? ORDER BY colid');
				$this->stmt->execute(array($p['pid']));
				$color = '111';
				while ($row = $this->stmt->fetch())
				{
					if ($color != $row['colid'])
					{
						$tmpc[] = array(
						'id' => $row['id'],
						'color' => $row['colid'],
						'name' => $row['colname'],
						'fid' => $row['fotoid'],
						'foto' => $row['path']);
						$color = $row['colid'];
					}
				}
				$tmpcount = array();
				$this->stmt = $this->pdo->prepare('SELECT count FROM countlink WHERE size = ? AND color = ?');
				foreach ($tmps as $s)
				{
					foreach($tmpc as $c)
					{
						if ($this->stmt->execute(array($s['id'],$c['id'])))
						{
							$row = $this->stmt->fetch();
							$tmpcount[] = array(
							'sid' => $s['id'],
							'cid' => $c['id'],
							'count' => $row['count']);
						}
					}
				}
				$source = array(
				'pos' => $p,
				'sizes' => $tmps,
				'colors' => $tmpc,
				'maxcounts' => $tmpcount);
				$positons[] = new posit($source);
			}
			$source = array(
			'positions' => $positons,
			'count' => $i,
			'sum' => $sum);
			$bask = new basket($source);
		}
		else
		{
			$bask = new basket(0);
		}
	}
	
	protected function undofrombasket($prod)
	{
		$this->stmt = $this->pdo->prepare('DELETE FROM orders WHERE orderid = ?');
		$this->stmt->execute(array($prod));
	}
	
	protected function changeCount($posit,$nc)
	{
		$this->stmt = $this->pdo->prepare('UPDATE orders SET count = ? WHERE orderid = ?');
		$this->stmt->execute(array($nc,$posit));
	}
	
	protected function changeS($prodid, $val, &$count, $pfcid)
	{
		$this->stmt = $this->pdo->prepare('SELECT count FROM countlink WHERE size = ? AND color = ?');
		$this->stmt->execute(array($val['id'],$pfcid));
		$maxcount = $this->stmt->fetch();
		if ($maxcount < $count)
		{
			$count = $maxcount;
		}
		$this->stmt = $this->pdo->prepare('UPDATE orders SET sid = ?, count = ? WHERE orderid = ?');
		$this->stmt->execute(array($val['id'],$count,$prodid));
	}
	
	protected function changeColor($prodid, $val, &$count, $sizeid)
	{
		$this->stmt = $this->pdo->prepare('SELECT count FROM countlink WHERE size = ? AND color = ?');
		$this->stmt->execute(array($sizeid, $val['id']));
		$maxcount = $this->stmt->fetch();
		if ($maxcount < $count)
		{
			$count = $maxcount;
		}
		$this->stmt = $this->pdo->prepare('UPDATE orders SET pfcid = ?, colid = ?, fid = ?, count = ? WHERE orderid = ?');
		$this->stmt->execute(array($val['id'],$val['color'],$val['fid'],$count,$prodid));
	}
	
	protected function changeSC($prodid, $color, $size, &$count)
	{
		$this->stmt = $this->pdo->prepare('SELECT count FROM countlink WHERE size = ? AND color = ?');
		$this->stmt->execute(array($size['id'], $color['id']));
		$maxcount = $this->stmt->fetch();
		if ($maxcount < $count)
		{
			$count = $maxcount;
		}
		$this->stmt = $this->pdo->prepare('UPDATE orders SET sid = ?, pfcid = ?, colid = ?, fid = ?, count = ? WHERE orderid = ?');
		$this->stmt->execute(array($size['id'],$color['id'],$color['color'],$color['fid'],$count,$prodid));
	}
	
	protected function payed(&$log,&$usid,&$uslogin,$sesid,$ordids)
	{
		echo 'в разработке <br />';
		/*$usrinfo;
		if($log == 'on') 
		{
			$this->stmt = $this->pdo->prepare('SELECT name, surname, secondname, addr, postid, phone, email FROM users WHERE id = ?');
			$this->stmt->execute(array($usid));
			$row = $this->stmt->fetch();
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
			/*$this->stmt = $this->pdo->prepare('UPDATE users SET name = :name, surname = :surname, secondname = :lastname, addr = :addr, postid = :postid, phone = :phone, email = :email WHERE id = :id');
			$this->stmt->execute($usrinfo);
		}
		else 
		{
			/*приглашаем войти или зарегаться, получаем ответ $ans*/
			/*if ($ans == 1)
			{
				login($this->pdo,$log,$usid,$uslogin,$sesid);
				$this->stmt = $this->pdo->prepare('SELECT name, surname, secondname, addr, postid, phone, email FROM users WHERE id = ?');
				$this->stmt->execute(array($usid));
				$row = $this->stmt->fetch();
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
				/*$this->stmt = $this->pdo->prepare('UPDATE users SET 
										name = :name, 
										surname = :surname, 
										secondname = :lastname, 
										addr = :addr, 
										postid = :postid, 
										phone = :phone, 
										email = :email 
										WHERE id = :id');
				$this->stmt->execute($usrinfo);
			}
			else if ($ans == 2)
			{
				regist($this->pdo,$log,$usid,$uslogin,$sesid);
				/*форма для оплаты*/
				/*загружаем новое инфо юзера*/
				/*$this->stmt = $this->pdo->prepare('UPDATE users SET 
										name = :name, 
										surname = :surname, 
										secondname = :lastname, 
										addr = :addr, 
										postid = :postid, 
										phone = :phone, 
										email = :email 
										WHERE id = :id');
				$this->stmt->execute($usrinfo);
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
				/*$this->stmt = $this->pdo->prepare('UPDATE users SET 
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
				$this->stmt->execute($usrinfo);
			}
		$this->stmt = $this->pdo->prepare('UPDATE orders SET status = 1 WHERE prodid = ?')
		foreach ($ordids as $d)
		{
			$this->stmt->execute(array($d));
		}	*/	
	}
	
	}
?>