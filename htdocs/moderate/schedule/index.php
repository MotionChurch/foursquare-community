<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once('../../src/base.inc.php');


require_once('../src/header.inc.php');

echo "<h3>Moderation Schedule</h3>";

// List out moderators in order with next moderation week
$ui = new ModerationSchedule();
$ui->query();

echo "<div class=\"userrow header\">"
    . "<span class=\"name\">Name</span>"
    . " <span class=\"email\">Email</span>"
    . " <span class=\"admin\">Next Week</span>"
    . " </div>";

for ($i = 0; $i < $ui->getNumberOfModerators(); $i++, $ui->next()) {
    $user = $ui->current();

    printf("<div class=\"userrow\">"
        . "<span class=\"name\">%s %s</span>"
        . " <span class=\"email\"><a href=\"mailto:%s\">%s</a></span>"
        . " <span class=\"admin\">%s</span></div>",
        $user->getName(),
        $ui->isException() ? '*' : '',
        $user->getEmail(), $user->getEmail(),
        date('F j', $ui->key())
        );
}

echo "<h3>Exceptions</h3>";
echo "<p class=\"nomargin\"><a href=\"editor.php\">New Exception</a></p>";

// List out exceptions in order.
$exceptions = new ModerationExceptions();
$exceptions->query();

echo "<div class=\"userrow header\">"
    . "<span class=\"name\">Name</span>"
    . "<span class=\"name\">Substitute</span>"
    . " <span class=\"admin\">Week</span>"
    . " </div>";

while ($exceptions->valid()) {
    $user = $exceptions->getUser();
    $substitute = $exceptions->getSubstitute();

    $remove = "";

    if ($_SESSION['currentUser']->getId() == $user->getId()) {
        $remove = "<a href=\"remove.php?uid=" . $user->getId() ."&time=". $exceptions->key() ."\">remove</a>";
    }

    printf("<div class=\"userrow\">"
        . "<span class=\"name\">%s</span>"
        . "<span class=\"name\">%s</span>"
        . " <span class=\"admin\">%s</span>"
        . " <span class=\"remove\">%s</span></div>",
        $user->getName(),
        $substitute->getName(),
        date('F j', $exceptions->key()),
        $remove
        );
    


    $exceptions->next();
}



require_once('../src/footer.inc.php');

?>
