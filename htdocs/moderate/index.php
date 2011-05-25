<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once('../src/base.inc.php');

if (!isset($_SESSION['currentUser'])) {
    header('Location: ' . $CONFIG['urlroot'].'/moderate/login.php');
    exit();
}

require_once('header.inc.php');

?>

<h2>Welcome</h2>

<?php

// List posts to be approved
echo "<h3>Moderate Posts</h3>";

$posts = new PostIterator();
$posts->filterStage('moderation');
$posts->query();

// TODO: Also filter by source?

if ($posts->valid()) {
    foreach ($posts as $id => $post) {
        printf("<div class=\"post\"><p><a href=\"preview/%s.html\">%s</a></p>"
                . "%s <a href=\"mailto:%s\">%s</a></div>",

                $id, $post->getName(), $post->getCreated(), $post->getEmail(), $post->getEmail());
    }

} else {
    echo "<p>No posts awaiting approval</p>";
}

require_once('footer.inc.php');

?>
