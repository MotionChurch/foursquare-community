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

echo "<h2>Email Verification</h2>";

if (isset($_GET['id'])) {
    $id = addslashes($_GET['id']);
    $post = Post::getBySecretId($id);

    if ($post) {
        $post->verify();
        $post->save();

        echo "<p>Your email address has been validated. Your post will be listed"
            . " as soon as we approve the content. You will recieve an email when"
            . " the post is approved.</p>";
    
    } else {
        echo "<div class=\"error\">Invalid validation ID provided.</div>";
    }

} else {
    echo "<div class=\"error\">No validation ID provided.</div>";
}

require_once "src/footer.inc.php";

?>
