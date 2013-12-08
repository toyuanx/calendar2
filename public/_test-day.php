<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<?php 
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	@set_time_limit(1000);
	@set_magic_quotes_runtime(0);
      date_default_timezone_set('PRC');
	  $now_day=date('Y-m-d H:i:s');
	  $ts=strtotime($now_day);
	  $month=date('m',$ts);
	  $year=date('y',$ts);
      $start_ts = mktime(0, 0, 0, $month, 1, $year);
      $end_ts = mktime(23, 59, 59, $month+1, 0, $year);
      $start_date = date('Y-m-d H:i:s', $start_ts);
      $end_date = date('Y-m-d H:i:s', $end_ts);
	  echo '<h3>'.$start_date.'</h3>';
	  echo '<h3>'.$end_date.'</h3>';
	  echo $now_day;
	  echo '<br />'.date('Y年m月d日 H:i:s',$ts);
?>
<div>
若将时间保存在数据库中，不要保存时间date('Y年m月d日 H:i:s'),而是保存date('Y-m-d H:i:s')或者保存时间戳，为什么呢？因为strtotime()函数可以将date('Y-m-d H:i:s')返回的字符串转为时间戳。而date('Y年m月d日 H:i:s')却不可以。
</div>
</body>
</html>