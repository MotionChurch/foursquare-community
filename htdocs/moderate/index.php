<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once('../src/base.inc.php');

require_once('src/header.inc.php');

?>

<?php

// List posts to be approved
echo "<h3>Moderate Posts</h3>";

$posts = new PostIterator();
$posts->filterStage('moderation');
$posts->query();

// TODO: Also filter by source?

if ($posts->valid()) {
    foreach ($posts as $id => $post) {
        printf("<div class=\"post\"><p><a href=\"%s/postings/%s.html?moderate\">%s</a></p>"
                . "%s <a href=\"mailto:%s\">%s</a> - %s</div>",

                $GLOBALS['CONFIG']['urlroot'],
                $id, $post->getName(),
                $post->getCreated(),
                $post->getEmail(), $post->getEmail(),
                $post->getSourceName());
    }

} else {
    echo "<p>No posts awaiting approval</p>";
}

require_once('src/footer.inc.php');

?>
