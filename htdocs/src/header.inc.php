<!DOCTYPE html>
<html>
<head>
    <title><?= $CONFIG['sitetitle'] ?></title>

    <link rel="stylesheet" href="<?= $CONFIG['urlroot'] ?>/css/main.css" />

</head>
<body>
<div id="header">
    <p><img src="<?= $CONFIG['urlroot'] ?>/images/logo.png" 
        alt="<?= $CONFIG['sitetitle'] ?>" /></p>

    <div id="about">
        Foursquare community is a place...
    </div>
</div>

<div id="nav">
    <ul>
        <li><a href="" class="bigbutton">Create Post</a></li>
        <li><a href="" class="bigbutton">Safety Tips</a></li>
        <li><a href="" class="bigbutton">Contact Us</a></li>
    </ul>
</div>

<div id="buttonblock">
    <p>What are you looking for?</p>
    <div>
        <a href="<?= $CONFIG['urlroot'] ?>/categories/forsale"><img src="<?= $CONFIG['urlroot'] ?>/images/tag.png" alt="For Sale" /></a>
        <br />
        <a href="<?= $CONFIG['urlroot'] ?>/categories/forsale" class="smallbutton">For Sale</a>
    </div>
    <div>
        <a href="<?= $CONFIG['urlroot'] ?>/categories/events"><img src="<?= $CONFIG['urlroot'] ?>/images/calendar.png" alt="Events" /></a>
        <br />
        <a href="<?= $CONFIG['urlroot'] ?>/categories/events" class="smallbutton">Events</a>
    </div>
    <div>
        <a href="<?= $CONFIG['urlroot'] ?>/categories/housing"><img src="<?= $CONFIG['urlroot'] ?>/images/house.png" alt="Housing" /></a>
        <br />
        <a href="<?= $CONFIG['urlroot'] ?>/categories/housing" class="smallbutton">Housing</a>
    </div>
    <div>
        <a href="<?= $CONFIG['urlroot'] ?>/categories/jobs"><img src="<?= $CONFIG['urlroot'] ?>/images/jobs.png" alt="Jobs" /></a>
        <br />
        <a href="<?= $CONFIG['urlroot'] ?>/categories/jobs" class="smallbutton">Jobs</a>
    </div>
        
</div>

<div id="content">
