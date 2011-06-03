<?php

// Require Authentication
if (!isset($_SESSION['currentUser'])) {
    header('Location: ' . $CONFIG['urlroot'].'/moderate/login.php');
    exit();
}

?><!DOCTYPE html>
<html>
<head>
    <title><?= $CONFIG['sitetitle'] ?> Moderation</title>

    <link rel="stylesheet" href="<?= $CONFIG['urlroot'] ?>/css/main.css" />
    <link rel="stylesheet" href="<?= $CONFIG['urlroot'] ?>/moderate/admin.css" />

    <script type="text/javascript" src="<?= $CONFIG['urlroot'] ?>/js/tiny_mce/tiny_mce.js" ></script>
    <script type="text/javascript" >
        tinyMCE.init({
                mode : "exact",
                elements : "contentarea",
                theme : "advanced",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left"
        });
    </script>
</head>
<body>
<div id="header">
    <p><a href="<?= buildUrl() ?>">
        <img src="<?= $CONFIG['urlroot'] ?>/images/logo.png" 
        alt="<?= $CONFIG['sitetitle'] ?>" /></a></p>

    <div id="about">
        Foursquare community is a place where you can find help,
        sell merchandise, list events or even post your rental.
        We want to build a help you get connected to the community of our church!
    </div>
</div>

<h1><?= $CONFIG['sitetitle'] ?> Moderation</h1>

<div id="content">

<div id="modnav">
    <ul>
        <li><a href="<?= buildUrl('moderate/') ?>">Moderate Posts</a></li>
        
        <?php
            // Admin Navigation
            if ($_SESSION['currentUser']->isAdmin()) {
                echo "<li><a href=\"". buildUrl('moderate/pages/') ."\">Pages</a></li>";
                echo "<li><a href=\"". buildUrl('moderate/users/') ."\">Users</a></li>";
            }
        ?>

        <li><a href="<?= buildUrl('moderate/account.php') ?>">
            Account Settings</a></li>
        <li><a href="">Logout</a></li>
    </ul>
</div>

