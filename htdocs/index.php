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

echo "<h2>Recent Posts</h2>";

// Get all recent, approved posts.
$posts = new PostIterator();
$posts->filterStage('approved');
$posts->limit(5);
$posts->query();

if ($posts->valid()) {
    for ($i = 0; $i < 10; $i++) { 
    foreach ($posts as $id => $post) {
        printf("<div class=\"post\"><p><a href=\"". $GLOBALS['CONFIG']['urlroot']
                . "/postings/%s.html\">%s</a></p>"
                . "<div class=\"desc\"><span class=\"location\">%s</span>"
                . " <span class=\"age\">%s</span></div></div>",
                
                $id, $post->getName(), $post->getLocation(), $post->getAge());
    }
    }

} else {
    echo "<p>No recent posts.</p>";
}

require_once "src/footer.inc.php";

?>
