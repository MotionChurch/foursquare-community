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


echo "<h3>Exceptions</h3>";
echo "<p><a href=\"exception.php\">New Exception</a></p>";

// List out exceptions in order.

$ui = new UserIterator();
$ui->query();

echo "<div class=\"userrow header\">"
    . "<span class=\"name\">Name</span>"
    . " <span class=\"email\">Email</span>"
    . " <span class=\"admin\">Admin</span>"
    . " <span class=\"actions\">Actions</span></div>";

foreach ($ui as $user) {
    printf("<div class=\"userrow\">"
        . "<span class=\"name\">%s</span>"
        . " <span class=\"email\"><a href=\"mailto:%s\">%s</a></span>"
        . " <span class=\"admin\">%s</span>"
        . " <span class=\"actions\">"
        . " <a class=\"smallbutton\" href=\"editor.php?id=%s\">edit</a>"
        . " <a class=\"smallbutton\" href=\"delete.php?id=%s\">delete</a></span></div>",
        $user->getName(),
        $user->getEmail(), $user->getEmail(),
        $user->isAdmin() ? 'Yes' : 'No',
        $user->getId(), $user->getId()
        );
}

require_once('../src/footer.inc.php');

?>
