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

// If we have a valid id.
if (isset($_GET['id']) and is_numeric($_GET['id'])) {

    // Get the post.
    $post = Post::getById($_GET['id']);

    if ($post) {
        // Accept or Reject.
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'approve':
                    $post->approve();
                    break;

                case 'reject':
                    $post->reject();
                    break;

                case 'delete':
                    if ($_SESSION['currentUser']->isAdmin()) {
                        $post->delete();
                    }
                    break;
            }

            $post->save();
        }
    }
}

// Redirect back to the moderation index.
header('Location: ' . $CONFIG['urlroot'] . '/moderate');

?>
