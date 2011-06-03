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

$page = false;
if (isset($_GET['id']) and is_numeric($_GET['id'])) {
    $page = Page::getById($_GET['id']);
    
    if ($page !== false and isset($_GET['confirmed'])) {
        $page->delete();
        
        header('Location: index.php');
    }

}

require_once('../src/header.inc.php');

echo "<h3>Delete Pages</h3>";

if ($page !== false) {
    echo "<p>Are you sure you want to delete " . $page->getTitle() ."?</p>"
        . "<p><a href=\"delete.php?id=". $page->getId() ."&confirmed\">Yes</a>"
        . " <a href=\"index.php\">No</a></p>";

} else {
    echo "<p>No page to delete.</p>";
}

require_once('../src/footer.inc.php');

?>

