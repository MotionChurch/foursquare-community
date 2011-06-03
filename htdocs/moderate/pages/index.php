<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once('../../src/base.inc.php');

// Verify User is admin
if (!$_SESSION['currentUser']->isAdmin()) {
    header('Location: ' . buildUrl('moderate/'));
    exit;
}

$error = '';

require_once('../src/header.inc.php');

echo "<h3>Pages</h3>";

echo "<p><a class=\"bigbutton\" href=\"editor.php\">Create Page</a></p>";

$pi = new PageIterator();
$pi->query();

if ($pi->valid()) {
    echo "<div class=\"userrow header\">"
        . "<span class=\"name\">Title</span>"
        . " <span class=\"actions\">Actions</span></div>";

    foreach ($pi as $page) {
        printf("<div class=\"userrow\">"
            . "<span class=\"name\"><a href=\"%s.html\">%s</a></span>"
            . " <span class=\"actions\">"
            . " <a class=\"smallbutton\" href=\"editor.php?id=%s\">edit</a>"
            . " <a class=\"smallbutton\" href=\"delete.php?id=%s\">delete</a></span></div>",
            buildUrl('page/' . $page->getURL()), $page->getTitle(),
            $page->getId(), $page->getId()
            );
    }

} else {
    echo "<p>There are no pages to edit. Click the button above to create one.</p>";
}

require_once('../src/footer.inc.php');

?>
