<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once('../../src/base.inc.php');

// Verify User is admin
if (!isset($_SESSION['currentUser']) or !$_SESSION['currentUser']->isAdmin()) {
    header('Location: ' . buildUrl('moderate/'));
    exit;
}

$error = '';

// Get the current user object.
$page = new Page();
if (isset($_GET['id']) and is_numeric($_GET['id'])) {
    $page = Page::getById($_GET['id']);
}

// Save changes?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Title
    if (isset($_POST['title']) and trim($_POST['title']) != '') {
        $page->setTitle($_POST['title']);

    } else {
        $error .= '<p>Title is a required field.</p>';
    }

    // URL
    if (isset($_POST['url']) and trim($_POST['url']) != '') {
        $page->setURL($_POST['url']);

    } else {
        $error .= '<p>URL is a required field.</p>';
    }

    // Content
    if (isset($_POST['content']) and trim($_POST['content']) != '') {
        $page->setContent($_POST['content']);

    } else {
        $error .= '<p>Content is a required field.</p>';
    }

    // Save
    if ($error == '') {
        if ($page->save()) {
            // Return to pages list
            header("Location: index.php");

        } else {
            $error .= '<p>An error has occured.</p>';
        }
    }
}

require_once('../src/header.inc.php');

echo "<h3>Edit Page</h3>";

if ($error != '') {
    echo "<div class=\"errorbox\">$error</div>";
}

$url = "editor.php";

if (isset($_GET['id'])) {
    $url .= '?id=' . $_GET['id'];
}

echo "<form action=\"$url\" method=\"post\">";

?>

<p><label>Title: <input type="text" name="title" value="<?= $page->getTitle() ?>" /></label></p>
<p><label>URL: <input type="text" name="url" value="<?= $page->getURL() ?>" /></label></p>
<p><textarea id="contentarea" name="content" rows="50" cols="120"><?= $page->getContent() ?></textarea></p>

<p>
<input type="submit" class="bigbutton" value="Save" />
<a href="index.php" class="bigbutton">Cancel</a>
</p>


</form>

<?php

require_once('../src/footer.inc.php');

?>

