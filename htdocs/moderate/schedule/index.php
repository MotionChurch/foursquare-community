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
echo "<p><a href=\"exception.php\">New Exception</a></p>";

// List out exceptions in order.
$exceptions = new ModerationExceptions();
$exceptions->query();

echo "<div class=\"userrow header\">"
    . "<span class=\"name\">Name</span>"
    . " <span class=\"email\">Email</span>"
    . " <span class=\"admin\">Exception Week</span>"
    . " </div>";

while ($exceptions->valid()) {
    $user = $exceptions->current();

    printf("<div class=\"userrow\">"
        . "<span class=\"name\">%s</span>"
        . " <span class=\"email\"><a href=\"mailto:%s\">%s</a></span>"
        . " <span class=\"admin\">%s</span></div>",
        $user->getName(),
        $user->getEmail(), $user->getEmail(),
        date('F j', $exceptions->key())
        );
    


    $exceptions->next();
}



require_once('../src/footer.inc.php');

?>
