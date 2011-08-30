<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "src/base.inc.php";

// Check if we need to login first...
if (isset($_GET['moderate']) and !isset($_SESSION['currentUser'])) {
    header('Location: ' . $CONFIG['urlroot'].'/moderate/login.php');
    exit();
}

require_once "src/header.inc.php";


// Make sure we had a path info
if (!isset($_SERVER['PATH_INFO'])) {
    errorNotFound();
}

// Clean up the id in the path info.
$id = substr($_SERVER['PATH_INFO'], 1, strpos($_SERVER['PATH_INFO'], '.') - 1);

if (!is_numeric($id)) {
    errorNotFound();
}

// Get the post.
$post = Post::getById($id);


if (!$post or (!isset($_SESSION['currentUser']) and $post->getStage() != 'approved')) {
    errorNotFound();
}

if (isset($_SESSION['currentUser'])) {

    if ($post->getStage() != 'approved') {
        // Post waiting for approval...
        echo "<div class=\"moderationbox\">You are moderating this post: ";
        printf("<a href=\"../moderate/moderate.php?id=%s&action=approve\">approve</a> "
            . " or <a href=\"../moderate/moderate.php?id=%s&action=reject\">reject</a>."
            , $post->getid(), $post->getid());

        // Print Source information
        printSourceInfo($post);

        echo "<p><a href=\"../moderate/index.php\">return to moderation</a></p>";
        echo "</div>";


    } else {
        // Post already approved
        echo "<div class=\"moderationbox\">Administrative options:<br />";
        
        printf("<a href=\"../moderate/moderate.php?id=%s&action=delete\">delete post</a><br />"
            . "<a href=\"../moderate/moderate.php?id=%s&action=reject\">reject post</a>",
            $post->getid(), $post->getid());

        printSourceInfo($post);
        
        echo "</div>";
    }

}


// Display the post.

echo "<h2>". $post->getName();

if ($post->getPrice() != 0) {
    echo ' - $' . $post->getPrice();
}

echo "</h2>";

echo "<p>Category: " . $post->getCategory()->getName() . "</p>";
echo "<p>Date: ". date('r', $post->getTimestamp()) ."</p>";
echo "<p>Email: <a href=\"mailto:". $post->getPublicEmail() ."\">"
    . $post->getPublicEmail() ."</a></p>";
echo "<p>Location: ". $post->getLocation() ."</p>";

echo "<p class=\"desc\">". 
    str_replace("\n", '<br />', $post->getDescription())
    ."</p>";

foreach ($post->getImages() as $imgid) {
    echo "<p><img src=\"". $GLOBALS['CONFIG']['urlroot']
        . "/postimages/$imgid\" /></p>";
}

require_once "src/footer.inc.php";

function printSourceInfo($post) {
    // Print Source information
    $source = $post->getSource();
    
    if ($source !== false) {
        // Get source from source id
        $sourceObj = Source::getById($source);
        $source = $sourceObj->getName();
        

    } else {
        // Get other
        $source = $post->getOtherSource();
    }

    printf("<p>This post was posted by %s from %s.</p>",
        $post->getEmail(), $source);
}

function errorNotFound() {
    // Get the 404 page
    $page = Page::getByUrl('404');
    if ($page) {
        echo $page->getContent();
    } else {
        echo "Error: Page not found.";
    }
    require_once "src/footer.inc.php";
    exit;
}

?>
