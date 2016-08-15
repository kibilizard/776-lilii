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
	if ($arr['ik_inv_st'] == 'success')
    {
      $stmt = $pdo->prepare('UPDATE orders SET status = 2 WHERE id = ?');
      $arr2 = explode('_',$arr['ik_pm_no']);
      $stmt->execute(array($arr2[1]));
      
      $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
      $stmt->execute(array($arr2[1]));
      $row = $stmt->fetch();
      print_r($row);
      
      $stm2 = $pdo->prepare('SELECT foto, cost, crdate FROM positions WHERE id = ?');
      $stm2->execute(array($row['prid']));
      $r2 = $stm2->fetch();
      print_r($r2);
      
      $stm3 = $pdo->prepare('SELECT value FROM sizes WHERE id = ?');
      $stm3->execute(array($row['sid']));
      $r3 = $stm3->fetch();
      
      $to      = 'webmaster@776store.com';
      $subject = 'Payed order';

      $message = 'payed:<br>'.$row['fio'].'<br>' . $row['adr'].'<br>' .$row['city'].'<br>' .$row['region']. '<br>' .$row['pindex']. '<br>' .$row['country']. '<br>' .$row['phone']. '<br>' .$row['email']. '<br>' .$row['summ'].' rub. '.'<br>' .$row['time']. '<br><br>' .'position: '.'<br>'.'cost:'.$r2['cost']. '<br>' .'date: '.$r2['crdate']. '<br><br>'.'size: ' .$r3['value'].'<br>color: '.$row['color'];

	  $fp = fopen($r2['foto'],"rb"); 
      if (!$fp)   
      { 
        print "Cannot open file";   
     	exit();   
      }   
      $file = fread($fp, filesize($r2['foto']));   
      fclose($fp); 
		$arn = explode('/',$r2['foto']);
    $name = $arn[1]; // в этой переменной надо сформировать имя файла (без всякого пути)  
    $EOL = "\r\n"; // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
    $boundary     = "--".md5(uniqid(time()));  // любая строка, которой не будет ниже в потоке данных.  
    $headers    = "MIME-Version: 1.0;$EOL";   
    $headers   .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";  
    $headers   .= "From: orders@776store.com";  
      
    $multipart  = "--$boundary$EOL";   
    $multipart .= "Content-Type: text/html; charset=windows-1251$EOL";   
    $multipart .= "Content-Transfer-Encoding: base64$EOL";   
    $multipart .= $EOL; // раздел между заголовками и телом html-части 
    $multipart .= chunk_split(base64_encode($message));   

    $multipart .=  "$EOL--$boundary$EOL";   
    $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";   
    $multipart .= "Content-Transfer-Encoding: base64$EOL";   
    $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";   
    $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла 
    $multipart .= chunk_split(base64_encode($file));   

    $multipart .= "$EOL--$boundary--$EOL";   
      
    mail($to, $subject, $multipart, $headers);
      $to      = '2999776@gmail.com';
    mail($to, $subject, $multipart, $headers);
      
      $to = $row['email'];
      $subject = 'Ваш заказ принят';
      $message = 'Ваш заказ был успешно оплачен и принят в обработку.'. "\r\n" .'Как только заказ будет сформирован мы вышлем вам письмо с номером для отслеживания посылки на этот же электронный адрес.'. "\r\n" .'Со сроками формирования заказа и доставки вы можете ознакомиться в разделе "Доставка\возврат" на 776store.com'. "\r\n" .'Мы будем рады видеть вас снова!';
      
      $headers = 'From: orders@776store.com' . "\r\n" .
        'Reply-To: orders@776store.com' . "\r\n" .
    	'X-Mailer: PHP/' . phpversion();
      mail($to, $subject, $message, $headers);
    }
header('Location:http://776store.com');
exit;
?>