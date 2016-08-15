<?php
	$db_hostname = 'localhost';
	$db_database = '***';
	$dsn = "mysql:host=$db_hostname;dbname=$db_database;charset=utf8";
	$opt = array(
		PDO::ATTR_ERRMODE 			 => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
    $pdo = new PDO($dsn, '****', '*****', $opt);
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
	$stmt = $pdo->prepare('SELECT id, value, descript FROM sizes WHERE posid = ?');
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
?> 
<html>
<head>
<meta http-equiv="Content-Type" content = "text/html; charset=windows-1251" />
<title> 776 admin </title>
<link rel="stylesheet" type="text/css" href="css/776style.css" /> 
<link rel="stylesheet" href="jquery-ui-1.10.4.custom/css/base/jquery-ui-1.10.4.custom.css">
<script type="text/javascript" src="jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
  <script>
  var files;
  var addposinf;
  var addpossiz;
  var addposfot;
  <?php
	foreach($lib as $prod)
	{
		echo '
		var sizp'.strval($prod['id']).'; 
		var posp'.strval($prod['id']).';
		var costp'.strval($prod['id']).';
		var cols'.strval($prod['id']).';
		var actp'.strval($prod['id']).';
		';
		foreach($prod['fotos'] as $foto)
		{
			echo '
			var sizp'.strval($prod['id']).'f'.strval($foto['fid']).';
			var posp'.strval($prod['id']).'f'.strval($foto['fid']).';
			var actp'.strval($prod['id']).'f'.strval($foto['fid']).';
			';
		}
		foreach($prod['sizes'] as $size)
		{
			echo '
			var acts'.$size['sid'].';
			var vals'.$size['sid'].';
			var descs'.$size['sid'].';
			';
		}
	}
  ?>
  $(document).ready(init);
    document.onkeydown = function checkKeycode(event)
        {
          var keycode;
          if(!event) var event = window.event;
          if (event.keyCode) keycode = event.keyCode; // IE
          else if(event.which) keycode = event.which; // all browsers
            //alert(keycode);
              if (keycode == 13)
              {
                var p = document.querySelector('#pwd').value;
                if (p == 'fynjy-ufyljy')
                	document.querySelector('#shadow3').style.display = 'none';
              }
    }
function init(){
	$('input[type=file]').change(function(){
		files = this.files;
		});
	$("#closeside").click(
		function(){$('#side').fadeOut(1000);}
		);
	$("#rel").hover(
		function(){$('#side').fadeIn(1000);},
		function(){}
		);
	$("#addposf").click(function( event ){
				event.stopPropagation();
				event.preventDefault();
				var data = new FormData();
				$.each( files, function( key, value ){
					data.append( key, value );
				});
				$.ajax({
					url: './submit.php?uploadfiles',
					type: 'POST',
					data: data,
					cache: false,
					dataType: 'json',
					processData: false, 
					contentType: false, 
					success: function( respond, textStatus, jqXHR ){
						if( typeof respond.error === 'undefined' ){
							addposfot=[];
							var files_path = respond.files;
							var hf = '';
							$.each( files_path, function( key, val ){
                              		var arr = val.split('/');
									var s;
									for (var i = 0; i < arr.length; i++) {
										if (arr[i] == 'uploads')
										{
											s = arr[i]+'/'+arr[i+1];
										}
									}
									hf = hf + '<img id="im'+key+'" src="'+s+'" style="width:66px;border: 2px solid white;">';
									addposfot.push(s);
								});
							$("#fcontainer").html(hf);
						}
						else{
							alert('ОШИБКИ ОТВЕТА сервера: ' + respond.error );
						}
					},
					error: function( jqXHR, textStatus, errorThrown ){
						alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
					}
				});
			});
	$("#addposfm").click(function( event ){
				event.stopPropagation();
				event.preventDefault();
				var data = new FormData();
				$.each( files, function( key, value ){
					data.append( key, value );
				});
				$.ajax({
					url: './submit.php?uploadfiles',
					type: 'POST',
					data: data,
					cache: false,
					dataType: 'json',
					processData: false, 
					contentType: false, 
					success: function( respond, textStatus, jqXHR ){
						if( typeof respond.error === 'undefined' ){
							var files_path = respond.files;
							var hf = '';
							$.each( files_path, function( key, val ){
                              		var arr = val.split('/');
									var s;
									for (var i = 0; i < arr.length; i++) {
										if (arr[i] == 'uploads')
										{
											s = arr[i]+'/'+arr[i+1];
										}
									}
									hf = hf + '<img id="im'+key+'" src="'+s+'" style="width:150px;border: 2px solid white;">';
									addposinf.path = s;
								});
							$("#fmcontainer").html(hf);
							$("#fotosn").show();
						}
						else{
							alert('ОШИБКИ ОТВЕТА сервера: ' + respond.error );
						}
					},
					error: function( jqXHR, textStatus, errorThrown ){
						alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
					}
				});
			});
	$("#addpos").click(function(){
		addpossiz = [];
		var c = $('input[name="addposc"]').val();
		addposinf['cost']=c;
		$("#newpos input:checkbox:checked").each(function(){
			var s = $(this).val();
			var d = $('input[name="addposdesk'+s+'"]').val();
			var x = {val:s,desc:d}
			addpossiz.push(x);
		})
        var stl="top:350px;left:350px";
      	var cols = $('input[name="addposcolors"]').val();
      var inf={type:'pos',inf:addposinf,fot:addposfot,siz:addpossiz,stl:stl,col:cols};
		var ass = $.param(inf);
		$.ajax({
			type: "POST",
			url: "add.php",
			data: ass,
			success: function(msg){
				location.reload();
				},
			error: function( jqXHR, textStatus, errorThrown ){
				alert('ОШИБКИ AJAX запроса: ' + textStatus +' '+errorThrown );
			}
		});
	});	
	$("#fotosn").hide();
	$("#footer").hide();
	$("#mbut").hover(
		function(){$('#footer').show();},
		function(){}
		);
	$("#footer").hover(
		function(){},
		function(){$('#footer').hide();}
		);
	<?php
	foreach($lib as $prod)
	{
		echo '
			$("#adf'.$prod['id'].'").click(function( event ){
				event.stopPropagation();
				event.preventDefault();
				var data = new FormData();
				$.each( files, function( key, value ){
					data.append( key, value );
				});
				$.ajax({
					url: \'./submit.php?uploadfiles\',
					type: \'POST\',
					data: data,
					cache: false,
					dataType: \'json\',
					processData: false, 
					contentType: false, 
					success: function( respond, textStatus, jqXHR ){
						if( typeof respond.error === \'undefined\' ){
							var files_path = respond.files;
							$.each( files_path, function( key, val ){
									var af = {
									type:\'fot\',
									pid:'.$prod['id'].',
									path:val
								};
								var afs = $.param(af);
								$.ajax({
								type: "POST",
								url: "add.php",
								data: afs,
								success: function(msg){
									}
								});
								location.reload();
							});
						}
						else{
							alert(\'ОШИБКИ ОТВЕТА сервера: \' + respond.error );
						}
					},
					error: function( jqXHR, textStatus, errorThrown ){
						alert(\'ОШИБКИ AJAX запроса: \' + textStatus +\' \'+errorThrown );
					}
				});
			});
			$("#prod'.$prod['id'].'").draggable({
				distance:5,
				stop: function(event, ui) {
					var a = '.$prod['id'].';
					var e=ui.offset.left;
					var f=ui.offset.top;
					var of = ui.offset;
					posp'.strval($prod['id']).'=\'left:\'+e+\'px;top:\'+f+\'px;\';
			var doc = document.querySelector(\'#img'.$prod['id'].'\').getBoundingClientRect();
			sizp'.strval($prod['id']).'=\'width:\'+doc.width+\'px;height:\'+doc.height+\'px;\';
					actp'.strval($prod['id']).'= \'upd\';
					$("#siz'.$prod['id'].'").show();
					$("#siz'.$prod['id'].'").offset(of);
					$("#siz'.$prod['id'].'").hide();
					}
			});
			$("#prod'.$prod['id'].'").click(function(){';
				foreach($prod['fotos'] as $foto)
				{
					echo '
						$("#prod'.$prod['id'].'f'.$foto['fid'].'").toggle();
					';
				}
				echo '
			});
			$("#siz'.$prod['id'].'").hide();
			$("#sizshow'.$prod['id'].'").hover(
				function(){$("#siz'.$prod['id'].'").show();},
				function(){}
				);
			$("#sizyes'.$prod['id'].'").click(function(){';
				foreach($prod['sizes'] as $size)
				{
					echo '
					descs'.$size['sid'].' = $("#is'.$size['sid'].'").val();
					acts'.$size['sid'].' = \'upd\';
					';
				}
				echo '
				costp'.$prod['id'].'=$("#cost'.$prod['id'].'").val();
                cols'.$prod['id'].'=$("#colors'.$prod['id'].'").val();
				actp'.strval($prod['id']).'= \'upd\';
				';
			echo '});
			$("#sizhide'.$prod['id'].'").click(function(){$("#siz'.$prod['id'].'").hide();});
			$("#undpos'.$prod['id'].'").click(function(){
					var r = confirm("Вы действительно хотите удалить этот товар?");
					if (r)
					{
						var dp = {
							type:\'pos\',
							id:'.$prod['id'].'
						};
						var dps = $.param(dp);
						alert("удаляем товар "+dps);
						$.ajax({
						type: "POST",
						url: "delette.php",
						data: dps,
						success: function(msg){
							$("#prod'.$prod['id'].'").remove();
							$("#siz'.$prod['id'].'").remove();
							$("#img'.$prod['id'].'").remove();
							$("#sizshow'.$prod['id'].'").remove();';
							foreach($prod['fotos'] as $f)
							{
								echo '
								$("#prod'.$prod['id'].'f'.$f['fid'].'").remove();
								$("#delf'.$f['fid'].'").remove();';
							}
							
							echo '}
						});
						location.reload();
					}
				});
			$("#adds'.$prod['id'].'").click(function(){
				var v = $("#sels'.$prod['id'].' :selected").val();
				var dsc = $("#sets'.$prod['id'].'").val();
				var r = confirm("Вы действительно хотите добавить размер "+v+" "+dsc);
				if (r)
				{
					var as = {
						type:\'siz\',
						pid:'.$prod['id'].',
						val:v,
						dsc:dsc
					};
					var ass = $.param(as);
					$.ajax({
					type: "POST",
					url: "add.php",
					data: ass,
					success: function(msg){
						alert( "ответ сервера: " + msg );
						}
					});
					location.reload();
				}
			});
		';
		foreach($prod['fotos'] as $foto)
		{
			echo '
				$("#prod'.$prod['id'].'f'.$foto['fid'].'").draggable({
					distance:5,
					stop: function(event, ui) {
						var a = '.$prod['id'].';
						var e=ui.offset.left;
						var f=ui.offset.top;
						posp'.strval($prod['id']).'f'.strval($foto['fid']).'=\'left:\'+e+\'px;top:\'+f+\'px;\';
				var doc = document.querySelector(\'#img'.$prod['id'].'f'.$foto['fid'].'\').getBoundingClientRect();
				sizp'.strval($prod['id']).'f'.strval($foto['fid']).'=\'width:\'+doc.width+\'px;height:\'+doc.height+\'px;\';
						actp'.strval($prod['id']).'f'.strval($foto['fid']).'= \'upd\';
						}
				});
				$("#prod'.$prod['id'].'f'.$foto['fid'].'").hide();
				$("#delf'.$foto['fid'].'").hover(
					function(){},
					function(){$("#delf'.$foto['fid'].'").hide();}
					);
				$("#shdelf'.$foto['fid'].'").hover(
					function(){$("#delf'.$foto['fid'].'").show();},
					function(){}
					);
				$("#delf'.$foto['fid'].'").hide();
				$("#delf'.$foto['fid'].'").click(function(){
					var r = confirm("Вы действительно хотите удалить эту фотографию?");
					if (r)
					{
						var df = {
							type:\'fot\',
							id:'.$foto['fid'].'
						};
						var dfs = $.param(df);
						$.ajax({
						type: "POST",
						url: "delette.php",
						data: dfs,
						success: function(msg){
							$("#prod'.$prod['id'].'f'.$foto['fid'].'").remove();
							$("#delf'.$foto['fid'].'").remove();
							}
						});
					}
				});
			';
		}
		foreach($prod['sizes'] as $size)
		{
			echo '
			$("#dels'.$size['sid'].'").click(function(){
				var r = confirm("Вы действительно хотите удалить этот размер?");
				if (r)
				{
					var ds = {
						type:\'siz\',
						id:'.$size['sid'].'
					};
					var dss = $.param(ds);
					$.ajax({
					type: "POST",
					url: "delette.php",
					data: dss,
					success: function(msg){
						}
					});
					location.reload();
				}
			});
			';
		}	
	}
	?>
}
$(function() {
	addposinf = {path:'',cost:''};
	<?php
	foreach($lib as $prod)
	{
      $style = explode(';',$prod['style']);
		echo '
			$( "#img'.$prod['id'].'" ).resizable({
            minWidth: 200,
            stop:  function(event, ui) {
				var a='.$prod['id'].';
			sizp'.strval($prod['id']).'=\'width:\'+ui.size.width+\'px;height:\'+ui.size.height+\'px;\';
			var doc = document.querySelector(\'#img'.$prod['id'].'\').getBoundingClientRect();
			posp'.strval($prod['id']).'=\'left:\'+doc.left+\'px;top:\'+doc.top+\'px;\';
				actp'.strval($prod['id']).'= \'upd\';
				$("#siz'.$prod['id'].'").width(ui.size.width);
				$("#siz'.$prod['id'].'").height(ui.size.height);
                }
            });
			var doc = document.querySelector(\'#img'.$prod['id'].'\').getBoundingClientRect();
			sizp'.strval($prod['id']).'=\''.$style[0].';'.$style[1].';\';
			posp'.strval($prod['id']).'=\''.$style[2].';'.$style[3].';\';
            cols'.strval($prod['id']).'=\''.$prod['colors'].'\';
			costp'.strval($prod['id']).'='.strval($prod['cost']).';
			actp'.strval($prod['id']).'= \'non\';';
			foreach($prod['fotos'] as $foto)
			{
				echo '
					$("#img'.$prod['id'].'f'.$foto['fid'].'").resizable({
           			minWidth: 50,
					stop:  function(event, ui) {
						var a='.$prod['id'].';
						sizp'.strval($prod['id']).'f'.strval($foto['fid']).'=\'width:\'+ui.size.width+\'px;height:\'+ui.size.height+\'px;\';
				var doc = document.querySelector(\'#img'.$prod['id'].'f'.$foto['fid'].'\').getBoundingClientRect();
			posp'.strval($prod['id']).'f'.strval($foto['fid']).'=\'left:\'+doc.left+\'px;top:\'+doc.top+\'px;\';
						actp'.strval($prod['id']).'f'.strval($foto['fid']).'= \'upd\';
						}
					});
				var doc = document.querySelector(\'#img'.$prod['id'].'f'.$foto['fid'].'\').getBoundingClientRect();
				sizp'.strval($prod['id']).'f'.strval($foto['fid']).'=\'width:\'+doc.width+\'px;height:\'+doc.height+\'px;\';
				posp'.strval($prod['id']).'f'.strval($foto['fid']).'=\'left:\'+doc.left+\'px;top:\'+doc.top+\'px;\';
				actp'.strval($prod['id']).'f'.strval($foto['fid']).'= \'non\';
				';
			}
			foreach($prod['sizes'] as $size)
			{
				echo '
				acts'.$size['sid'].'=\'non\';
				vals'.$size['sid'].'=\''.$size['val'].'\';
				descs'.$size['sid'].'=\''.$size['desc'].'\';
				';
			}
	}
	?>
  });
 function tst(){
	 var a = [];
	<?php
	foreach($lib as $prod)
	{
		echo '
		var f=[],
		s=[];
		';
		foreach($prod['fotos'] as $foto)
		{
			echo '
			if (actp'.strval($prod['id']).'f'.strval($foto['fid']).' != \'non\')
				{
					f['.strval($foto['fid']).']= {css:sizp'.strval($prod['id']).'f'.strval($foto['fid']).'+posp'.strval($prod['id']).'f'.strval($foto['fid']).',
					act:actp'.strval($prod['id']).'f'.strval($foto['fid']).'};
				}
				';
		}
		foreach($prod['sizes'] as $size)
		{
			echo '
			if (acts'.$size['sid'].' != \'non\')
				{
					s['.$size['sid'].']={act:acts'.$size['sid'].',
					val:vals'.$size['sid'].',
					desc:descs'.$size['sid'].'};
				}
				';
		}
		echo '
		a['.$prod['id'].']= {css:sizp'.strval($prod['id']).'+posp'.strval($prod['id']).',
		act:actp'.strval($prod['id']).',
		cst:costp'.strval($prod['id']).',
		col:cols'.strval($prod['id']).',
		fot:f,
		siz:s};
		';
	}
  ?>
	var x = {info:a};
	var s = $.param(x);
	$.ajax({
	type: "POST",
	url: "ajtest.php",
	data: s,
	success: function(msg){
      location.reload();
		}
	});
 }
</script>
</head>
<body link="#000000" vlink="#000000" alink="#000000">

  <div id="shadow" style="display:none;"></div>

<div id="rel" style="background:#ffffff;width:10px;height:400px;opacity:0.1;position:fixed;right:0px;top:0px;Z-index:100;"></div>
<div id="side" style="background:#ffffff;opacity:0.9;width:350px;height:100%;position:absolute;right:0px;top:0px;Z-index:100;">
	<form id="newpos" action="upload.php">
		<div id="closeside" style="cursor:pointer;">СКРЫТЬ</div>
		<br> ОСНОВНОЕ ФОТО<br>
		<input type="file" name="fotm" id="fotm" accept="image/*">
		<img id="addposfm" src="images/ok.png" style="cursor:pointer;"><br>
		<div id="fmcontainer" style="width:100%"></div><br>
		<div id="fotosn"><br> ДОБАВЬТЕ ФОТОГРАФИИ <br>
		<input type="file" name="fot" id="fot" multiple accept="image/*">
		<img id="addposf" src="images/ok.png" style="cursor:pointer;"><br>
		<div id="fcontainer" style="width:100%"></div></div><br>
		ЦЕНА: <input type="text" name="addposc" size="5">руб.<br><br>
		РАЗМЕРЫ:<br><table><?php
		$arr = array('XS','S','M','L','XL','XXL','XXXL','ONE SIZE');
		foreach ($arr as $size)
		{
			echo '<tr><td><input type="checkbox" name="addsv" value="'.$size.'">'.$size.' :</td><td><input type="text" name="addposdesk'.$size.'" size="25"></td></tr>';
		}
		?>
      <tr><td>ЦВЕТА: </td><td><input type="text" name="adposcolors" size="25"></td></tr>
		</table><br>
		<div id="addpos" style="cursor:pointer;position:relative;left:110px;width:130px;height:22px;margin:auto;padding-top:10px;background:#000000;border:none;color:#ffffff;text-align:center;text-decoration:none;display:inline-block;font-size:12px;">СОХРАНИТЬ</div>
	</form>
</div>
<?php
	foreach($lib as $prod)
	{
		$arr = explode(";",$prod['style']);
        $x = explode(":",$arr[0]);
          	if ($x[0] != 'width')
            {
              $size = getimagesize($foto['path']);
            }
		echo '<div>
			<div id="prod'.$prod['id'].'" style="position:absolute;'.$prod['style'].',Z-index:90;">
			<img id="img'.$prod['id'].'" class="ui-widget-content" src="'.$prod['foto'].'" style="';
      if ($x[0] == 'width') echo $arr[0].';'.$arr[1].';';
          else echo 'width:100px;height:100px;';
      echo 'Z-index:90;">
			<div id="sizshow'.$prod['id'].'" style="';
      if ($x[0] == 'width') echo 'width: 100%';
      else echo 'width:100px';
      echo ';height:2px;Z-index:100;"></div>
			</div>
			<div id="siz'.$prod['id'].'" style="background:#ffffff;opacity:0.6;position:absolute;Z-index:110;'.$prod['style'].'">
			<form id="chsize'.$prod['id'].'">РАЗМЕРЫ:<br>';
			$sizes = array('XS','S','M','L','XL','XXL','XXXL','ONE SIZE');
			foreach($prod['sizes'] as $siz)
			{
				$c = count($sizes);
				for($i=0;$i<$c;$i++)
				{
					if ($sizes[$i] == $siz['val'])
						unset($sizes[$i]);
				}
				$sizes = array_values($sizes);
				echo '<img id="dels'.$siz['sid'].'" src="images/undo.png" style="cursor:pointer;">&nbsp'.$siz['val'].': <input id="is'.$siz['sid'].'" type="text" value="'.$siz['desc'].'" size="10"><br>';
			}
			if (count($sizes)>0)
			{
				echo '<select id="sels'.$prod['id'].'" name="sels'.$prod['id'].'">';
				foreach($sizes as $s)
				{
					echo '<option>'.$s.'</option>';
				}
				echo '</select>&nbsp<input id="sets'.$prod['id'].'" type="text" value="" size="25">&nbsp<img id="adds'.$prod['id'].'" src="images/add.png" style="cursor:pointer;"><br><br>';
			}
      		echo 'ЦВЕТА: <input id="colors'.$prod['id'].'" value="'.$prod['colors'].'"><br>';
			echo 'ЦЕНА: <input id="cost'.$prod['id'].'" type="text" value="'.$prod['cost'].'" size="23"><br><br>';
			echo 'ДОБАВИТЬ ФОТО: <input type="file" multiple="multiple" accept="image/*">
				<a href="#" id="adf'.$prod['id'].'" class="submit button">Загрузить файлы</a>';
			echo '</form>
			<div id="sizyes'.$prod['id'].'" style="float:left;cursor:pointer;">СОХРАНИТЬ|</div>
			<div id="sizhide'.$prod['id'].'" style="float:left;cursor:pointer;">СКРЫТЬ ИНФО|</div>
			<div id="undpos'.$prod['id'].'" style="float:left;cursor:pointer;">УДАЛИТЬ ТОВАР</div>
			</div>
			</div>';
		foreach($prod['fotos'] as $foto)
		{
			$arr = explode(";",$foto['style']);
        	$x = explode(":",$arr[0]);
          	if ($x[0] != 'width')
            {
              $size = getimagesize($foto['path']);
            }
          echo '<div id="prod'.$prod['id'].'f'.$foto['fid'].'" style="position:absolute;Z-index:120;'.$foto['style'].'">
			<img id="img'.$prod['id'].'f'.$foto['fid'].'" class="ui-widget-content" src="'.$foto['path'].'" style="';
          if ($x[0] == 'width') echo $arr[0].';'.$arr[1].';';
          else echo 'width:100px;height:100px;';
          echo 'Z-index:120;">
			<div id="shdelf'.$foto['fid'].'" src="images/undo.png" style="width: 16px; height:16px;cursor:pointer;"></div>
			<img id="delf'.$foto['fid'].'" src="images/undo.png" style="position:relative;top:-16px;cursor:pointer;">
			</div>';
		}
	}
?>
<div id="mbut" style="position:fixed;top:0px;width:100%;height:2px;opacity:0.1;"></div>
<div id="footer" style="position:fixed;top:0px;left:0px;width:180px;height:15px;background:#ffffff;Z-index:104;">
  <div id="mbutton" onClick="tst()" style="cursor:pointer;width:180px;height:25px;background:#000000;color:#ffffff;font-size:10px;padding-top:15px;padding-left:45px;">ПРИМЕНИТЬ ИЗМЕНЕНИЯ</div>
  </div>
  <div id="shadow3" style="display:block; position:fixed; top:0px; left:0px; height:100%; width:100%; background:#000000; opacity:0.6; Z-index:107;"><div style="width:300px; height:300px; position:relative; top:130px; left:0px; right:0px; margin:auto; font-size:30px;Z-index:107;"><p>ВНЕСЕНИЕ ИЗМЕНЕНИЙ НЕДОСТУПНО!</p><p>пароль:&nbsp<input name="pwd" id="pwd" type="password"></p></div></div>
</body>
</html>
