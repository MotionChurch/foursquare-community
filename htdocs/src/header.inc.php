<!DOCTYPE html>
<html>
<head>
    <title><?= $CONFIG['sitetitle'] ?></title>

    <link rel="stylesheet" href="<?= $CONFIG['urlroot'] ?>/css/main.css" />

</head>
<body>
<div id="header">
    <p><a href="<?= buildUrl() ?>">
        <img src="<?= $CONFIG['urlroot'] ?>/images/logo.png" 
        alt="<?= $CONFIG['sitetitle'] ?>" /></a></p>

    <div id="about">
        List-and-Share is a place where you can find help,
        sell merchandise, list events or even post your rental.
        We want to help you get connected to the community of the church!
    </div>
</div>

<div id="nav">
    <ul>
        <li><a href="<?= $CONFIG['urlroot'] ?>/new-post" class="bigbutton">
            Create Post</a></li>
        <li><a href="<?= buildUrl('page/safety.html') ?>" class="bigbutton">Safety Tips</a></li>
        <li><a href="<?= buildUrl('page/contact.html') ?>" class="bigbutton">Contact Us</a></li>
    </ul>
</div>

<div id="buttonblock">
    <p>What are you looking for?</p>
    <div>
        <a href="<?= $CONFIG['urlroot'] ?>/categories/free"><img src="<?= $CONFIG['urlroot'] ?>/images/free.png" alt="Free Items" /></a>
        <br />
        <a href="<?= $CONFIG['urlroot'] ?>/categories/free" class="smallbutton">Free Items</a>
    </div>
    <div>
        <a href="<?= $CONFIG['urlroot'] ?>/categories/forsale"><img src="<?= $CONFIG['urlroot'] ?>/images/tag.png" alt="For Sale" /></a>
        <br />
        <a href="<?= $CONFIG['urlroot'] ?>/categories/forsale" class="smallbutton">For Sale</a>
    </div>
    <div>
        <a href="<?= $CONFIG['urlroot'] ?>/categories/needs"><img src="<?= $CONFIG['urlroot'] ?>/images/needs.png" alt="Needs" /></a>
        <br />
        <a href="<?= $CONFIG['urlroot'] ?>/categories/needs" class="smallbutton">Needs</a>
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
