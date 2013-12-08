<?php
/*
* Created on 2012-5-14 by lunwei
*/
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	@set_time_limit(1000);
	@set_magic_quotes_runtime(0);
/*
* Include necessary files
*/
include_once '../sys/core/init.inc.php';

/*
* Load the calendar for January
*/
$cal = new Calendar($dbo,'2012-05-30 00:00:00');
$page_title = "Events Calendar";
$css_files = array('style.css','admin.css','ajax.css');
include_once 'assets/common/header.inc.php';
?>
<div id="content">
  <?php
   echo $cal->buildCalendar();
  ?>
</div><!-- end #content -->
<p>
  <?php
    echo isset($_SESSION['user']) ? "欢迎您，".$_SESSION['user']['name']."!": "还未登录！";
  ?>
</p>

<?php
/*
* Include the footer
*/
include_once 'assets/common/footer.inc.php';
?>