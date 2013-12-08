<?php
/*
* Created on 2012-5-14 by lunwei
*/
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	@set_time_limit(1000);
	@set_magic_quotes_runtime(0);

include_once '../sys/core/init.inc.php';
if ( !isset($_SESSION['user']) ){
  header("Location: ./");
  exit;
}

/*
* Output the header
*/
$page_title = "Add/Edit Event";
$css_files = array("style.css","admin.css");
include_once 'assets/common/header.inc.php';

/*
* Load the calendar
*/
$cal = new Calendar($dbo);
?>

<div id="content">
  <?php echo @$cal->displayForm(); ?>
</div><!-- end #content -->

<?php
/*
* Output the footer
*/
include_once 'assets/common/footer.inc.php';
?>
