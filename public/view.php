<?php
/*
 * Created on 2012-5-14 by lunwei
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);

if (isset($_GET['event_id'])) {
    $id = preg_replace('/[^0-9]/', '', $_GET['event_id']);
    if (empty($id)) {
        header("Location: ./");
        exit;
    }
} else {
    header("Location: ./");
    exit;
}
include_once '../sys/core/init.inc.php';
$page_title = "View Event";
$css_files = array("style.css", "admin.css");
include_once 'assets/common/header.inc.php';
$cal = new Calendar($dbo);
?>

<div id="content">
    <?php echo $cal->displayEvent($id) ?>
    <a href="./">&laquo; 返回日历页面</a>
</div><!-- end #content -->

<?php
include_once 'assets/common/footer.inc.php';
?>