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

// Do we have a path info?
if (isset($_SERVER['PATH_INFO'])) {
    // Get the category
    $category = addslashes(substr($_SERVER['PATH_INFO'], 1));

    $category = Category::getByShortname($category);
    if ($category) {
        displayEvents($category);
    
    } else {
        // Bad category. List them all.
        listCategories();
    }

} else {
    // No category. List them all.
    listCategories();
}


function listCategories() {
    echo "<h2>Categories</h2>";

    $cats = Category::getCategories();
    foreach ($cats as $short => $name) {
        $url = $GLOBALS['CONFIG']['urlroot'] . "/categories/$short";
        echo "<p><a href=\"$url\">$name</a></p>";
    }
}

function displayEvents($category) {
    echo "<h2>". $category->getName() ."</h2>";

    // Get all recent, approved posts.
    $posts = new PostIterator();
    $posts->filterCategory($category->getId());
    $posts->filterStage('approved');
    $posts->query();

    if ($posts->valid()) {
        foreach ($posts as $id => $post) {
            printf("<div class=\"post\"><p><a href=\"postings/%s.html\">%s</a></p>"
                    . "<div class=\"desc\"><span class=\"location\">%s</span>"
                    . " <span class=\"age\">%s</span></div></div>",
                    
                    $id, $post->getName(), $post->getLocation(), $post->getAge());
        }

    } else {
        echo "<p>No recent posts.</p>";
    }
}

require_once "src/footer.inc.php";

?>

