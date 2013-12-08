<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $page_title; ?></title>
        <?php foreach ($css_files as $css): ?>
            <link rel="stylesheet" type="text/css" media="screen,projection" href="assets/css/<?php echo $css; ?>" />
        <?php endforeach; ?>
    </head>
    <body>