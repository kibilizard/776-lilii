<?php
	if (isset($_GET['pid']) AND !isset($_GET['width']))
    {
    	echo "<script language='javascript'>\n";
    	echo "  location.href=\"${_SERVER['SCRIPT_NAME']}?${_SERVER['QUERY_STRING']}"
        . "&width=\" + screen.width + \"&height=\" + screen.height;\n";
    	echo "</script>\n";
    	exit();
    }
	$db_hostname = 'localhost';
	$db_database = 'ck74682_776';
	$dsn = "mysql:host=$db_hostname;dbname=$db_database;charset=utf8";
	$opt = array(
		PDO::ATTR_ERRMODE 			 => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
    $pdo = new PDO($dsn, 'ck74682_776', 'fynjy776', $opt);
	$stmt = $pdo->query('SELECT id, foto, style, cost, crdate, colors FROM positions');
	$lib = array();
	while ($row = $stmt->fetch())
	{
		$lib[]=array(
			'id' => $row['id'],
			'foto' => $row['foto'],
			'style' => $row['style'],
			'cost' => $row['cost'],
			'date' => $row['crdate'],
          	'colors' => $row['colors'],
			'sizes' => array(),
			'fotos' => array());
	}
	$stmt = $pdo->prepare('SELECT id, value, descript FROM sizes WHERE posid = ? ORDER BY orderid');
	$stmt2 = $pdo->prepare('SELECT id, path, style FROM fotos WHERE posid = ?');
	foreach($lib as &$pos)
	{
		$stmt->execute(array($pos['id']));
		while ($row = $stmt->fetch())
		{
			$pos['sizes'][]=array(
				'sid' => $row['id'],
				'val' => $row['value'],
				'desc' => $row['descript']);
		}
		$stmt2->execute(array($pos['id']));
		while ($row = $stmt2->fetch())
		{
			$pos['fotos'][]=array(
				'fid' => $row['id'],
				'path' => $row['path'],
				'style' => $row['style']);
		}
	}
	if (isset($_GET['pid']))
    {
      	foreach ($lib as $prod)
        {
          	if ($prod['id'] == $_GET['pid'])
              	$curprod = $prod;
        }
    }
?> 
<!DOCTYPE html>
<html>
<head> 
<meta http-equiv="Content-Type" content = "text/html; charset=windows-1251" />
<meta name="interkassa-verification" content="447a783885235c080c4362c58100e938" />
  <meta name="telderi" content="8390d7fe7481aa24f4867ac5a919c938" />
<meta name=viewport content="width=device-width, initial-scale=1">
<title> 776 Discount Store</title>
 <!--[if lt IE 9]>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js">IE7_PNG_SUFFIX=".png";</script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="css/776style.css" />
<script type="text/javascript" src="jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
  <script type="text/javascript" src="//vk.com/js/api/openapi.js?124"></script>

<script type="text/javascript">
  VK.init({apiId: 5559530, onlyWidgets: true});
</script>
  
</head>
<body link="#000000" vlink="#000000" alink="#000000">
  <header style="color:white;">
  <H1>Модная и стильная одежда для девушек по низким ценам</H1><br><br>
  <h2>Красивая и удобная одежда для стильных девушек. Прямые поставки из КНР. Купить стильную одежду.</h2>
  </header>
  
  
  
<div id="maininf" onMouseOver="showupbar();" style="position: fixed;Z-index: 110;display: none;width: 500px;top: 135px;left: 0px;right: 0px;margin: auto;background: #ffffff;padding: 15px 10px 0px;font-size:15px;">
  <p style="font-size:20px;text-align:center">776 Discount store</p>
  <p>776 занимается поставками женской одежды из КНР.</p>
  <p>Мы работаем без посредников, напрямую с производителями, благодаря этому у нас самые низкие цены. Мы работаем только с проверенными Китайскими производителями.</p>

  <p>Наш магазин работает по 100% предоплате, поскольку для доставки мы используем международную авиапочту, в которой наложенный платеж невозможен технически. Несем полную финансовую ответственность перед нашими покупателями.</p>

  <p>776 осуществляет бесплатную доставку по всему миру.</p>
    <div style="width:100%;height:60px;padding-top:30px;">
      <div onClick="clmaininf()" style="cursor:pointer; position:relative; left:145px; height:20px; padding-top:5px; padding-left:70px; width:125px;background:#000000; color:#ffffff;">ЗАКРЫТЬ
        </div>
    </div>
  </div>
  
<div id="howtobuy" onMouseOver="showupbar();" style="position: fixed;Z-index: 110;display: none;width: 500px;top: 135px;left: 0px;right: 0px;margin: auto;background: #ffffff;padding: 15px 10px 0px;font-size:15px;">
  
  <div id="htb1" style="display:block;">
    <div style="width:100%;height:30px;">
      <div onClick="htbup(1);" style="cursor:pointer; position:relative; left:290px; top:0px; height:20px; padding-top:5px; padding-left:45px; width:100px;background:#000000; color:#ffffff;">ДАЛЕЕ
      </div>
    </div>
    <div style="width:100%;padding-top:10px;">
      <img src="images/1.png" style="width:490px;">
    </div>
    <div style="width:100%;padding-top:5px;">
      <p>На Исходной странице вы видите множество фотографий товаров предоставляемых 776.</p>
    </div>
  </div>
  
  <div id="htb2" style="display:none;">
    <div style="width:100%;height:25px;padding-top:10px;">
      <div onClick="htbdown(2);" style="cursor:pointer; position:relative; left:55px; height:20px; padding-top:5px; padding-left:40px; width:100px;background:#000000; color:#ffffff;">НАЗАД
        </div>
      <div onClick="htbup(2);" style="cursor:pointer; position:relative; left:290px; top:-25px; height:20px; padding-top:5px; padding-left:45px; width:100px;background:#000000; color:#ffffff;">ДАЛЕЕ
        </div>
    </div>
    <div style="width:100%;padding-top:10px;">
      <img src="images/2.png" style="width:490px;">
    </div>
    <div style="width:100%;padding-top:5px;">
      <p>Наведите на понравившийся товар, вы увидите его стоимость и доступные размеры. </p><p style="margin-top:-17px;">Наведя на размер вы увидите его описание. </p><p style="margin-top:-17px;">Клик на размере приведет вас на форму заказа данного товара. </p><p style="margin-top:-17px;">Клик на самом товаре - в галерею с дополнительными фото.</p>
    </div>
  </div>
  
  <div id="htb3" style="display:none;">
    <div style="width:100%;height:25px;padding-top:10px;">
      <div onClick="htbdown(3);" style="cursor:pointer; position:relative; left:55px; height:20px; padding-top:5px; padding-left:40px; width:100px;background:#000000; color:#ffffff;">НАЗАД
        </div>
      <div onClick="htbup(3);" style="cursor:pointer; position:relative; left:290px; top:-25px; height:20px; padding-top:5px; padding-left:45px; width:100px;background:#000000; color:#ffffff;">ДАЛЕЕ
        </div>
    </div>
    <div style="width:100%;padding-top:10px;">
      <img src="images/3.png" style="width:490px;">
    </div>
    <div style="width:100%;padding-top:5px;">
      <p>В галерее можно листать фото стрелочками влево и вправо на клавиатуре, а так же кликом на навигационных элементах.</p><p style="margin-top:-17px;">Кликнув на крестик вы вернетесь на основную страницу.</p><p style="margin-top:-17px;">Наведя на кнопку "заказать" увидите доступные размеры.</p><p style="margin-top:-17px;">Наведя на размер увидите его описание.</p><p style="margin-top:-17px;">Клик на размере приведет вас на форму заказа товара.</p>
    </div>
  </div>
  
  <div id="htb4" style="display:none;">
    <div style="width:100%;height:25px;padding-top:10px;">
      <div onClick="htbdown(4);" style="cursor:pointer; position:relative; left:55px; height:20px; padding-top:5px; padding-left:40px; width:100px;background:#000000; color:#ffffff;">НАЗАД
        </div>
    </div>
    <div style="width:100%;padding-top:10px;">
      <img src="images/4.png" style="width:490px;">
    </div>
    <div style="width:100%;padding-top:5px;">
      <p>Правильно заполните все поля формы, выберите цвет если есть другие цвета, и нажмите кнопку "оплатить". Вы попадете на страницу оплаты системы "интеркасса" где сможете выбрать удобный для вас способ оплаты и заказать товар.</p>
      <p style="margin-top:-10px;">После оплаты вам придет письмо с подтверждением заказа на указанный в форме почтовый адресс. На этот же адресс придет письмо с номером для отслеживания заказа после его формирования.</p>
    </div>
  </div>
  
    <div style="width:100%;height:60px;padding-top:5px;">
      <div id="clhtb" onClick="clhtb()" style="cursor:pointer; position:relative; left:145px; height:20px; padding-top:5px; padding-left:70px; width:125px;background:#000000; color:#ffffff;">ЗАКРЫТЬ
        </div>
    </div>
  </div>
  
<div id="delivery" onMouseOver="showupbar();" style="position: fixed;Z-index: 110;display: none;width: 500px;top: 135px;left: 0px;right: 0px;margin: auto;background: #ffffff;padding: 15px 10px 0px;font-size:15px;">
  <p style="font-size:20px;text-align:center">Доставка:</p>
  <p>Сроки на доставку рассчитываются следующим образом:</p>
  <p>20-35 дней формирование и около 20-30 дней сама доставка.</p> 
  <p>Формирование включает в себя: сбор заказов фабрикой, отшив продукции, упаковка и отправка товара.</p>
  <p>После формирования предоставляем номер для отслеживания.</p>
  <p>Доставка из Китая бесплатная.</p>
  <p style="font-size:20px;text-align:center">Возврат:</p>
  <p>Мы предоставляем возможность вернуть товары без лишних хлопот в течение 30 дней с момента получения заказа</p>

  <p>По вопросам возврата Вы всегда можете связаться с консультантом.</p>
    <div style="width:100%;height:60px;padding-top:30px;">
      <div onClick="cldelivery()" style="cursor:pointer; position:relative; left:145px; height:20px; padding-top:5px; padding-left:70px; width:125px;background:#000000; color:#ffffff;">ЗАКРЫТЬ
        </div>
    </div>
  </div>
  
<div id="invest" onMouseOver="showupbar();" style="position: fixed;Z-index: 110;display: none;width: 500px;top: 135px;left: 0px;right: 0px;margin: auto;background: #ffffff;padding: 15px 10px 0px;font-size:15px;">
      <p>Приглашаем к сотрудничеству якорных инвесторов. 
        Предлагаем инвестировать в выгодный сектор - производство одежды.</p> 

      <p>Предлагаем доходность: 20-30% в месяц от вложенной суммы.</p>

      <p>Возможность вернуть деньги в любой момент.</p>
      <p>Рассмотрим любые суммы.</p>
    <p>Для более детального диалога пишите: <a href="http://vk.com/write214689053" style="color:#0000ff;" target="_blank">Антон Гранд</a></p>
    <div style="width:100%;height:60px;padding-top:30px;">
      <div onClick="clinvest()" style="cursor:pointer; position:relative; left:145px; height:20px; padding-top:5px; padding-left:70px; width:125px;background:#000000; color:#ffffff;">ЗАКРЫТЬ
        </div>
    </div>
  </div>
  
<div id="partner" onMouseOver="showupbar();" style="position: fixed;Z-index: 110;display: none;width: 500px;top: 135px;left: 0px;right: 0px;margin: auto;background: #ffffff;padding: 15px 10px 0px;font-size:15px;">
    <p>В связи с активным расширением компании находимся в поиске надежных партнеров в городах России.</p>

    <p>Различные варианты сотрудничества: магазин, он-лайн магазин, франшиза.</p>

    <p>Предлагаем низкие цены, взаимовыгодные условия, постоянную рекламную и техническую поддержку. Возможность хорошей наценки и прибыли.</p>

    <p>От партнеров требуется лишь выполнение технической части и осуществление продаж: доставка, хранение товара, учет остатков. </p>
    <p>Остальные бизнес-процессы, в том числе поиск клиентов, мы берем на себя.</p>
    <p>Для более детального диалога пишите: <a href="http://vk.com/write214689053" style="color:#0000ff;" target="_blank">Антон Гранд</a></p>
    <div style="width:100%;height:60px;padding-top:30px;">
      <div onClick="clpartner()" style="cursor:pointer; position:relative; left:145px; height:20px; padding-top:5px; padding-left:70px; width:125px;background:#000000; color:#ffffff;">ЗАКРЫТЬ
        </div>
    </div>
</div>
  
<div id="kontact" onMouseOver="showupbar();" style="position: fixed;Z-index: 110;display: none;width: 500px;top: 135px;left: 0px;right: 0px;margin: auto;background: #ffffff;padding: 15px 10px 0px;font-size:15px;">
  <table cellspacing="5" style="width:300px;margin:auto;padding-left:15px;">
        <tr><td>EMAIL:</td><td>    	suport@776store.com</td></tr>
        <tr><td>ТЕЛЕФОН:</td><td>    +7 (383) 2999-776</td></tr>
    <tr><td>ВКонтакте:</td><td>    <a href="https://vk.com/sh776" style="color:#0000ff;" target="_blank">776 Discount store</a></td></tr>
        <tr><td>АДРЕСС:</td><td>    Галущака 2а. 405</td></tr>
      </table>
    <div style="width:100%;height:60px;padding-top:30px;">
      <div onClick="clkontact()" style="cursor:pointer; position:relative; left:145px; height:20px; padding-top:5px; padding-left:70px; width:125px;background:#000000; color:#ffffff;">ЗАКРЫТЬ
        </div>
    </div>
  </div>
  
<div id="buy" onMouseOver="showupbar();" style="position: fixed;Z-index: 110;display: none;width: 500px;top: 135px;left: 0px;right: 0px;margin: auto;background: #ffffff;padding: 15px 10px 0px;font-size:15px;">
  <div id="ptobuy" style="width:100%; height:120px; padding: 5px 20px 0px;font-size:12px;"></div>
  <form id="infoform">
    <table style="border-spacing:1px;">
      <tr><td align="right">ФИО</td><td><input name="fio" type="text" size="25"></td><td style="font-size:14px;"><p style="margin-top:-10px;">полностью, латинскими буквами</p><p style="color:#585858;margin-top:-17px;margin-bottom:-10px;">exp: Ivanov Ivan Ivanovitch</p></td></tr>
      <tr><td align="right">АДРЕС</td><td><input name="adr" type="text" size="25"></td><td style="font-size:14px;"><p style="margin-top:-10px;">улица, дом\корпус, квартира.</p><p style="color:#585858;margin-top:-17px;margin-bottom:-10px;">exp: ul. Lenina d. 46\1 kv. 37</p></td></tr>
      <tr><td align="right">ГОРОД</td><td><input name="cyt" type="text" size="25"></td><td style="font-size:14px;"><p style="margin-top:-10px;">латинскими буквами</p><p style="color:#585858;margin-top:-17px;margin-bottom:-10px;">exp: Novosibirsk</p></td></tr>
      <tr><td align="right">РЕГИОН</td><td><input name="reg" type="text" size="25"></td><td style="font-size:14px;"><p style="margin-top:-10px;">латинскими буквами</p><p style="color:#585858;margin-top:-20px;margin-bottom:-10px;">exp: Novosibirskaya obl.</p></td></tr>
      <tr><td align="right">ИНДЕКС</td><td><input name="ind" type="text" size="25"></td><td><p style="color:#585858;margin-top:-5px;margin-bottom:-5px;">exp: 659000</p></td></tr>
      <tr><td align="right">СТРАНА</td><td><input name="cnt" type="text" size="25"></td><td style="font-size:14px;"><p style="margin-top:-10px;">латинскими буквами</p><p style="color:#585858;margin-top:-17px;margin-bottom:-10px;">exp: Russian Federation</p></td></tr>
      <tr><td align="right">ТЕЛЕФОН</td><td><input name="phn" type="text" size="25"></td><td style="font-size:14px;"><p style="color:#585858;margin-top:-5px;margin-bottom:-5px;">exp: 89123456789</p></td></tr>
      <tr><td align="right">EMAIL</td><td><input name="eml" type="text" size="25"></td><td style="font-size:14px;"><p style="color:#585858;margin-top:-5px;margin-bottom:-5px;">exp: example@test.ru</p></td></tr>
    </table>
	<input type="hidden" name="sum"/>
	<input type="hidden" name="pid"/>
	<input type="hidden" name="sid"/>
	<input type="hidden" name="col"/>
    </form>
  <div style="display:none;">
  <form id="payment" name="payment" method="post" action="https://sci.interkassa.com/" enctype="utf-8">
	<input type="hidden" name="ik_co_id" value="57846e623c1eaf1a118b4568" />
	<input type="hidden" name="ik_pm_no" value="ID_4233" />
	<input type="hidden" name="ik_am" value="100.00" />
	<input type="hidden" name="ik_cur" value="RUB" />
	<input type="hidden" name="ik_desc" value="Event Description" />
	<input type="hidden" name="ik_exp" value="2016-07-14" />
	<input type="hidden" name="ik_enc" value="utf-8" />
        <input type="submit" value="Pay">
    </form></div>
    <div id="validateerror" style="width:100%;color:#b40404;"></div>
    <div style="width:100%;height:60px;padding-top:30px;">
      <div onClick="clbuy()" style="cursor:pointer; position:relative; left:55px; height:20px; padding-top:5px; padding-left:40px; width:100px;background:#000000; color:#ffffff;">ОТМЕНА
        </div>
      <div onClick="pay()" style="cursor:pointer; position:relative; left:240px; top:-25px; height:20px; padding-top:5px; padding-left:45px; width:120px;background:#000000; color:#ffffff;">ОПЛАТИТЬ
        </div>
    </div>
  </div>
<div id="shadow"></div>
  
  <div id="shadowblack" onMouseOver="showupbar();"<?php 
	if (isset($_GET['pid']))
      echo ' style="display:block;"';
?>></div>
  
<?php
	if (isset($_GET['pid']))
    {
      	$down = 'none';
      	$curfoto = 'none';
      	$up = 'none';
      	if (isset($_GET['fid']))
        {
          	$check = false;
          	foreach($curprod['fotos'] as $foto)
            {
              	if ($check)
                {
                  	$up = $foto['fid'];
                  	break;
                }
              	if ($foto['fid'] == $_GET['fid'])
                {
                  	$check = true;
                  	$curfoto = $foto['path'];
                }
              	else $down = $foto['fid'];
            }
        }
      	else 
        {
          	$curfoto = $curprod['foto'];
          	$up = $curprod['fotos'][0]['fid'];
          	$down = $curprod['fotos'][count($curprod['fotos'])-1]['fid'];
        }
      	$Sleft = 0;
      	$Swidth = 12;
      	foreach ($curprod['sizes'] as $size)
        {
          	switch($size['val'])
            {
             	case 'S':
             	case 'L': $Swidth += 25; break;
             	case 'M': 
             	case 'XS':
             	case 'XL': $Swidth += 30; break;
             	case 'XXL': $Swidth += 50; break;
             	case 'XXXL': $Swidth += 70; break;
             	case 'ONE SIZE': $Swidth += 100; break;
            }
        }
      	if ($Swidth < 140) 
        {
          	$Sleft = (int)((140 - $Swidth)/2);
          	$Swidth = 140;
        }
      	$size = getimagesize($curfoto);
      	$h = $_GET['height'] - 225;
      	$x = $h / $size[1];
      	$fotoW = (int)($size[0]*$x);
      	$clleft = (int)($_GET['width']/2 + $fotoW/2);
      echo '<div id="curprod" style="position: fixed;Z-index: 110;display: block;width:'.($fotoW+80).'px;top: 135px;left: 0px;right: 0px;margin: auto;font-size:15px;">';
      echo '<a href="http://776store.com/index.php?pid='.$_GET['pid'].'&width='.$_GET['width'].'&height='.$_GET['height'].'';
      if ($down != 'none')
      	echo '&fid='.$down;
      echo '"><div style="height:'.$h.'px; width:40px; background: #ffffff;opacity:0.8;filter: alpha(opacity=80);display:block;float:left;">';
      echo '<img src="images/navidown.png" style="border:0px;position:relative; top:'.((int)($h/2)-25).'px;">';
      echo '</div></a>';
      echo '<div id="cpbuy" onMouseEnter="openS();" onMouseLeave="closeS();" style="position:fixed; width:'.$Swidth.'px; top:135px; left:0px; right:0px; margin:auto; background:#ffffff; text-align:center; opacity:0.9;filter: alpha(opacity=90); font-size:20px; padding: 2px 5px;"><div id="vspom1" style="cursor:pointer;">ЗАКАЗАТЬ</div><div id="cpsizes" style="display:none; position:relative; left:'.$Sleft.'px;">';
      foreach ($curprod['sizes'] as $size)
      {
        echo '<div onMouseEnter="cpsdshow(\''.$size['desc'].'\')" onMouseLeave="cpsdhide()" onClick="buy('.$_GET['pid'].', '.$curprod['cost'].', '.$size['sid'].', \''.$size['val'].'\', \''.$size['desc'].'\', \''.$curfoto.'\', \''.$curprod['colors'].'\')" style="cursor:pointer; float:left;">';
        if ($curprod['sizes'][0] == $size) echo '|';
        echo '&nbsp'.$size['val'].'&nbsp|</div>';
      }
      echo '</div><div id="cpsdesc" style="float:top; width:100%; text-align:center; font-size:14px;"></div>';
      echo '</div>';
      echo '<a href="http://776store.com/"><img src="images/close.png" style="border:0px;position:fixed; top:135px; left:'.$clleft.'px; opacity:0.7;filter: alpha(opacity=70); cursor:pointer; Z-index:115;"></a>';
      echo '<img src="'.$curfoto.'" style="width:'.$fotoW.'px;float:left;">';
      echo '<a href="http://776store.com/index.php?pid='.$_GET['pid'].'&width='.$_GET['width'].'&height='.$_GET['height'].'';
      if ($up != 'none')
      	echo '&fid='.$up;
      echo '"><div style="height:'.$h.'px; width:40px; background: #ffffff;opacity:0.8;filter: alpha(opacity=80);display:block;float:left;">';
      echo '<img src="images/navi.png" style="border:0px;position:relative; top:'.((int)($h/2)-25).'px;">';
      echo'</div></a>';
      echo '</div>';
    }

	foreach($lib as $prod)
	{
		$arr = explode(";",$prod['style']);
		if ($prod['style']=='')
		{
			$style = getimagesize($prod['foto']);
			$arr[0] = 'width:'+$style[0];
			$arr[1] = 'height:'+$style[1];
		}
      $x = explode(":",$arr[0]);
      $y = explode(":",$arr[1]);
		echo '
			<div id="prod'.$prod['id'].'" onMouseEnter="openposit('.$prod['id'].');" style="position:absolute;Z-index:90;'.$prod['style'].'">
			<img id="img'.$prod['id'].'" src="'.$prod['foto'].'" style="'.$arr[0].';'.$arr[1].';">
			</div>
            <div id="block'.$prod['id'].'" onclick="blockclick('.$prod['id'].')" style="display:none;">';
			echo '<div id="siz'.$prod['id'].'" onMouseEnter="clearTimeout(timer);" onMouseLeave="timerstart('.$prod['id'].');" style="position: absolute;Z-index: 101;';
      		if ($prod['style']=='')
              echo'top:0px;left:0px;width:'.$style[0].'px;height:'.$style[1].'px">';
      else echo $prod['style'].'">';
      		echo '<table border="0" cellspacing="5" cellpadding="5" style="width: 200px;margin: auto;position: relative;top: '.(int)($y[1]/2 - 20).'px;">
            <tr><td align ="center">';
			$c = (int)($prod['cost']/1000);
			if ($c>0)
              echo '<div style="font-size:20px;">&nbsp'.$c.'&nbsp';
      		else echo '<div style="font-size:20px;">&nbsp';
      		$costhun = $prod['cost']-$c*1000;
      		if ($costhun < 100)
            {
              if ($costhun < 10)
                echo '00';
              else echo '0';
            }
			echo ($costhun).'&nbsp rub.</div>';
     		echo '</td></tr><tr><td align ="center">';
      $cs = count($prod['sizes']);
      if ($prod['sizes'][0]['val'] == "ONE SIZE") $cs1 = 90 - (int)($cs*70/2);
      else $cs1 = 90 - (int)($cs*25/2);
      echo '<div style="position:relative;left:'.$cs1.'px;">';
			foreach($prod['sizes'] as $siz)
			{
              echo '<div onMouseOver="showsdesc('.$prod['id'].',\''.$siz['desc'].'\');" onClick="buy('.$prod['id'].','.$prod['cost'].','.$siz['sid'].',\''.$siz['val'].'\',\''.$siz['desc'].'\',\''.$prod['foto'].'\',\''.$prod['colors'].'\',event);" onMouseOut="clsdesc('.$prod['id'].');" style="cursor:pointer;float:left;Z-index:103;">';
              if ($siz == $prod['sizes'][0]) echo '|';
              echo '&nbsp'.$siz['val'].'&nbsp|</div>';
			}
      echo'</div>';
      echo '</td></tr><tr><td align="center">';
      echo '<div id="sdesc'.$prod['id'].'" style="padding-top:10px;padding-left:5px;font-size:12px;"></div>';
			echo '</div>';
      echo '</td></tr></table></div>';
      echo '</div>';
	}
?>
  <div onMouseEnter="showupbar();" style="position:fixed;top:0px;cursor:pointer;width:100%;height:5px;Z-index:170;background:#ffffff;<?php if (isset($_GET['pid'])) echo 'display:none;';?>"></div>
  
<div id="upbar" <?php
	if (!isset($_GET['pid']))
      	echo 'onMouseOver="showupbar();" onMouseOut="hideupbar();"';
?> style="position:fixed;top:0px;left:0px;Z-index:170;display:block;background:#ffffff;opacity:0.8;filter: alpha(opacity=80);height:135px;width:100%;">
  
	<div style="width:230px;height:100%;float:left;">
		<img src="images/logo776.png" style="width:150px;height:75px;position:relative;top:30px;left:50px;">
    </div>
  
  	<div style="height:75px;padding-top:60px; padding-left:100px;font-size:20px;">
      <div onClick="maininf();" style="cursor:pointer;position:relative;left:20px;float:left;color:black;">&nbspО НАС&nbsp</div>
      <div onClick="howtobuy();" style="cursor:pointer;position:relative;left:40px;float:left;color:black;">&nbspИНСТРУКЦИИ ПО ПОКУПКЕ&nbsp</div>
      <div onClick="delivery();" style="cursor:pointer;position:relative;left:60px;float:left;color:black;">&nbspДОСТАВКА/ВОЗВРАТ&nbsp</div>
      <div onClick="invest();" style="cursor:pointer;position:relative;left:80px;float:left;color:black;">&nbspИНВЕСТОРАМ&nbsp</div>
      <div onClick="partner();" style="cursor:pointer;position:relative;left:100px;float:left;color:black;">&nbspПАРТНЕРАМ&nbsp</div>
      <div onClick="kontact();" style="cursor:pointer;position:relative;left:120px;float:left;color:black;">&nbspКОНТАКТЫ&nbsp</div>
    </div>
  
</div>
<script type="text/javascript">
  var ver = "used: ";
  if (!('querySelector' in document))
    ver+= "ie 9+ ; ";
  if (!('localStorage' in window))
	ver+= "ie 8+ ; ";
  if (!('addEventListener' in window))
    ver+= "(aEL)ie 8 + ; ";
  if (!('matchMedia' in window))
  	ver+= "ie 10+";
  //alert(ver);
<?php
if (isset($_GET['pid']))
{
     $down = 'none';
     $up = 'none';
     if (isset($_GET['fid']))
     {
         $check = false;
         foreach($curprod['fotos'] as $foto)
         {
             if ($check)
             {
                 $up = $foto['fid'];
                 break;
             }
             if ($foto['fid'] == $_GET['fid'])
             {
                 $check = true;
             }
             else $down = $foto['fid'];
         }
     }
     else 
     {
         $up = $curprod['fotos'][0]['fid'];
         $down = $curprod['fotos'][count($curprod['fotos'])-1]['fid'];
     }
  	 echo 'var downlink = "http://776store.com/index.php?pid='.$_GET['pid'].'&width='.$_GET['width'].'&height='.$_GET['height'];
  	 if ($down != 'none') echo '&fid='.$down;
  	 echo '";';
  	 echo 'var uplink = "http://776store.com/index.php?pid='.$_GET['pid'].'&width='.$_GET['width'].'&height='.$_GET['height'];
  	 if ($up != 'none') echo '&fid='.$up;
  	 echo '";';

}
?>var debilnuiNedoBrauzer=0;
  if ((!('querySelector' in document))||(!('localStorage' in window))||(!('addEventListener' in window))||(!('matchMedia' in window)))
  {
    debilnuiNedoBrauzer=1;
    document.getElementById('maininf').style.width="600px";
    document.getElementById('delivery').style.width="600px";
    document.getElementById('howtobuy').style.width="700px";
   	document.getElementById('clhtb').style.left = "245px";
    
   	document.getElementById('htb1').innerHTML = "<div style=\"width:100%;height:30px;\"><div onClick=\"htbup(1);\" style=\"cursor:pointer; position:relative; left:300px; top:0px; height:20px; padding-top:5px; padding-left:45px; width:100px;background:#000000; color:#ffffff;\">ДАЛЕЕ</div></div><div style=\"width:100%;padding-top:10px;\"><img src=\"images/1.png\" style=\"position:relative;left:105px;width:490px;\"></div><div style=\"width:100%;padding-top:5px;\"><p>На Исходной странице вы видите множество фотографий товаров предоставляемых 776.</p> </div></div>";
    
   	document.getElementById('htb2').innerHTML = "<div style=\"width:100%;height:25px;padding-top:10px;\"><div onClick=\"htbdown(2);\" style=\"cursor:pointer; position:relative; left:55px; height:20px; padding-top:5px; padding-left:40px; width:100px;background:#000000; color:#ffffff;\">НАЗАД</div><div onClick=\"htbup(2);\" style=\"cursor:pointer; position:relative; left:440px; top:-25px; height:20px; padding-top:5px; padding-left:45px; width:100px;background:#000000; color:#ffffff;\">ДАЛЕЕ</div></div><div style=\"width:100%;padding-top:10px;\"><img src=\"images/2.png\" style=\"position:relative;left:105px;width:490px;\"></div><div style=\"width:100%;padding-top:5px;\"><p>Наведите на понравившийся товар, вы увидите его стоимость и доступные размеры. Наведя на размер вы увидите его описание. Клик на размере приведет вас на форму заказа данного товара. Клик на самом товаре - в галерею с дополнительными фото.</p></div>";
    
    document.getElementById('htb3').innerHTML = "<div style=\"width:100%;height:25px;padding-top:0px;\"><div onClick=\"htbdown(3);\" style=\"cursor:pointer; position:relative; left:55px; height:20px; padding-top:5px; padding-left:40px; width:100px;background:#000000; color:#ffffff;\">НАЗАД</div><div onClick=\"htbup(3);\" style=\"cursor:pointer; position:relative; left:440px; top:-25px; height:20px; padding-top:5px; padding-left:45px; width:100px;background:#000000; color:#ffffff;\">ДАЛЕЕ</div></div><div style=\"width:100%;padding-top:10px;\"> <img src=\"images/3.png\" style=\"position:relative;left:150px;width:400px;\"></div><div style=\"width:100%;padding-top:5px;\"><p>В галерее можно листать фото стрелочками влево и вправо на клавиатуре, а так же кликом на навигационных элементах. Кликнув на крестик вы вернетесь на основную страницу. Наведя на кнопку \"заказать\" увидите доступные размеры. Наведя на размер увидите его описание. Клик на размере приведет вас на форму заказа товара.</p></div>";
    
    document.getElementById('htb4').innerHTML = "<div style=\"width:100%;height:25px;padding-top:10px;\"><div onClick=\"htbdown(4);\" style=\"cursor:pointer; position:relative; left:300px; height:20px; padding-top:5px; padding-left:40px; width:100px;background:#000000; color:#ffffff;\">НАЗАД</div></div><div style=\"width:100%;padding-top:10px;\"><img src=\"images/41.png\" style=\"position:relative;left:105px;width:490px;\"></div><div style=\"width:100%;padding-top:5px;\"><p>Правильно заполните все поля формы, выберите цвет если есть другие цвета, и нажмите кнопку \"оплатить\". Вы попадете на страницу оплаты системы \"интеркасса\" где сможете выбрать удобный для вас способ оплаты и заказать товар. После оплаты вам придет письмо с подтверждением заказа на указанный в форме почтовый адресс. На этот же адресс придет письмо с номером для отслеживания заказа после его формирования.</p></div>";
  }
        document.onkeydown = function checkKeycode(event)
        {
			if (debilnuiNedoBrauzer)
			{
				var keycode;
				if(!event) var event = window.event;
				if (event.keyCode) keycode = event.keyCode; // IE
				else if(event.which) keycode = event.which; // all browsers
				if (document.getElementById('curprod')&&(document.getElementById('curprod').style.display == 'block'))
				{
					if (keycode == 37)
						location.href = downlink;
					else if (keycode == 39)
						location.href = uplink;
                    else if (keycode == 27)
                      	location.href = "http://776store.com/";
				}
				if (document.getElementById('howtobuy').style.display == 'block')
				{
					if (keycode == 37)
					{
						if (document.getElementById('htb2').style.display == 'block')
							htbdown(2);
						else if (document.getElementById('htb3').style.display == 'block')
							htbdown(3);
						else if (document.getElementById('htb4').style.display == 'block')
							htbdown(4);
					}
					else if (keycode == 39)
					{
						if (document.getElementById('htb1').style.display == 'block')
							htbup(1);
						else if (document.getElementById('htb2').style.display == 'block')
							htbup(2);
						else if (document.getElementById('htb3').style.display == 'block')
							htbup(3);
					}
                    else if (keycode == 27)
                      		clhtb();
				}
                if (keycode == 27)
                {
                  	if (document.getElementById('delivery').style.display == 'block')
                      cldelivery();
                  	if (document.getElementById('kontact').style.display == 'block')
                      clkontact();
                  	if (document.getElementById('invest').style.display == 'block')
                      clinvest();
                  	if (document.getElementById('partner').style.display == 'block')
                      clpartner();
                  	if (document.getElementById('maininf').style.display == 'block')
                      clmaininf();
                }
				
			}
			else
            {
				var keycode;
				if(!event) var event = window.event;
				if (event.keyCode) keycode = event.keyCode; // IE
				else if(event.which) keycode = event.which; // all browsers
                  //alert(keycode);
				if (document.querySelector('#curprod')&&(document.querySelector('#curprod').style.display == 'block'))
				{
					if (keycode == 37)
						location.href = downlink;
					else if (keycode == 39)
						location.href = uplink;
                    else if (keycode == 27)
                      	location.href = 'http://776store.com/index.php';
				}
				if (document.querySelector('#howtobuy').style.display == 'block')
				{
					if (keycode == 37)
					{
						if (document.querySelector('#htb2').style.display == 'block')
							htbdown(2);
						else if (document.querySelector('#htb3').style.display == 'block')
							htbdown(3);
						else if (document.querySelector('#htb4').style.display == 'block')
							htbdown(4);
					}
					else if (keycode == 39)
					{
						if (document.querySelector('#htb1').style.display == 'block')
							htbup(1);
						else if (document.querySelector('#htb2').style.display == 'block')
							htbup(2);
						else if (document.querySelector('#htb3').style.display == 'block')
							htbup(3);
					}
                    else if (keycode == 27)
                      		clhtb();
				}
                if (keycode == 27)
                {
                  	if (document.querySelector('#delivery').style.display == 'block')
                      cldelivery();
                  	if (document.querySelector('#kontact').style.display == 'block')
                      clkontact();
                  	if (document.querySelector('#invest').style.display == 'block')
                      clinvest();
                  	if (document.querySelector('#partner').style.display == 'block')
                      clpartner();
                  	if (document.querySelector('#maininf').style.display == 'block')
                      clmaininf();
                }
			}
        }
      
      var timer;
      function openposit(pid){
		if (debilnuiNedoBrauzer)
		{
              var x = 'block'+pid;
              document.getElementById(x).style.display="block";
              var x = 'prod'+pid;
              document.getElementById(x).style.zIndex="80";
		}
		else
		{
          var x = '#block'+pid;
          document.querySelector(x).style.display="block";
          var x = '#prod'+pid;
          document.querySelector(x).style.zIndex="80";
		}
        //document.querySelector('#shadow').style.display="block";
      }
      function timerstart(pid){
		if (debilnuiNedoBrauzer)
		{
              var x = 'block'+pid;
              document.getElementById(x).style.display="none";
              var x = 'prod'+pid;
              document.getElementById(x).style.zIndex="90";
		}
		else
		{
          var x = '#block'+pid;
          document.querySelector(x).style.display="none";
          var x = '#prod'+pid;
          document.querySelector(x).style.zIndex="90";
		}
        //document.querySelector('#shadow').style.display="none";
        //timer = setTimeout(timeout,200,pid);
      }
      function showsdesc(pid,sdesc){
        clearTimeout(timer);
        var str='',tmp;
        if (sdesc != '')
        {
          var arr = sdesc.split(',');
          for(var i=0; i<arr.length; i++) {
              tmp = arr[i].split(':');
              str += '<b>'+tmp[0]+':</b>&nbsp'+tmp[1]+'<br>';
              }
        }
		if (debilnuiNedoBrauzer)
		{
            var x = "sdesc"+pid;
            document.getElementById(x).innerHTML =str;
		}
		else
		{
        var x = "#sdesc"+pid;
        document.querySelector(x).innerHTML =str;
		}
      }
      function clsdesc(pid){
		if (debilnuiNedoBrauzer)
		{
            var x = "sdesc"+pid;
            document.getElementById(x).innerHTML ='';
		}
		else
		{
        var x = "#sdesc"+pid;
        document.querySelector(x).innerHTML ='';
		}
      }
      function buy(pid,sid){
        clearTimeout(timer);
      }
      function openfoto(){
        clearTimeout(timer);
      }
      function closefoto(pid){
        timerstart(pid);
      }
      function timeout(pid){
		if (debilnuiNedoBrauzer)
		{
              var x = 'block'+pid;
              document.getElementById(x).style.display="none";
              document.getElementById('shadow').style.display="none";
		}
		else
		{
          var x = '#block'+pid;
          document.querySelector(x).style.display="none";
          document.querySelector('#shadow').style.display="none";
		}
      }
      function showupbar(){
		if (debilnuiNedoBrauzer)
			document.getElementById('upbar').style.display="block";
		else
          document.querySelector('#upbar').style.display="block";
      }
      function hideupbar(){
		if (debilnuiNedoBrauzer)
			document.getElementById('upbar').style.display="none";
		else
          document.querySelector('#upbar').style.display="none";
      }  
      function maininf(){
          clpartner();
          clinvest();
          clkontact();
          clhtb();
          cldelivery();
          clbuy();
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="none";
              }
              else document.getElementById('shadowblack').style.display="block";
              document.getElementById('maininf').style.display="block";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="none";
          }
          else document.querySelector('#shadowblack').style.display="block";
          document.querySelector('#maininf').style.display="block";
		}
      }  
      function howtobuy(){
          clpartner();
          clinvest();
          clkontact();
          cldelivery();
          clmaininf();
          clbuy();
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="none";
              }
              else document.getElementById('shadowblack').style.display="block";
              document.getElementById('howtobuy').style.display="block";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="none";
          }
          else document.querySelector('#shadowblack').style.display="block";
          document.querySelector('#howtobuy').style.display="block";
		}
      } 
      function delivery(){
          clpartner();
          clinvest();
          clkontact();
          clhtb();
          clmaininf();
          clbuy();
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="none";
              }
              else document.getElementById('shadowblack').style.display="block";
              document.getElementById('delivery').style.display="block";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="none";
          }
          else document.querySelector('#shadowblack').style.display="block";
          document.querySelector('#delivery').style.display="block";
		}
      }  
      function invest(){
          clpartner();
          clkontact();
          clhtb();
          cldelivery();
          clmaininf();
          clbuy();
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="none";
              }
              else document.getElementById('shadowblack').style.display="block";
              document.getElementById('invest').style.display="block";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="none";
          }
          else document.querySelector('#shadowblack').style.display="block";
          document.querySelector('#invest').style.display="block";
		}
      }  
      function kontact(){
          clpartner();
          clinvest();
          clhtb();
          cldelivery();
          clmaininf();
          clbuy();
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="none";
              }
              else document.getElementById('shadowblack').style.display="block";
              document.getElementById('kontact').style.display="block";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="none";
          }
          else document.querySelector('#shadowblack').style.display="block";
          document.querySelector('#kontact').style.display="block";
		}
      } 
      function partner(){
          clkontact();
          clinvest();
          clhtb();
          cldelivery();
          clmaininf();
          clbuy();
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="none";
              }
              else document.getElementById('shadowblack').style.display="block";
              document.getElementById('partner').style.display="block";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="none";
          }
          else document.querySelector('#shadowblack').style.display="block";
          document.querySelector('#partner').style.display="block";
		}
      }
      function clmaininf(){
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="block";
              }
              else 
              {
                  document.getElementById('shadowblack').style.display="none";
                  document.getElementById('upbar').style.display="none";
              }
              document.getElementById('maininf').style.display="none";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="block";
          }
          else 
          {
              document.querySelector('#shadowblack').style.display="none";
              document.querySelector('#upbar').style.display="none";
          }
          document.querySelector('#maininf').style.display="none";
		}
      }  
      function clhtb(){
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="block";
              }
              else document.getElementById('shadowblack').style.display="none";
              document.getElementById('howtobuy').style.display="none";
              document.getElementById('upbar').style.display="none";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="block";
          }
          else document.querySelector('#shadowblack').style.display="none";
          document.querySelector('#howtobuy').style.display="none";
          document.querySelector('#upbar').style.display="none";
		}
      } 
      function cldelivery(){
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="block";
              }
              else 
              {
                  document.getElementById('shadowblack').style.display="none";
                  document.getElementById('upbar').style.display="none";
              }
              document.getElementById('delivery').style.display="none";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="block";
          }
          else 
          {
              document.querySelector('#shadowblack').style.display="none";
              document.querySelector('#upbar').style.display="none";
          }
          document.querySelector('#delivery').style.display="none";
		}
      } 
      function clinvest(){
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="block";
              }
              else 
              {
                  document.getElementById('shadowblack').style.display="none";
                  document.getElementById('upbar').style.display="none";
              }
              document.getElementById('invest').style.display="none";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="block";
          }
          else 
          {
              document.querySelector('#shadowblack').style.display="none";
              document.querySelector('#upbar').style.display="none";
          }
          document.querySelector('#invest').style.display="none";
		}
      } 
      function clpartner(){
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="block";
              }
              else 
              {
                  document.getElementById('shadowblack').style.display="none";
                  document.getElementById('upbar').style.display="none";
              }
              document.getElementById('partner').style.display="none";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="block";
          }
          else 
          {
              document.querySelector('#shadowblack').style.display="none";
              document.querySelector('#upbar').style.display="none";
          }
          document.querySelector('#partner').style.display="none";
		}
      }
      function clkontact(){
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="block";
              }
              else
              {
                  document.getElementById('shadowblack').style.display="none";
                  document.getElementById('upbar').style.display="none";
              }
              document.getElementById('kontact').style.display="none";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="block";
          }
          else
          {
              document.querySelector('#shadowblack').style.display="none";
              document.querySelector('#upbar').style.display="none";
          }
          document.querySelector('#kontact').style.display="none";
		}
      }
      function buy(pid,cost,sid,sv,sd,fp,col,event){
          clpartner();
          clinvest();
          clkontact();
          clhtb();
          cldelivery();
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="none";
              }
              else document.getElementById('shadowblack').style.display="block";
              var htm1 = "<div style=\"float:left;\"><img src=\""+fp+"\" style=\"width:70px;\"></div>";
              var htm2 = "<div style=\"float:left;padding-left:10px;\"><p>"+sv;
              if (sd != '') htm2 +=": "+sd+"</p>";
              else htm2+="</p>";
              var htm3 = "<p>стоимость: "+cost+" руб.</p>";
              var htm4 = "";
              var cols = col.split(",");
              if (col != '')
              {
                htm4 = htm4+"<p>";
                if (cols.length >1)
                {
                  htm4 = htm4+"color: <select id=\"colors\">";
                  for(var i=0; i<cols.length; i++)
                  {
                    if (i==0) 
                    {
                      htm4 = htm4+"<option selected value=\""+cols[i]+"\"> "+cols[i]+" </option>";
                      document.getElementById('infoform').col.value=cols[i];
                    }
                    else htm4 = htm4+"<option value=\""+cols[i]+"\"> "+cols[i]+" </option>";
                  }
                  htm4 = htm4+"</select>";
                }
                else 
                {
                  htm4 = htm4+"color: "+col;
                  document.getElementById('infoform').col.value=col;
                }
                htm4 = htm4+"</p>";
              }
              else document.getElementById('infoform').col.value='none';
              htm4+="</div>";
              var htm = htm1+htm2+htm3+htm4;
              document.getElementById("ptobuy").innerHTML =htm;
              var form=document.getElementById('infoform');
              form.sum.value = cost;
              form.pid.value = pid;
              form.sid.value = sid;
              document.getElementById('buy').style.display="block";
              (event && event.stopPropagation) ? event.stopPropagation() : window.event.cancelBubble = true;
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="none";
          }
          else document.querySelector('#shadowblack').style.display="block";
          var htm1 = "<div style=\"float:left;\"><img src=\""+fp+"\" style=\"width:70px;\"></div>";
          var htm2 = "<div style=\"float:left;padding-left:10px;\"><p>"+sv;
          if (sd != '') htm2 +=": "+sd+"</p>";
          else htm2+="</p>";
          var htm3 = "<p>стоимость: "+cost+" руб.</p>";
          var htm4 = "";
          var cols = col.split(",");
          if (col != '')
          {
            htm4+="<p>";
            if (cols.length >1)
            {
              htm4+="color: <select id=\"colors\">";
              for(var i=0; i<cols.length; i++)
              {
                if (i==0) 
                {
                  htm4+="<option selected value=\""+cols[i]+"\"> "+cols[i]+" </option>";
                  document.querySelector('#infoform').col.value=cols[i];
                }
                else htm4+="<option value=\""+cols[i]+"\"> "+cols[i]+" </option>";
              }
              htm4+="</select>";
            }
            else 
            {
              htm4="color: "+col;
              document.querySelector('#infoform').col.value=col;
            }
            htm4+="</p>";
          }
          else document.querySelector('#infoform').col.value='none';
          htm4+="</div>";
          var htm = htm1+htm2+htm3+htm4;
          document.querySelector("#ptobuy").innerHTML =htm;
          var form=document.querySelector('#infoform');
          form.sum.value = cost;
          form.pid.value = pid;
          form.sid.value = sid;
          document.querySelector('#buy').style.display="block";
          (event && event.stopPropagation) ? event.stopPropagation() : window.event.cancelBubble = true;
        }
      } 
      function validate(){
		if (debilnuiNedoBrauzer)
			var form=document.getElementById('infoform');
		else
			var form=document.querySelector('#infoform');
        var valid = true;
        var ptrn = /[a-z]+\s[a-z]+\s[a-z]/i;
        if (!ptrn.test(form.fio.value))
        {
          valid=false;
          form.fio.style.backgroundColor = '#FFA07A';
        }
        else form.fio.style.backgroundColor = '#ffffff';
        
        var ptrn = /ul\.\s[a-z]+\sd\.\s[0-9]+[\\[0-9]+]?\skv\.\s[0-9]+/i;
        if (!ptrn.test(form.adr.value))
        {
          valid=false;
          form.adr.style.backgroundColor = '#FFA07A';
        }
        else form.adr.style.backgroundColor = '#ffffff';
        
        
        var ptrn = /[a-z_]+/i;
        if (!ptrn.test(form.cyt.value))
        {valid=false;
          form.cyt.style.backgroundColor = '#FFA07A';
        }
        else form.cyt.style.backgroundColor = '#ffffff';
        
        var ptrn = /[a-z_]+/i;
        if (!ptrn.test(form.reg.value))
        {valid=false;
          form.reg.style.backgroundColor = '#FFA07A';
        }
        else form.reg.style.backgroundColor = '#ffffff';
        
        
        var ptrn = /[0-9]+/i;
        if (!ptrn.test(form.ind.value))
        {valid=false;
          form.ind.style.backgroundColor = '#FFA07A';
        }
        else form.ind.style.backgroundColor = '#ffffff';
        
        
        var ptrn = /[a-z_]+/i;
        if (!ptrn.test(form.cnt.value))
        {valid=false;
          form.cnt.style.backgroundColor = '#FFA07A';
        }
        else form.cnt.style.backgroundColor = '#ffffff';
        
        var ptrn = /89[0-9]{9}/i;
        if (!ptrn.test(form.phn.value))
        {valid=false;
          form.phn.style.backgroundColor = '#FFA07A';
        }
        else form.phn.style.backgroundColor = '#ffffff';
        
        var ptrn = /[0-9a-z_]+@[0-9a-z_]+\.[a-z]{2,5}/i;
        if (!ptrn.test(form.eml.value))
        {valid=false;
          form.eml.style.backgroundColor = '#FFA07A';
        }
        else form.eml.style.backgroundColor = '#ffffff';
        
        return valid;
        
      }
      function pay(){
		if (debilnuiNedoBrauzer)
		{
              if (validate())
              {
                var form=document.getElementById('infoform');
                if(document.getElementById('colors'))
                {
                   var cols = document.getElementById('colors');
                   form.col.value= cols.options[cols.selectedIndex].value;
                }
                var inf={
                  fio:form.fio.value,
                  adr:form.adr.value,
                  cyt:form.cyt.value,
                  reg:form.reg.value,
                  ind:form.ind.value,
                  cnt:form.cnt.value,
                  phn:form.phn.value,
                  eml:form.eml.value,
                  cst:form.sum.value,
                  prd:form.pid.value,
                  siz:form.sid.value,
                  col:form.col.value};
                  
                var ass = $.param(inf);
                var oid;
                $.ajax({
                      type: "POST",
                      url: "buy.php",
                      data: ass,
                      success: function(msg){
                          oid = msg;
                          var paym = document.getElementById('payment');
                          paym.ik_pm_no.value = 'ID_'+oid;
                        paym.ik_am.value = inf.cst;
                          var tomorrow = new Date();
                          tomorrow.setDate(tomorrow.getDate()+1);
                          var date = new Date((tomorrow.getMonth()+1)+ ',' + tomorrow.getDate() + ',' + tomorrow.getFullYear() + ',05:00:00');
                          var Y = date.getFullYear();
                          var M = date.getMonth()+1;
                          var D = date.getDate();
                          var PD;
                          if (M < 10)
                          {
                            if (D <10)
                            {
                              PD = Y+'-0'+M+'-0'+D;
                            }
                            else PD = Y+'-0'+M+'-'+D;
                          }
                          else PD = Y+'-'+M+'-'+D;
                        paym.ik_exp.value = PD;
                          paym.submit();
                          },
                      error: function( jqXHR, textStatus, errorThrown ){
                          alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
                      }
                });
              }
              else 
              {
                var str = "Поля заполнены неверно! все поля должны быть заполнены по образцу, латинскими буквами.";
                document.getElementById('validateerror').innerHTML = str;
              }
		}
		else
		{
          if (validate())
          {
            var form=document.querySelector('#infoform');
            if(document.querySelector('#colors'))
            {
               var cols = document.querySelector('#colors');
               form.col.value= cols.options[cols.selectedIndex].value;
            }
            var inf={
              fio:form.fio.value,
              adr:form.adr.value,
              cyt:form.cyt.value,
              reg:form.reg.value,
              ind:form.ind.value,
              cnt:form.cnt.value,
              phn:form.phn.value,
              eml:form.eml.value,
              cst:form.sum.value,
              prd:form.pid.value,
              siz:form.sid.value,
              col:form.col.value};
              
            var ass = $.param(inf);
            var oid;
            $.ajax({
                  type: "POST",
                  url: "buy.php",
                  data: ass,
                  success: function(msg){
                      oid = msg;
                      var paym = document.querySelector('#payment');
                      paym.ik_pm_no.value = 'ID_'+oid;
                    paym.ik_am.value = inf.cst;
                      var tomorrow = new Date();
                      tomorrow.setDate(tomorrow.getDate()+1);
                      var date = new Date((tomorrow.getMonth()+1)+ ',' + tomorrow.getDate() + ',' + tomorrow.getFullYear() + ',05:00:00');
                      var Y = date.getFullYear();
                      var M = date.getMonth()+1;
                      var D = date.getDate();
                      var PD;
                      if (M < 10)
                      {
                        if (D <10)
                        {
                          PD = Y+'-0'+M+'-0'+D;
                        }
                        else PD = Y+'-0'+M+'-'+D;
                      }
                      else PD = Y+'-'+M+'-'+D;
                    paym.ik_exp.value = PD;
                      paym.submit();
                      },
                  error: function( jqXHR, textStatus, errorThrown ){
                      alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
                  }
            });
          }
          else 
          {
            var str = "Поля заполнены неверно! все поля должны быть заполнены по образцу, латинскими буквами.";
            document.querySelector('#validateerror').innerHTML = str;
          }
		}
      }
      function clbuy(){
		if (debilnuiNedoBrauzer)
		{
              if (document.getElementById('curprod'))
              {
                  document.getElementById('curprod').style.display="block";
              }
              else 
              {
                  document.getElementById('shadowblack').style.display="none";
                  document.getElementById('upbar').style.display="none";
              }
              document.getElementById('buy').style.display="none";
		}
		else
		{
          if (document.querySelector('#curprod'))
          {
              document.querySelector('#curprod').style.display="block";
          }
          else 
          {
              document.querySelector('#shadowblack').style.display="none";
              document.querySelector('#upbar').style.display="none";
          }
          document.querySelector('#buy').style.display="none";
		}
      }
      function htbup(x)
      {
		if (debilnuiNedoBrauzer)
		{
            var h1 = "htb"+x;
            var h2 = "htb"+(x+1);
            document.getElementById(h1).style.display='none';
            document.getElementById(h2).style.display='block';
		}
		else
		{
        var h1 = "#htb"+x;
        var h2 = "#htb"+(x+1);
        document.querySelector(h1).style.display='none';
        document.querySelector(h2).style.display='block';
		}
      }
      function htbdown(x)
      {
		if (debilnuiNedoBrauzer)
		{
            var h1 = "htb"+x;
            var h2 = "htb"+(x-1);
            document.getElementById(h1).style.display='none';
            document.getElementById(h2).style.display='block';
		}
		else
		{
        var h1 = "#htb"+x;
        var h2 = "#htb"+(x-1);
        document.querySelector(h1).style.display='none';
        document.querySelector(h2).style.display='block';
		}
      }
      function openS()
      {
		if (debilnuiNedoBrauzer)
		{
              document.getElementById('vspom1').style.display = 'none';
              document.getElementById('cpsizes').style.display = 'block';
		}
		else
		{
          document.querySelector('#vspom1').style.display = 'none';
          document.querySelector('#cpsizes').style.display = 'block';
		}
      }  
      function closeS()
      {
		if (debilnuiNedoBrauzer)
		{
              document.getElementById('vspom1').style.display = 'block';
              document.getElementById('cpsizes').style.display = 'none';
		}
		else
		{
          document.querySelector('#vspom1').style.display = 'block';
          document.querySelector('#cpsizes').style.display = 'none';
		}
      }  
      function cpsdshow(sd)
      {
        var str='<br><br>',tmp;
        if (sd != '')
        {
          var arr = sd.split(',');
          for(var i=0; i<arr.length; i++) {
              tmp = arr[i].split(':');
              str += '<b>'+tmp[0]+':</b>&nbsp'+tmp[1]+'<br>';
              }
        }
		if (debilnuiNedoBrauzer)
		{
            document.getElementById('cpsdesc').innerHTML =str;
            document.getElementById('cpsdesc').style.display = 'block';
		}
		else
		{
        document.querySelector('#cpsdesc').innerHTML =str;
        document.querySelector('#cpsdesc').style.display = 'block';
		}
      }  
      function cpsdhide()
      {
		if (debilnuiNedoBrauzer)
		{
            document.getElementById('cpsdesc').innerHTML ='';
            document.getElementById('cpsdesc').style.display = 'none';
		}
		else
		{
        document.querySelector('#cpsdesc').innerHTML ='';
        document.querySelector('#cpsdesc').style.display = 'none';
		}
      }
      function blockclick(pid)
      {
          location.href = "http://776store.com/index.php?pid="+pid;
      }
 
</script>
</body>
</html>