<?php
/*
 * Created on 2012-4-25 by xiongxuebing
 */
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);
/*
 * Include necessary files
 */
include_once '../sys/core/init.inc.php';
/*
 * Output the header
 */
$page_title = "Please Log In";
$css_files = array("style.css", "admin.css");
include_once 'assets/common/header.inc.php';
$admin_object = new Admin();
?>
<div id="content">
    <?php print $admin_object->displayLoginForm(); ?>
</div><!-- end #content -->
<?php
/*
 * Output the footer
 */
include_once 'assets/common/footer.inc.php';
?>