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


// Make sure we had a path info
if (!isset($_SERVER['PATH_INFO'])) {
    errorNotFound();
}

// Clean up the id in the path info.
$id = substr($_SERVER['PATH_INFO'], 1, strpos($_SERVER['PATH_INFO'], '.') - 1);

// Get the Page.
$page = Page::getByUrl($id);

if (!$page) {
    errorNotFound();
}

// Display the page.

echo $page->getContent();

require_once "src/footer.inc.php";

function errorNotFound() {
    // TODO: Better 404 error
    echo "404";
    exit;
}

?>

