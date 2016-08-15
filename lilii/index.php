<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content = "text/html; charset=utf-8" />
<title> test </title>
<link rel="stylesheet" type="text/css" href="css/mainstyle.css" />
<script type="text/javascript" src="jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script> 
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<?php
$sesid = session_id();
require_once 'user.php';
require_once 'link.php';
require_once 'category.php';
require_once 'basket.php';
require_once 'posit.php';
require_once 'login.php';

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
if (isset($_GET['act']))
{
	if ($_GET['act'] == 'atb')
	{
		$usr->add_to_basket($_GET['prodid'],$_GET['c'],$_GET['s'],$_GET['catpath'],1);
		header('location: http://127.0.0.1/edsa-lilii/index.php?type=product&catpath='.$_GET['catpath'].'&prodid='.$_GET['prodid']);
		exit();
	}
}
function closelib(&$lib, $path, $i, $toclose, $close)
{
	foreach($lib as &$cat)
	{
		if ($close)
			$cat['open']=0;
		if (isset($path[$i])&&($cat['id'] == $path[$i]))
		{
			if ($cat['id']==$toclose)
			{
				$close = true;
			}
			if (isset($path[$i+1])) closelib($cat['sub'], $path, ++$i, $toclose, $close);
		}
	}
}
function openlib(&$usr, &$lib, $path, $i)
{
	foreach($lib as &$cat)
	{
		$cat['open'] = 1;
		if (isset($path[$i]))
		{
			if ((!isset($path[$i+1]))&&($cat['id'] == $path[$i]))				
			{
				$usr->opencategory($cat['id'],$cat['desc'], $path);
				if(($usr->current_category->prodcount == 0)&&(isset($cat['sub'][0])))
				{
					$path[$i+1] = $cat['sub'][0]['id'];
				}
			}
			if (($cat['id'] == $path[$i])&&(isset($cat['sub'][0])))
			{
				openlib($usr, $cat['sub'], $path, ++$i);
			}
		}
	}
}
function searchcat(&$usr, $path)
{
	$change = true;
	$close = true;
	$closeid = 0;
	for( $i = 0; $i < count($path); $i++)
	{
		if (isset($usr->cur_cat_path[$i]))
		{	
			if ($path[$i] == $usr->cur_cat_path[$i])
			{
				if(!isset($usr->cur_cat_path[$i+1]))
				{
					$close = false;
					if(!isset($path[$i+1]))
						$change = false;
				}
				else if(!isset($path[$i+1]))
					$closeid = $usr->cur_cat_path[$i+1];
			}
			else $closeid = $usr->cur_cat_path[$i];
		}
	}
	if ($change) 
	{
		if ($close) 
			closelib($usr->library, $usr->cur_cat_path, 0, $closeid, false);
		openlib($usr, $usr->library, $path, 0);
	}
}
if (!empty($_GET['catpath']))
{
	$p = unserialize($_GET['catpath']);
	searchcat($usr,$p);
	$s = serialize($usr);
	file_put_contents($file,$s);
	$usr = unserialize($s);
}
function printlib($lib,$i,$path)
{
	foreach($lib as $cat)
	{
		$p = $path;
		$p[] = (int)$cat['id'];
		echo '<div id="lib'.$i.'"><a href="index.php?type=category&catpath='.serialize($p).'&count=2">';
		if ($i < 3) 
			echo strtoupper($cat['name']).'</a>';
		else
			echo $cat['name'].'</a>';
		if ((isset($cat['sub'][0]))&&($cat['sub'][0]['open'] == 1))
		{
			printlib($cat['sub'],$i+1, $p);
		}
		echo '</div>';
	}
}

?>
	<div id="shadow"></div>
	<div id="log">
	</div>
	<?php
	echo '<div style="position:fixed;top:0px;left:0px;width:100%;Z-index:90;opacity:0.2;';
	if (isset($_GET['type']))
		echo 'display:none;';
	echo '">';
	?>
	<img src="images/fon.jpg" width="100%"></div>
	<?php
	echo '<header';
	if(isset($_GET['type']))
		echo' style="background:#ffffff;"';
	echo '>';
	?>
		<img id="logo" src="images/logolilii.png" alt="logo">
		<div id="hmenu">
			<?php
			if($usr->login == 'admin')
			{
				echo '<div style="position:relative;top:10px;float:left;min-width: 70px;"><a href="admin.php';
				if (isset ($_GET['type']))
				{
					echo '?type='.$_GET['type'];
					if (isset ($_GET['catpath']))
						echo '&catpath='.$_GET['catpath'].'&count='.$_GET['count'];
				}
				echo '">АДМИНКА</a>&nbsp&nbsp&nbsp </div>';
			}
			?>
			<div onClick="login()" style="position:relative;top:10px;float:left;min-width: 70px;">МОЙ АККАУНТ &nbsp&nbsp&nbsp </div>
			<div style="position:relative;top:10px;float:left;min-width: 70px;">КОНТАКТЫ</div>
			<div <?php if ($usr->basket->poscount > 0) echo 'onmouseenter="openminibask()" onmouseleave="tstart()" ';?> style="float:left;">
			<?php
			if ($usr->basket->poscount > 0)
			{
				echo '<a href="index.php?type=basket"><img id="baskmain" src="images/basketmain.png" alt="корзина">';
				echo '<div id="basccount" style="position:relative;width:15px;height:15px;left:14px;top:-3px;padding-left:0px;">'.$usr->basket->poscount.'</div></a>';
				if ($_GET['type'] != 'basket')
				{
					echo '<div id="minibask" onmouseenter="clearTimeout(timer);" style="display:none; position:fixed; padding: 3px; background: white; Z-index:104; width:220px; top:35px; right:42px; box-shadow: 0 0 7px 1px #d8d8d8;">';
					echo '<table cellpadding="10">';
					foreach ($usr->basket->positions as $p)
					{
						$h ='http://127.0.0.1/edsa-lilii/index.php?type=product&catpath='.$p->position['cpath'].'&prodid='.$p->position['pid'].'&color='.$p->position['colid'];
						echo '<tr><td style="background: url(\'images/dotted_line.png\') repeat-x bottom left;"><a href='.$h.'><img src="'.$p->position['foto'].'" style="width:45px;"></a></td>';
						echo '<td style="background: url(\'images/dotted_line.png\') repeat-x bottom left;"><a href='.$h.'><p style="text-transform:uppercase;">'.$p->position['desc'].'</p>';
						$c1 = (int)($p->position['cost']/1000);
						$c2 = $p->position['cost'] - $c1*1000;
						echo '<p style="margin-top: -5px;">'.$c1.'&nbsp';
						if ($c2 < 100)
						{
							if ($c2 < 10)
								echo '00';
							else echo '0';
						}
						echo $c2.'&nbspруб.</p></a></td></tr>';
					}
					echo '<tr><td colspan="2" style="padding: 17px 23px;"><a href="index.php?type=basket"><div style="text-transform:uppercase; width:120px; padding: 8px 25px; color:white; background: black;">посмотреть корзину</div></a></td></tr>';
					echo '</table></div>';
				}
			}
			else echo '<img id="baskmain" src="images/basketmain.png" alt="корзина">';?></div></a>
		</div>
		<div id="clear"></div>
		<div id="search">
			<img id="line" src="images/line.png" alt="search">
			ПОИСК
		</div>
		<?php
		if(isset($_GET['type']))
		{
			if ($_GET['type'] == 'category')
			{
				$s = $usr->current_category->prodcount;
				echo '<div id="prodcount">'.$s.' &nbsp товар';
				$h ='| просмотреть <a href="index.php?type=category&catpath='.serialize($usr->cur_cat_path).'&count=2">2</a>&nbsp<a href="index.php?type=category&catpath='.serialize($usr->cur_cat_path).'&count=6">6</a>';
				$i = $s%10;
				if (($s < 10) or ($s > 20))
				{
					if ($i == 1)
					{
						echo $h.'</div>';
					}
					else if (($i > 0)&&($i < 5))
					{
						echo 'а '.$h.'</div>';
					}
					else
					{
						echo 'ов '.$h.'</div>';
					}
				}
				else echo 'ов '.$h.'</div>';
			}
		}
		?>
	</header>
	<div style="height: 160px; width: 100%"></div>
	<main>
	<div id="linker1"></div>
	<aside id="aside1"><?php
		$p = array();
		printlib($usr->library,1,$p);?>
		<div id="info">
			<a href="index.php?type=info">Информация</a><br>
			<a href="index.php?type=invest">Инвесторам</a>
		</div>
	</aside>
	<article id="article">
			<?php
			if (isset($_GET['type']))
			{
				switch ($_GET['type'])
				{
					case "category":
					{
						echo '<div id="content">';
						if ($_GET['count'] == 2)
						{
							$left = true;
							foreach($usr->current_category->products as $prod)
							{
								if ($left) echo '<div id="prodstr">';
								echo'<a href="index.php?type=product&catpath='.$_GET['catpath'].'&prodid='.$prod->info['id'].'"><div id="prod';
								if ($left) echo 'l">';
								else echo 'r">';
								echo '<img id="prod2" src="'.$prod->info['foto'].'"><br><br><br>';
								echo '<span id="desc">'.strtoupper($prod->info['name']).'</span><br>';
								$c = (int)($prod->info['cost'] / 1000);
								echo '<span id="cost">'.$c.' '.($prod->info['cost'] - $c*1000).' руб.</span><br><br><br></div></a>';
								if (!$left) echo '</div>';
								$left = !$left;
							}
							if (($usr->current_category->prodcount % 2) == 1)
								echo '<div id="prodr" style="bacground:#ffffff;display:block;color:#ffffff">rgh fhf ghf fdg srh fgx bxb xgh dxf gsr dgf fxd gxd fgx dfg xfc gxd fgx dfg xdf gse rdt gxf dgz dfg xdf gxd fgx drg dxf gxf gxd fgx dfg xgx dfg xfd gbx cfg xfd</div></div>';
						}
						else
						{
							echo '<div id="c_info">Раздел в разработке</div>';
						}
						break;
					}
					case "product":
					{
						echo '<div id="content">';
						echo '<div id="prm"><div id="prupbar"><div style="float:left;">';
						echo '<a href="#" onclick="history.back();">';
						echo 'НАЗАД</a></div><div style="float:right;">';
						$i = -1;
						$chk = false;
						$p = 0;
						foreach($usr->current_category->products as $prod)
						{
							if ($prod->info['id'] == $_GET['prodid'])
							{
								$p = $prod;
								if ($i < 0)
								{
									echo '<img src="images/prlp.png">';
								}
								else
								{
									echo '<a href="index.php?type=product&catpath='.$_GET['catpath'].'&prodid='.$i.'"><img src="images/prla.png" border="0"></a>';
								}
								$chk = true;
							}
							else if ($chk)
							{
								echo '<a href="index.php?type=product&catpath='.$_GET['catpath'].'&prodid='.$prod->info['id'].'"><img src="images/prra.png" border="0"></a>';
								$chk = false;
							}
							$i = $prod->info['id'];
						}
						if ($chk)
						{
							echo '<img src="images/prrp.png">';
						}
						echo '</div></div>';
						echo '<div id="prfotos">';
						if (isset($_GET['color']))
							$col = $_GET['color'];
						else $col = $p->choise['color'];
						$colname = '';
						foreach($p->colors as $color)
						{
							if ($color['val'] == $col)
							{
								$colname = $color['name'];
								$left = true;
								foreach($color['fotos'] as $foto)
								{
									if ($left) echo '<div style="display:table-row;width:100%;float:top;"><div style="width:50%;float:left;text-align:left;padding-top:5px;padding-bottom:5px;">';
									else echo '<div style="width:50%;float:right;text-align:right;padding-top:5px;padding-bottom:5px;">';
									echo '<img src="'.$foto['path'].'" style="width:97%">';
									if ($left) echo '</div>';
									else echo '</div></div>';
									$left = !$left;
								}
								if (!$left) echo '<div style="width:50%;float:right;color:#ffffff">dfs dfs sdf dsf sdf dsf sdf sdf sdf sdf sdf dsf sdf dsf dsf dff sd dsf dsf sdf sdf sfd</div></div>';
							}
						}
						echo '</div>';
						if (count($p->info['compable']) > 0)
						{
							if (count($p->info['compable']) > 1)
							{
								$left = true;
								foreach($color['fotos'] as $foto)
								{
									if ($left) echo '<div style="display:table-row;width:100%;float:top;"><div style="width:50%;float:left;text-align:left;padding-top:5px;padding-bottom:5px;">';
									else echo '<div style="width:50%;float:right;text-align:right;padding-top:5px;padding-bottom:5px;">';
									echo '<img src="'.$foto['path'].'" style="width:97%">';
									if ($left) echo '</div>';
									else echo '</div></div>';
									$left = !$left;
								}
								if (!$left) echo '<div style="width:50%;float:right;color:#ffffff">dfs dfs sdf dsf sdf dsf sdf sdf sdf sdf sdf dsf sdf dsf dsf dff sd dsf dsf sdf sdf sfd</div></div>';
							}
							else
							{
								$c = $p->info['compable'][0];
								echo '<div style="display:table-row;width:100%;float:top;"><div style="width:50%;position:relative; left:0px; right:0px; margin:auto; padding-top:5px;padding-bottom:5px; text-align:center;">';
								echo '<img src="'.$c['foto'].'" style="width:97%;"><br><br><span>'.strtoupper($c['name']).'</span><br>';
								$c1 = (int)($c['cost']/1000);
								$c2 = $c['cost'] - $c1*1000;
								echo '<span>'.$c1.'&nbsp';
								if ($c2 < 100)
								{
									if ($c2 < 10)
										echo '00';
									else echo '0';
								}
								echo $c2.'&nbspруб.</span><br>';
								echo '</div></div>';
							}
						}
						print_r($p->info['compable']);
						echo '</div>';
						echo '<div id="prinfo">';
						echo '<span onClick="test11();" style="padding-left: 29px;font-size:15px;">'.strtoupper($p->info['name']).'</span><br><br><br>';
						$c = (int)($p->info['cost'] / 1000);
						echo '<span style="padding-left: 29px;font-size:14px;font-weight:bold;">'.$c.' '.($p->info['cost'] - $c*1000).' руб. </span><br><br><br>';?>
						<div id="cpar" onClick="openWin('#compound','block','#cpar')" style="padding-left: 29px;font-size:10px;float:left;">СОСТАВ И УХОД&nbsp&nbsp&nbsp|</div>
						<div onClick="openWin('#send','block','#cpar')" style="font-size:10px;float:left;">&nbsp&nbsp&nbspДОСТАВКА&nbsp&nbsp&nbsp|</div>
						<div onClick="openWin('#back','block','#cpar')" style="font-size:10px;float:left;">&nbsp&nbsp&nbspВОЗВРАТ</div><br><br><br><br><br><br>
						<div onClick="openWin('#compound','none','#cpar')" id="compound" style="padding: 5px 10px 0px;"><div style="float:left; text-transform: uppercase; font-weight:bold;">состав:</div><img src="images/undo.png" style="float:right">
						<?php
						$comp1 = explode(';',$p->info['compos']);
						$i=0;
						foreach ($comp1 as $compos)
						{
							$val = explode(':',$compos);
							if ($i)
							{
								echo '<p style="margin-top:-10px;">';
							}
							else echo '<p>';
							echo '<span style="font-weight:bold;">'.$val[0].':</span>&nbsp'.$val[1].'</p>';
							$i++;
						}
						?><div id="clear"></div><div style="width:228px;height:30px;display:block;"><p style="text-transform: uppercase; font-weight:bold;">уход:</p></div>
						<div style="float:top;width:228px;height:1px;"></div>
						<div id="clear"></div>
						<?php
						$i=0;
						foreach($p->info['care'] as $care)
						{
							if ($i)
								echo '<div style="width:15px;height:30px;float:left;display:block;"></div>';
							echo '<img src="'.$care['foto'].'" title="'.$care['desc'].'" style="float:left;">';
							$i++;
						}
						?></div>
						<div onClick="openWin('#send','none','#cpar')" id="send" style="padding: 5px 10px 0px;"><div style="float:left; text-transform: uppercase; font-weight:bold;">доставка</div><img src="images/undo.png" style="float:right"><p>Для доставки товаров мы используем различные транспортные компании. </p><p style="font-weight:bold;">Сроки:</p><p style="margin-top:-10px;">Доставка производится в самые короткие сроки: в среднем, занимает 2-3 рабочих дня. </p><p style="font-weight:bold;">Получение:</p><p style="margin-top:-10px;">Получение товара осуществляется на складе/офисе транспортной компании или курьером до квартиры.</p></div>
						<div onClick="openWin('#back','none','#cpar')" id="back" style="padding: 5px 10px 0px;"><div style="float:left; text-transform: uppercase; font-weight:bold;">возврат</div><img src="images/undo.png" style="float:right"><p>Мы предоставляем возможность вернуть товары без лишних хлопот в течение 30 дней с момента получения заказа</p><p>По вопросам возврата Вы всегда можете связаться с консультантом.</p></div>
						<div style="display:table-row;width:100%;padding-left: 29px;"><?php
						foreach($p->colors as $color)
						{
							echo '<a href="index.php?type=product&catpath='.$_GET['catpath'].'&prodid='.$p->info['id'].'&color='.$color['val'].'">';
							echo '<div style="position:relative;left:38px;float:left;text-align:center;width:40px;"><img src="'.$color['main'].'" width="22";><br>';
							echo '<img src="images/col.png"><br>';
							echo '<span style="font-size:10px">'.$color['name'].'</span></div></a>&nbsp&nbsp&nbsp';
						}
						echo '</div><br><br><br>';
						echo '<span id="csize" onClick="changesize(-1)" style="padding-left: 29px;font-size:12px;">ВЫБЕРИТЕ РАЗМЕР</span><br><br>';
						echo '<div style="position:relative;left:29px;width:65%;height:1px;background:#000000;"></div><br>';
						echo '<div id="sizes" style="display:table-row;width:65%;padding-left: 29px;">';
						foreach($p->sizes as $size)
						{
							echo '<div id="siz'.$size['id'].'" onClick="changesize('.$size['id'].')" onMouseEnter="fixS('.$size['id'].')" onMouseLeave="outS('.$size['id'].')" style="cursor:pointer;position:relative;left:29px;width:100%;padding: 4 9;float:top;font-size:10px">'.$size['size'].'&nbsp('.$size['desc'].')</div>';
						}
						echo '</div><form name="fcsval"><input type="hidden" value="';
						if (count($p->sizes) == 1) echo $p->sizes[0]['id'];
						else echo 'none';
						echo '" name="csval"/></form><div style="height:12px;"></div>';?>
						<div style="position:relative;left:29px;width:65%;height:14px;border-top: 1px dashed #000;"></div>
						<a href="#nul" onClick="window.open('sizeman.php ','','Toolbar=1,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=550,Height=400');"><span style="padding-left: 29px;font-size:10px;">СПРАВОЧНИК ПО РАЗМЕРАМ<span></a>
						<div style="position:relative;left:29px;width:65%;height:14px;border-bottom: 1px solid #000;"></div>
						<div style="position:relative;left:29px;width:100%;height:25px;"></div>
						<div onclick="addtobasket(<?php echo '\''.$colname.'\'';?>)" style="position:relative;left:29px;width:200px;height:29px;padding-top:15px;background:#000000;border:none;color:#ffffff;text-align:center;text-decoration:none;display:inline-block;font-size:12px;">ДОБАВИТЬ В КОРЗИНУ</div>
						<?php
						echo '</div>';
						break;
					}
					case "basket":
					{
						echo '<div id="content2">';
						?>
						<div style="font-family: 'Exo 2'; width:auto; padding-left:15px; padding-right:170px;">
						<div style="width:100%; padding-top:5px; padding-bottom:22px; font-size:14px; font-weight:bold;">КОРЗИНА</div>
						<form id="baskf" name="baskf">
						<table cellpadding="0" style="width:100%; min-width: 500px; border-collapse: collapse; font-size: 10px;background: url('images/dotted_line.png') repeat-x top left;">
							<thead>
								<tr>
								<td style="padding-top:15px; padding-bottom:15px; font-weight:bold; background: url('images/dotted_line.png') repeat-x bottom left; width:110px;">ПРОДУКТ</td>
								<td style="padding-top:15px; padding-bottom:15px; font-weight:bold; background: url('images/dotted_line.png') repeat-x bottom left;">ОПИСАНИЕ</td>
								<td style="padding-top:15px; padding-bottom:15px; font-weight:bold; background: url('images/dotted_line.png') repeat-x bottom left; text-align:center; width:15%;">ЦВЕТ</td>
								<td style="padding-top:15px; padding-bottom:15px; font-weight:bold; background: url('images/dotted_line.png') repeat-x bottom left; text-align:center; width:15%;">РАЗМЕР</td>
								<td style="padding-top:15px; padding-bottom:15px; font-weight:bold; background: url('images/dotted_line.png') repeat-x bottom left; text-align:center; width:10%;">ШТУК</td>
								<td style="padding-top:15px; padding-bottom:15px; font-weight:bold; background: url('images/dotted_line.png') repeat-x bottom left; text-align:center; width:15%;">СУММА</td>
								<td style="padding:15px 20px 15px 0px; font-weight:bold; background: url('images/dotted_line.png') repeat-x bottom left; text-align: right; width:15%;">УДАЛИТЬ</td>
								</tr>
							</thead>
							<tbody>
							<?php
							$count = 0;
							$sum = 0;
							$tmppos = 100;
							foreach($usr->basket->positions as $p)
							{
								$h ='http://127.0.0.1/edsa-lilii/index.php?type=product&catpath='.$p->position['cpath'].'&prodid='.$p->position['pid'].'&color='.$p->position['colid'];
								echo '<tr>
								<td id="foto'.$p->position['id'].'" style="padding-top:15px; padding-bottom:15px;background: url(\'images/dotted_line.png\') repeat-x bottom left;">';
								echo '<a href="'.$h.'"><img src="'.$p->position['foto'].'" style="width:65px;"></a>';
								echo '</td>
								<td style="padding-top:15px; padding-bottom:15px;background: url(\'images/dotted_line.png\') repeat-x bottom left; text-transform: uppercase;"><a href="'.$h.'">';
								echo $p->position['desc'];
								echo '</a></td>
								<td id="c'.$p->position['id'].'" style="padding-top:15px; padding-bottom:15px;background: url(\'images/dotted_line.png\') repeat-x bottom left; text-transform: uppercase; text-align:center;">';
								if (count($p->colors) > 1)
								{
									if (count($p->sizes) < 2)
									{
										echo '<select onChange="chcolor('.$p->position['id'].',this)" style="width:auto; border: 0px;">';
										foreach($p->colors as $c)
										{
											foreach ($p->maxcounts as $m)
											{
												if (($m['sid'] == $p->position['sid'])&&($m['cid'] == $c['id']))
													break;
											}
											if ($c['name'] == $p->position['color'])
												echo '<option selected value='.$m['count'].':'.$c['id'].':'.$c['foto'].'>';
											else echo '<option value='.$m['count'].':'.$c['id'].':'.$c['foto'].'>';
											echo $c['name'].'</option>';
										}
										echo '</select>';
									}
									else echo $p->position['color'].'<img onClick="chpos('.$p->position['id'].',this)" src="images/select.png" style="width:13px; height:13px;">';
								}
								else echo $p->position['color'];
								echo '</td>
								<td id="s'.$p->position['id'].'" style="padding-top:15px; padding-bottom:15px;background: url(\'images/dotted_line.png\') repeat-x bottom left; text-align:center;">';
								if (count($p->sizes) > 1)
								{
									if (count($p->colors) < 2)
									{
										echo '<select onChange="chsize('.$p->position['id'].',this)" style="font-size:10px; border: 0px;">';
										foreach($p->sizes as $s)
										{
											foreach ($p->maxcounts as $m)
											{
												if (($m['sid'] == $s['id'])&&($m['cid'] == $p->position['pfcid']))
													break;
											}
											if ($s['size'] == $p->position['size'])
												echo '<option selected value='.$m['count'].':'.$s['id'].'>';
											else echo '<option value='.$m['count'].':'.$s['id'].'>';
											echo $s['size'].'&nbsp('.$s['sdesc'].')</option>';
										}
										echo '</select>';
									}
									else echo $p->position['size'].'&nbsp('.$p->position['sdesc'].')<img onClick="chpos('.$p->position['id'].',this)" src="images/select.png" style="width:13px; height:13px;">';
								}
								else echo $p->position['size'].'&nbsp('.$p->position['sdesc'].')';
								echo '</td>
								<td style="padding-top:15px; padding-bottom:15px;background: url(\'images/dotted_line.png\') repeat-x bottom left; text-align:center;">';
								if ($p->position['count'] < 2) echo '<img src="images/minusIA.png" style="width:10px;">';
								else echo '<img onClick="chcount('.$p->position['id'].','.($p->position['count'] - 1).',this)" src="images/minus.png" style="width:10px;">';
								echo '&nbsp'.$p->position['count'].'&nbsp';
								$check = false;
								foreach ($p->maxcounts as $m)
								{
									if (($m['sid'] == $p->position['sid'])&&($m['cid'] == $p->position['pfcid']))
									{
										$check = true;
										break;
									}
								}
								if ($p->position['count'] < $m['count'])
									echo '<img onClick="chcount('.$p->position['id'].','.($p->position['count'] + 1).',this)" src="images/plus.png" style="width:10px;">';
								else echo '<img src="images/plusIA.png" style="width:10px;">';
								echo '<input type="hidden" value="'.$p->position['count'].'" name="curc'.$p->position['id'].'" id="curc'.$p->position['id'].'"><input type="hidden" value="'.$m['count'].'" name="maxc'.$p->position['id'].'" id="maxc'.$p->position['id'].'">';
								echo '</td>
								<td id="cost'.$p->position['id'].'" style="padding-top:15px; padding-bottom:15px;background: url(\'images/dotted_line.png\') repeat-x bottom left; text-align:center;">';
								$c = (int)(($p->position['cost']*$p->position['count'])/1000);
								$c2 = $p->position['cost']*$p->position['count'] - 1000*$c;
								echo '<input type="hidden" value="'.$p->position['cost'].'" name="cinp'.$p->position['id'].'" id="cinp'.$p->position['id'].'">';
								echo '<input type="hidden" value="'.($p->position['cost']*$p->position['count']).'" name="suminp'.$p->position['id'].'" id="suminp'.$p->position['id'].'">';
								echo $c.'&nbsp';
								if ($c2 < 100)
								{
									if ($c2 < 10)
										echo '00';
									else echo '0';
								}
								echo $c2.'&nbspруб.';
								echo '</td>
								<td style="padding:15px 20px 15px 0px; background: url(\'images/dotted_line.png\') repeat-x bottom left; text-align: right;">';
								echo '
								<img onClick="undopos('.$p->position['id'].',this)" src="images/undo.png" style="width:20px;">
								';
								echo '</td></tr>';
								$count += $p->position['count'];
								$sum += $p->position['cost']*$p->position['count'];
							}
							?>
							</tbody>
							<tfoot style="font-size:12px; font-weight:normal;"><tr>
							<th colspan="6" style="background: url('images/dotted_line.png') repeat-x bottom left; background-color: #f8f8f8; text-align:right; font-size: 18px; font-weight:bold; padding-right:15px; padding-bottom: 15px; padding-top: 15px;">Итого: </th>
							<td id="summ" style="background: url('images/dotted_line.png') repeat-x bottom left; background-color: #f8f8f8; padding-left:15px; padding-bottom: 15px; padding-top: 15px; font-size: 18px; font-weight:bold;"><?php
							$s1 = (int)($sum/1000);
							$s2 = $sum - $s1*1000;
							echo '<input type="hidden" value="'.$sum.'" name="sumary" id="sumary">';
							echo $s1.'&nbsp';
							if ($s2 < 100)
							{
								if ($s2 < 10)
									echo '00';
								else echo '0';
							}
							echo $s2;?> &nbspруб.</td>
							</tr>
							<tr>
							<th colspan="3" style="background: url('images/dotted_line.png') repeat-x bottom left; font-weight:normal; padding-left: 0px; padding-top:30px; padding-bottom:40px;">
							<a href="#" onclick="history.back();"><div style="cursor:pointer; border: 1px solid black; width:140px; padding: 10px 35px; text-transform:uppercase;">Продолжить покупку</div></a></th>
							<th colspan="4" style="background: url('images/dotted_line.png') repeat-x bottom left; font-weight:normal; padding-right: 0px; padding-top:30px; padding-bottom:40px;">
							<div onClick="pay('payinfo');" style="cursor:pointer; float:right; border: 1px solid black; background:#000000; color:#ffffff; width:140px; padding: 10px 35px; text-transform:uppercase;">оформить заказ</div></th></tr>
							</tfoot>
						</table>
						<?php
						echo '<input type="hidden" value="'.$usr->basket->poscount.'" name="poscount" id="poscount"></form>';
						foreach($usr->basket->positions as $p)
						{
							if (count($p->sizes) > 1)
							{
								if (count($p->colors) > 1)
								{
									echo '<div id="chposcs'.$p->position['id'].'" style="display:none; position:fixed;"><div style="float:left; background:#ffffff; border: 1px solid #a8a8a8;">';
									echo '<form onChange="chosecs('.$p->position['id'].',this)" id="chcs'.$p->position['id'].'">';
									?>
									<table style="font-size:12px;">
									<thead>
									<td></td>
									<?php
									foreach ($p->colors as $c)
										echo '<td style="text-align:center;">'.$c['name'].'</td>';
									echo '</thead><tbody>';
									foreach ($p->sizes as $s)
									{
										echo '<tr><td>'.$s['size'].'&nbsp('.$s['sdesc'].')</td>';
										foreach ($p->colors as $c)
										{
											echo '<td style="text-align:center;">';
											$check = false;
											foreach ($p->maxcounts as $m)
											{
												if (($m['sid'] == $s['id'])&&($m['cid'] == $c['id'])&&($m['count']))
												{
													$check = true;
													break;
												}
											}
											if ($check)
											{
												echo '<input type="radio" name="rchcs'.$p->position['id'].'"';
												if (($s['size'] == $p->position['size'])&&($c['name'] == $p->position['color']))
													echo ' checked';
												echo ' value="'.$m['count'].':'.$s['size'].':'.$s['sdesc'].':'.$c['name'].':'.$s['id'].':'.$c['id'].':'.$c['foto'].'"></td>';
											}
											else echo '<img src="images/radioIA.png"></td>';
										}
										echo'</tr>';
									}
									echo '</tbody></table></form></div><img onClick="clchpos('.$p->position['id'].')" src="images/undo.png" style="float:left;"></div>';
								}
							}
							$tmppos +=100;
						}
						?>
						</div>
						<?php
						break;
					}
					case "info":
					{
						echo '<div id="content2">';
						echo '<div id="c_info">Раздел в разработке</div>';
						break;
					}
					case "invest":
					{
						echo '<div id="content2">';
						echo '<div id="c_invest">Раздел в разработке</div>';
						break;
					}
				}
			}
			else
			{
				echo '<div id="content">';
				echo '<div style="width:100%;height:400px;"></div>';
			}
			?>
		</div>
		<div style="width:100%;height:15px;dysplay:block;"></div>
		<div id="footer">
			<span id="social">Мы в социальных сетях:</span><br><br>
			<a href="https://vk.com/lilii_novosibirsk" target="_blank">вконтакте</a>&nbsp&nbsp&nbsp
			<a href="https://www.instagram.com/lilii_clothes/" target="_blank">instagram</a>
			
		</div>
	</article>
	</main>
	<script>
(function()
{
	var a = document.querySelector('#aside1'), 
		b = null, 
		asidetop = null, 
		pr = null,
		prmove = null,
		Z = 0, 
		P = 0, 
		N = 0;  // если у P ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента, если у N — нижний край дойдёт до нижнего края элемента. Может быть отрицательным числом
	window.addEventListener('scroll', Ascroll, false);
	document.body.addEventListener('scroll', Ascroll, false);
	function Ascroll() 
	{
		var Rasid1 = a.getBoundingClientRect(),//квадрат aside'а
			c = document.querySelector('#article'),
			checkpr = false;
			R1bottom = c.getBoundingClientRect().bottom;//низ article
		if (document.querySelector('#prinfo'))
		{
			checkpr = true;
			var pi = document.querySelector('#prinfo'),
				pm = document.querySelector('#prm');
			if (pi.getBoundingClientRect().bottom > pm.getBoundingClientRect().bottom)
			{
				pr = pi;
			}
			else pr = pm;
		}
		if (Rasid1.bottom > R1bottom) 
		{
			a = c;
			Rasid1 = a.getBoundingClientRect();
		}
		if (b == null) 
		{
			var Sa = getComputedStyle(a, ''), 
				s = '';//стиль aside
			for (var i = 0; i < Sa.length; i++) //копирует нужные свойства из aside
			{
				if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) 
				{
					s += Sa[i] + ': ' +Sa.getPropertyValue(Sa[i]) + '; '
				}
			}
			b = document.createElement('div');//создает div
			b.className = "stop";//класс стоп
			b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';//цепляет в него ширину aside
			a.insertBefore(b, a.firstChild);//ставит его перед первым потомком aside, то есть в начало его
			var l = a.childNodes.length;
			for (var i = 1; i < l; i++) 
			{
				b.appendChild(a.childNodes[1]);//переписывает в div потомков aside
			}
			a.style.height = b.getBoundingClientRect().height + 'px';//aside ставит высоту равную высоте прямоугольника нового div
			a.style.padding = '0';
			a.style.border = '0';
		}
		var Rdiv = b.getBoundingClientRect(),
			Rlbottom = document.querySelector('#linker1').getBoundingClientRect().bottom,
			//Rh = Rasid1.top + Rdiv.height,//высота оставшегося в поле зрения div
			W = document.documentElement.clientHeight;//высота видимой области
			//R1 = Math.round(Rh - R1bottom),//высота нижнего края div - низ article
			//R2 = Math.round(Rh - W);//разница между высотой оставшегося в поле зрения div и высотой видимого окна
		if (Rasid1.height > Rlbottom - 160)
		{
			if (Rasid1.top < asidetop)//едем вниз
			{
				if (Rdiv.bottom > Rlbottom)//низ дива относительно экрана больше чем высота экрана
				{
					b.className = 'stop';//стоим
					b.style.top = Rdiv.top - Rasid1.top + 'px';//высота относительно начала документа равна разнице между верхами эсайда и дива
				}
				else//низ дива попал на экран
				{
					b.className = 'sticky';//ползем
					b.style.top = Rlbottom - Rasid1.height + 'px';//нижняя граница на высоте экрана
				}
			}
			else//едем вверх
			{
				if (Rdiv.top < 0)//верх дива выше экрана
				{
					b.className = 'stop';//стоим
					b.style.top = Rdiv.top - Rasid1.top +'px';//высота отнно начала док = разнице меджду высотой тек полож дива и эсайда
				}
				else//верх дива зашел на экран
				{
					b.className = 'sticky';//прилипли
					b.style.top = 160 + 'px';//верхняя граница совпадает с началом экрана
				}
			}
			asidetop = Rasid1.top;
		}
		else
		{
			//alert("<<<"+Rasid1.height+" "+Rlbottom);
			b.className = 'sticky';//прилипли
			b.style.top = 160 + 'px';//верхняя граница совпадает с началом экрана
			b.style.height = Rasid1.height;
		}
		window.addEventListener('resize', function() 
		{
			a.children[0].style.width = getComputedStyle(a, '').width;
		}, false);
		//}
	}
})()
function  test11(){
	var x = document.querySelector('#prinfo');
	var y = x.getBoundingClientRect;
	var z = x.style;
	alert(y.top+" - "+y.bottom);
	alert(z.height+" - "+y.height);
}
function openminibask(){
	var x;
	if (x = document.querySelector('#minibask'))
	{
		x.style.display = 'block';
	}
}
var timer;
function tstart(){
	timer = setTimeout(closeminibask,200);
}
function closeminibask(){
	var x;
	if (x = document.querySelector('#minibask'))
	{
		x.style.display = 'none';
	}
}
function fixS(id)
{
	var input = document.forms.fcsval.csval;
	if (input.value != id)
	{
		var tmp = '#siz'+id;
		var x = document.querySelector(tmp);
		x.style.setProperty('background','#D8D8D8');
	}
}
function outS(id)
{
	var input = document.forms.fcsval.csval;
	if (input.value != id)
	{
		var tmp = '#siz'+id;
		var x = document.querySelector(tmp);
		x.style.removeProperty('background');
	}
}
function changesize(id,event){
	var block = document.querySelector('#sizes');
	var elems = block.getElementsByTagName('div');
	if (id != -1)
	{
		var input = document.forms.fcsval.csval;
		var y = document.forms.fcsval.csval.value;
		input.value = id;
		if (y != 'none') outS(y);
		var tmp = '#siz'+id;
		var x = document.querySelector(tmp);
		document.querySelector('#csize').style.removeProperty('color');
	}
	for (var i = 0; i < elems.length; i++)
	{
		if (id == -1)
			elems[i].style.display = 'block';
		else if (elems[i] != x)
		{
			if (elems[i].style.display != 'none')
				elems[i].style.display = 'none';
			else elems[i].style.display = 'block';
		}
	}
  	(event && event.stopPropagation) ? event.stopPropagation() : window.event.cancelBubble = true;
}
function openWin(W,S,P){
	var left=document.querySelector(P).getBoundingClientRect().left,
		top=document.querySelector(P).getBoundingClientRect().top;
	document.querySelector(W).style.top = top + 'px';
	document.querySelector(W).style.left = left + 29 + 'px';
	document.querySelector(W).style.display = S;
	document.querySelector('#shadow').style.display = S;
}
function addtobasket(color)
{
	var input = document.forms.fcsval.csval.value;
	if (input == 'none')
		document.querySelector('#csize').style.setProperty('color','#ff0000');
	else 
		location.href= window.location.href+"&act=atb&c="+color+"&s="+input;
}
<?php
if (isset($_GET['type']) && ($_GET['type'] == 'basket'))
{
?>
function undopos(id,obj)
{
	var n ='#suminp'+id;
	var inp = document.querySelector(n);
	var cost = parseInt(inp.value);
	inp = document.querySelector('#sumary');
	var sumval = parseInt(inp.value);
	sumval -= cost;
	var c1 = Math.floor(sumval/1000);
	var c2 = sumval - c1*1000;
	var htm = '<input type="hidden" value="'+sumval+'" name="sumary" id="sumary">'+c1+'&nbsp';
	if (c2 < 100)
	{
		if (c2 < 10)
			htm += '00';
		else htm += '0';
	}
	htm += c2+'&nbspруб.';
	var x = document.querySelector('#summ');
	x.innerHTML = htm;
	
	var row = obj.parentNode.parentNode;
	var table = row.parentNode.parentNode;
	table.deleteRow(row.rowIndex);
	inp = document.querySelector('#poscount');
	var count = inp.value;
	var empty = false;
	count--;
	if (count > 0)
	{
		document.querySelector('#basccount').innerHTML = count;
		inp.value = count;
	}
	else empty = true;
    var post={
		type:'undo',
		id:id,
	};
		
    var ass = $.param(post);
	$.ajax({
		type: "POST",
		url: "basketchange.php",
		data: ass,
		success: function(msg){
			},
			error: function( jqXHR, textStatus, errorThrown ){
				alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
			}
	  });
	if (empty) location.href = 'http://127.0.0.1/edsa-lilii/index.php?type=category&catpath=a:1:{i:0;i:2;}&count=2';
}
function chpos(id,obj)
{
	var x = '#chposcs'+id;
	var block = document.querySelector(x);
	if (block.style.display == 'block')
		clchpos(id)
	else
	{
		block.style.top = obj.getBoundingClientRect().bottom +'px';
		block.style.left = obj.getBoundingClientRect().right+'px';
		block.style.display = 'block';
	}
}
function chosecs(id,form)
{
	var n = 'rchcs'+id;
	var x = form.elements;
	for (var i=0; i<x.length; i++)
	{
		if (x[i].checked) var strval = x[i].value;
	}
	var vals = strval.split(':');
	//alert('count: '+vals[0]+' size: '+vals[1]+'('+vals[2]+') color: '+vals[3]);
	
	n = '#foto'+id;
	x = document.querySelector(n);
	x.innerHTML = '<img src="'+vals[6]+'" style="width:65px;">'
	
	
	n = '#c'+id;
	x = document.querySelector(n);
	x.innerHTML = vals[3]+'<img onClick="chpos('+id+',this)" src="images/select.png" style="width:13px; height:13px;">';
	
	n = '#s'+id;
	x = document.querySelector(n);
	x.innerHTML = vals[1]+'&nbsp('+vals[2]+')<img onClick="chpos('+id+',this)" src="images/select.png" style="width:13px; height:13px;">';
	
	n = '#maxc'+id;
	x = document.querySelector(n);
	var mcount = x.value;
	x.value = vals[0];
	
	n = '#curc'+id;
	x = document.querySelector(n);
	var count = parseInt(x.value);
	if (vals[0] < count)
		chcount(id,vals[0],x);
	else chcount(id,count,x);
	clchpos(id);
	
	var val ={
		sid:vals[4],
		cid:vals[5],
	};
    var post={
		type:'sc',
		id:id,
		val:val
	};
		
    var ass = $.param(post);
	$.ajax({
		type: "POST",
		url: "basketchange.php",
		data: ass,
		success: function(msg){
			},
			error: function( jqXHR, textStatus, errorThrown ){
				alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
			}
	  });
}
function clchpos(id)
{
	var x = '#chposcs'+id;
	var block = document.querySelector(x);
	block.style.display = 'none';
}
function chsize(id,obj)
{
	var strval = obj.options[obj.selectedIndex].value;
	var vals = strval.split(':');
	
	var n = '#maxc'+id;
	var x = document.querySelector(n);
	var mcount = x.value;
	x.value = vals[0];
	
	n = '#curc'+id;
	x = document.querySelector(n);
	var count = parseInt(x.value);
	if (vals[0] < count)
		chcount(id,vals[0],x);
	else chcount(id,count,x);
	
    var post={
		type:'size',
		id:id,
		val:vals[1]
	};
		
    var ass = $.param(post);
	$.ajax({
		type: "POST",
		url: "basketchange.php",
		data: ass,
		success: function(msg){
			},
			error: function( jqXHR, textStatus, errorThrown ){
				alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
			}
	  });
}
function chcolor(id,obj)
{
	var strval = obj.options[obj.selectedIndex].value;
	var vals = strval.split(':');
	
	var n = '#maxc'+id;
	var x = document.querySelector(n);
	var mcount = x.value;
	x.value = vals[0];
	
	n = '#foto'+id;
	x = document.querySelector(n);
	x.innerHTML = '<img src="'+vals[2]+'" style="width:65px;">'
	
	n = '#curc'+id;
	x = document.querySelector(n);
	var count = parseInt(x.value);
	if (vals[0] < count)
		chcount(id,vals[0],x);
	else chcount(id,count,x);
    var post={
		type:'color',
		id:id,
		val:vals[1]
	};
		
    var ass = $.param(post);
	$.ajax({
		type: "POST",
		url: "basketchange.php",
		data: ass,
		success: function(msg){
			},
			error: function( jqXHR, textStatus, errorThrown ){
				alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
			}
	  });
}
function chcount(id,count,obj)
{
	var n = '#curc'+id;
	var x = document.querySelector(n);
	var oldc = parseInt(x.value);
	n ='#maxc'+id;
	var inp = document.querySelector(n);
	var val = inp.value;
	var htm = '';
	if (count == 1)
	{
		htm += '<img src="images/minusIA.png" style="width:10px;">';
	}
	else htm += '<img onClick="chcount('+id+','+(count-1)+',this)" src="images/minus.png" style="width:10px;">';
	htm += '&nbsp'+count+'&nbsp';
	if (count >= val)
	{
		htm += '<img src="images/plusIA.png" style="width:10px;">';
	}
	else htm += '<img onClick="chcount('+id+','+(count+1)+',this)" src="images/plus.png" style="width:10px;">';
	htm += '<input type="hidden" value="'+count+'" name="curc'+id+'" id="curc'+id+'"><input type="hidden" value="'+val+'" name="maxc'+id+'" id="maxc'+id+'">';
	x = obj.parentNode;
	x.innerHTML = htm;
	n = '#cinp'+id;
	inp = document.querySelector(n);
	val = inp.value;
	n = '#cost'+id;
	x = document.querySelector(n);
	var cost = val*count;
	var c1 = Math.floor(cost/1000);
	var c2 = cost - c1*1000;
	htm = '<input type="hidden" value="'+val+'" name="cinp'+id+'" id="cinp'+id+'"><input type="hidden" value="'+cost+'" name="suminp'+id+'" id="suminp'+id+'">'+c1+'&nbsp';
	if (c2 < 100)
	{
		if (c2 < 10)
			htm += '00';
		else htm += '0';
	}
	htm += c2+'&nbspруб.';
	x.innerHTML = htm;
	
	inp = document.querySelector('#sumary');
	var sumval = parseInt(inp.value);
	var dif = val*(count-oldc);
	sumval += dif;
	c1 = Math.floor(sumval/1000);
	c2 = sumval - c1*1000;
	htm = '<input type="hidden" value="'+sumval+'" name="sumary" id="sumary">'+c1+'&nbsp';
	if (c2 < 100)
	{
		if (c2 < 10)
			htm += '00';
		else htm += '0';
	}
	htm += c2+'&nbspруб.';
	x = document.querySelector('#summ');
	x.innerHTML = htm;
    var post={
		type:'count',
		id:id,
		val:count
	};
		
    var ass = $.param(post);
	$.ajax({
		type: "POST",
		url: "basketchange.php",
		data: ass,
		success: function(msg){
			},
			error: function( jqXHR, textStatus, errorThrown ){
				alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
			}
	  });
}
function pay(msg){
	alert(msg);
}
<?php
}
?>
</script>
	
</body>
</html>