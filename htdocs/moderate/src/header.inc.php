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

</head>
<body>
<div id="header">
    <p><a href="<?= $CONFIG['urlroot'] ?>">
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
        <li><a href="">Moderate Posts</a></li>
        
        <?php
            // Admin Navigation
            if ($_SESSION['currentUser']->isAdmin()) {
                echo "<li><a href=\"". $CONFIG['urlroot'] ."/\">Pages</a></li>";
                echo "<li><a href=\"". $CONFIG['urlroot'] ."/\">Users</a></li>";
            }
        ?>

        <li><a href="">Account Settings</a></li>
        <li><a href="">Logout</a></li>
    </ul>
</div>

