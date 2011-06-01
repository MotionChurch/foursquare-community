<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "src/base.inc.php";

require_once "src/header.inc.php";

// Make sure we have all the needed information
if (!isset($_GET['id']) or !is_numeric($_GET['id'])
    or !isset($_GET['secret'])) {
    errorNotFound();
}

// Get the post.
$post = Post::getById($_GET['id']);

// Got a post with the right secretid?
if (!$post and $post->getSecretId() == $_GET['secret']) {
    errorNotFound();
}

if (isset($_GET['confirmed'])) {
    // Delete post
    $post->delete();

    echo "<p>Your post has been removed.</p>";

    echo "<p><a href=\"". $GLOBALS['CONFIG']['urlroot']
        ."\">Return to homepage</a>.</p>";

} else {
    // Are you sure...
    echo "<p>Are you sure you want to remove your posting titled "
        . $post->getName() ."?</p>";
    echo "<p><a href=\"". $_SERVER['REQUEST_URI']
        ."&confirmed\">Yes, delete it</a> ";
    echo "<a href=\"". $GLOBALS['CONFIG']['urlroot']
        ."\">No, do not delete</a></p>";
}

require_once "src/footer.inc.php";

function errorNotFound() {
    // TODO: Better 404 error
    echo "404";
    exit;
}

?>

