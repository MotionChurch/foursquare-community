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

$user = false;
if (isset($_GET['id']) and is_numeric($_GET['id'])) {
    $user = User::getById($_GET['id']);
    
    if ($user !== false and isset($_GET['confirmed'])) {
        $user->delete();
        
        header('Location: index.php');
    }

}

require_once('../src/header.inc.php');

echo "<h3>Delete Users</h3>";

if ($user !== false) {
    echo "<p>Are you sure you want to delete " . $user->getName() ."?</p>"
        . "<p><a href=\"delete.php?id=". $user->getId() ."&confirmed\">Yes</a>"
        . " <a href=\"index.php\">No</a></p>";

} else {
    echo "<p>No user to delete.</p>";
}

require_once('../src/footer.inc.php');

?>

