<html>
<head>
<meta http-equiv="Content-Type" content = "text/html; charset=utf-8" />
<title> test </title>
<link rel="stylesheet" type="text/css" href="css/mainstyle.css" />
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<?php
session_start();
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
function closelib(&$lib, $path, $i, $toclose, $close)
{
	foreach($lib as &$cat)
	{
		if ($close)
			$cat['open']=0;
		if ($cat['id'] == $path[$i])
		{
			if ($cat['id']==$toclose)
			{
				$close = true;
			}
			if ((isset($path[$i+1]))&&($path[$i+1] != 0)) closelib($cat['sub'], $path, ++$i, $toclose, $close);
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
		if($path[$i] != 0) 
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
	if ($lib != 0)
	{
		foreach($lib as $cat)
		{
			$p = $path;
			$p[] = (int)$cat['id'];
			echo '<div id="lib'.$i.'"><a href="admin.php?type='.$_GET['type'].'&catpath='.serialize($p).'&count=2">';
			if ($i < 3) 
				echo strtoupper($cat['name']).'</a>';
			else
				echo $cat['name'].'</a>';
			if ($_GET['type']=='cat')
			{
				echo '&nbsp<img onClick="undo(\'категорию\',\''.$cat['name'].'\','.(int)$cat['id'].')" src="images/undo.png">&nbsp';
				echo '<a href="admin.php?type=cat&catpath='.serialize($p).'&act=change&count=2">';
				echo '<img src="images/change.png"></a>';
			}
			if (!isset($cat['sub'][0]))
			{
				printlib(0,$i+1, $p);
			}
			else if ($cat['sub'][0]['open'] == 1)
			{
				printlib($cat['sub'],$i+1, $p);
			}
			echo '</div>';
		}
	}
	if ($_GET['type']=='cat')
	{
		if ($lib == 0)
			echo '<br>';
		$path[] = 0;
		echo '<a href="admin.php?type=cat&catpath='.serialize($path).'&act=add&count=2">';
		echo '<div style="';
		if ($i == 1) echo 'position:relative;left:80px;';
		else echo 'position:relative;left:20px;';
		echo 'width:120px;height:17px;padding-top:5px;background:#ffffff;border: 2px dashed grey;color:#000000;text-align:center;text-decoration:none;display:inline-block;font-size:12px;">+ НОВАЯ</div></a><br>';
		if ($lib == 0)
			echo '<div style="height:3px;"></div>';
	}
}
function printpath($lib,$i,$path,&$dest)
{
	foreach($lib as $cat)
	{
		if ($cat['id'] == $path[$i])
		{
			if (isset($path[$i+1]))
			{
				echo $cat['name'].'->';
				printpath($cat['sub'],$i+1,$path,$dest);
			}
			else 
			{
				$dest = array(
					'id' => $cat['id'],
					'name' => $cat['name'],
					'desc' => $cat['desc']);
			}
		}
		else if ($path[$i] == 0)
		{
			$dest = array(
				'id' => 0,
				'name' => ' ',
				'desc' => ' ');
		}
	}
	if ($path[$i] == 0)
	{
		$dest = array(
			'id' => 0,
			'name' => ' ',
			'desc' => ' ');
	}
}
function selpath($lib)
{
	foreach($lib as $cat)
	{
		echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';
		if (isset($cat['sub'][0]))
			selpath($cat['sub']);
	}
}

?>
	<div id="shadow"></div>;
	<div id="log">
	</div>
	<header>
		<img id="logo" src="images/logolilii.png" alt="logo">
		<div id="hmenu">
			<?php
			echo '<a  href="admin.php?type=cat&catpath='.$_GET['catpath'].'">';
			echo '<div style="position:relative;top:10px;float:left;min-width: 70px;">КАТЕГОРИИ &nbsp&nbsp&nbsp </div></a>';
			echo '<a  href="admin.php?type=pos&count=2&catpath='.$_GET['catpath'].'&count=2">';
			echo '<div style="position:relative;top:10px;float:left;min-width: 70px;">ТОВАРЫ</div></a>';?>
		</div>
		<div id="clear"></div>
		<div id="search">
			<img id="line" src="images/line.png" alt="search">
			ПОИСК
		</div>
		<?php
		if(isset($_GET['type']))
		{
			if ($_GET['type'] == 'pos')
			{
				$s = $usr->current_category->prodcount;
				echo '<div id="prodcount">'.$s.' &nbsp товар';
				$h ='| просмотреть <a href="admin.php?type=pos&catpath='.serialize($usr->cur_cat_path).'&count=2">2</a>&nbsp<a href="admin.php?type=pos&catpath='.serialize($usr->cur_cat_path).'&count=6">6</a>';
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
	<?php
	if ($_GET['type'] == 'cat')
		echo '<aside id="aside1" style="width:35%">';
	else echo '<aside id="aside1" style="width:22%">';
		$p = array();
		printlib($usr->library,1,$p);?>
		<div id="info">
			<a href="admin.php?type=info">Информация</a><br>
			<a href="admin.php?type=invest">Инвесторам</a>
		</div>
	</aside>
	<?php
	if ($_GET['type'] == 'cat')
		echo '<article id="article" style="width:65%;display:block;">';
	else echo '<article id="article" style="width:78%;display:block;">';?>
		<div id="content">
			<?php
			if (isset($_GET['type']))
			{
				switch ($_GET['type'])
				{
					case "cat":
					{
						echo '<div style="display:block;font-size:22px;text-align:center;width:100%;border-top: 1px solid black; border-bottom: 1px dashed black"><br>MAIN->';
						$s = unserialize($_GET['catpath']);
						$cat = array();
						printpath($usr->library,0,$s,$cat);
						echo '<br></div>';
						echo '<div style="display:block;width:100%;height:30px;"></div>';
						echo '<div style="display:block;width:100%;text-align:center;">';
						echo '<form name="dirform">';
						echo '<table  border="0" cellspacing="5" cellpadding="5" style="width:370px;margin:auto;">';
						echo '<tr><td align="right" valign="top">ИМЯ: </td><td><input type="text" name="dname" value="'.$cat['name'].'" size="25"></td>';
						echo '<tr><td align="right" valign="top">ОПИСАНИЕ: </td><td><textarea name="ddesc" cols="30" rows="3" wrap="physical">'.$cat['desc'].'</textarea></td>';
						echo '<tr><td align="right" valign="top">КАТЕГОРИЯ: </td><td><select>';
						selpath($usr->library);
						echo '</select></td></table>';
						echo '<div style="display:block;width:100%;height:30px;"></div>';
						echo '<div onClick="catchange()" style="width:200px;height:29px;padding-top:15px;background:#ffffff;border:2px solid grey;color:#000000;text-align:center;text-decoration:none;display:inline-block;font-size:12px;">ПРИМЕНИТЬ</div>';
						
						if(isset($_GET['act']))
						{
							if ($_GET['act'] == "add")
							{
								
							}
							else
							{
								
							}
						}
						else
						{
							
						}
						echo'</form>';
						echo '</div>';
						break;
					}
					case "pos":
					{
						if ($_GET['count'] == 2)
						{
							$left = true;
							foreach($usr->current_category->products as $prod)
							{
								if ($left) echo '<div id="prodstr">';
								echo'<a href="admin.php?type=product&prodid='.$prod->info['id'].'"><div id="prod';
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
								echo '</div>';
						}
						else
						{
							echo '<div id="c_info">Раздел в разработке</div>';
						}
						break;
					}
					case "product":
					{
						echo '<div id="prm"><div id="prupbar"><div style="float:left;">';
						echo '<a href="admin.php?type=category&catpath='.serialize($usr->cur_cat_path).'&count=2">';
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
									echo '<a href="admin.php?type=product&prodid='.$i.'"><img src="images/prla.png" border="0"></a>';
								}
								$chk = true;
							}
							else if ($chk)
							{
								echo '<a href="admin.php?type=product&prodid='.$prod->info['id'].'"><img src="images/prra.png" border="0"></a>';
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
						foreach($p->colors as $color)
						{
							if ($color['val'] == $col)
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
								if (!$left) echo '</div>';
							}
						}
						echo '</div></div>';
						echo '<div id="prinfo">';
						echo '<span style="padding-left: 29px;font-size:15px;">'.strtoupper($p->info['desc']).'</span><br><br><br>';
						$c = (int)($p->info['cost'] / 1000);
						echo '<span style="padding-left: 29px;font-size:14px;font-weight:bold;">'.$c.' '.($p->info['cost'] - $c*1000).' руб. </span><br><br><br>';?>
						<div id="cpar" onClick="openWin('#compound','block','#cpar')" style="padding-left: 29px;font-size:10px;float:left;">СОСТАВ И УХОД&nbsp&nbsp&nbsp|</div>
						<div onClick="openWin('#send','block','#cpar')" style="font-size:10px;float:left;">&nbsp&nbsp&nbspОТПРАВКА&nbsp&nbsp&nbsp|</div>
						<div onClick="openWin('#back','block','#cpar')" style="font-size:10px;float:left;">&nbsp&nbsp&nbspВОЗВРАТ</div><br><br><br><br><br><br>
						<div onClick="openWin('#compound','none','#cpar')" id="compound">11111111</div>
						<div onClick="openWin('#send','none','#cpar')" id="send">2222222222</div>
						<div onClick="openWin('#back','none','#cpar')" id="back">333333333333</div>
						<div style="display:table-row;width:100%;padding-left: 29px;"><?php
						foreach($p->colors as $color)
						{
							echo '<a href="admin.php?type=product&prodid='.$p->info['id'].'&color='.$color['val'].'">';
							echo '<div style="position:relative;left:38px;float:left;text-align:center;width:40px;"><img src="'.$color['main'].'" width="22";><br>';
							echo '<img src="images/col.png"><br>';
							echo '<span style="font-size:10px">'.$color['name'].'</span></div></a>&nbsp&nbsp&nbsp';
						}
						echo '</div><br><br><br>';
						echo '<span style="padding-left: 29px;font-size:12px;">ВЫБЕРИТЕ РАЗМЕР</span><br><br>';
						echo '<div style="position:relative;left:29px;width:65%;height:1px;background:#000000;"></div><br>';
						echo '<div id="sizes" style="display:table-row;width:100%;padding-left: 29px;">';
						$pad=0;
						foreach($p->sizes as $size)
						{
							echo '<div onClick="changesize('.$size['id'].')"style="position:relative;left:38px;padding-bottom:8px;float:top;font-size:10px">'.$size['size'].'&nbsp(RU 40/42)'.$size['desc'].'</div>';
							$pad += 8;
						}
						echo '</div><div style="height:12px;"></div>';?>
						<div style="position:relative;left:29px;width:65%;height:14px;border-top: 1px dashed #000;"></div>
						<a href="#nul" onClick="window.open('sizeman.php ','','Toolbar=1,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=550,Height=400');"><span style="padding-left: 29px;font-size:10px;">СПРАВОЧНИК ПО РАЗМЕРАМ<span></a>
						<div style="position:relative;left:29px;width:65%;height:14px;border-bottom: 1px solid #000;"></div>
						<div style="position:relative;left:29px;width:100%;height:25px;"></div>
						<div style="position:relative;left:29px;width:200px;height:29px;padding-top:15px;background:#000000;border:none;color:#ffffff;text-align:center;text-decoration:none;display:inline-block;font-size:12px;">ДОБАВИТЬ В КОРЗИНУ</div>
						<?php
						echo '</div>';
						break;
					}
					case "basket":
					{
						break;
					}
					case "info":
					{
						echo '<div id="c_info">Раздел в разработке</div>';
						break;
					}
					case "invest":
					{
						echo '<div id="c_invest">Раздел в разработке</div>';
						break;
					}
				}
			}
			else
			{
				
			}
			?>
		</div>
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
function changesize(id){
	alert(id);
}
function openWin(W,S,P){
	var left=document.querySelector(P).getBoundingClientRect().left,
		top=document.querySelector(P).getBoundingClientRect().top;
	document.querySelector(W).style.top = top + 'px';
	document.querySelector(W).style.left = left + 29 + 'px';
	document.querySelector(W).style.display = S;
	document.querySelector('#shadow').style.display = S;
}
function undo(type, name, id){
	var rez = confirm("Вы действительно хотите удалить "+type+" "+name+"?");
	if (rez)
	{
		switch(type)
		{
			case 'категорию':{
				alert("категория "+name+" удалена!");
				break;
			}
			case 'продукт':{
				alert("продукт "+name+" удален!");
				break;
			}
		}
	}
	location.reload();
}
</script>
	
</body>
</html>
