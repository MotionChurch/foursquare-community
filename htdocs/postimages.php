<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "src/base.inc.php";

// Make sure we had a path info
if (!isset($_SERVER['PATH_INFO'])) {
    errorNotFound();
}

// Clean up the id in the path info.
$id = substr($_SERVER['PATH_INFO'], 1);

if (!is_numeric($id)) {
    errorNotFound();
}

// Get the post.
$post = Post::getByImage($id);

if (!$post or 
    (!isset($_SESSION['currentUser']) and $post->getStage() != 'approved')) {
    errorNotFound();
}

// Check if file exists.
$file = $CONFIG['uploads'] . "/$id";

if (!file_exists($file)) {
    echo $file;
    errorNotFound();
}

// Output the file
$info = getimagesize($file);
header('Content-Type: ' . $info['mime']);
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;

function errorNotFound() {
    header("HTTP/1.0 404 Not Found");
    exit;
}

?>

