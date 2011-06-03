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
if (!$_SESSION['currentUser']->isAdmin()) {
    header('Location: ' . buildUrl('moderate/'));
    exit;
}

$error = '';

// Get the current user object.
$user = new User();
if (isset($_GET['id']) and is_numeric($_GET['id'])) {
    $user = User::getById($_GET['id']);
}

// Save changes?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Name
    if (isset($_POST['name']) and trim($_POST['name']) != '') {
        $user->setName($_POST['name']);

    } else {
        $error .= '<p>Name is a required field.</p>';
    }

    // Email
    if (isset($_POST['email']) and trim($_POST['email']) != '') {
        $user->setEmail($_POST['email']);

    } else {
        $error .= '<p>Email is a required field.</p>';
    }

    // Source
    if (isset($_POST['source']) and trim($_POST['source']) != '') {
        $user->setSource($_POST['source']);

    } else {
        $error .= '<p>Source is a required field.</p>';
    }

    // Set Admin
    $admin = isset($_POST['admin']) and $_POST['admin'] == '1';
    $user->setAdmin($admin);

    // Set Notify
    $notify = isset($_POST['notify']) and $_POST['notify'] == '1';
    $user->setNotify($notify);

    // Send new password
    if (isset($_POST['newpass']) and $_POST['newpass'] == '1') {
        $user->sendNewPassword();
    }

    // Save the user
    if ($error == '') {
        if ($user->save()) {
            // Return to users list
            header("Location: index.php");

        } else {
            $error .= '<p>An error has occured.</p>';
        }
    }
}

require_once('../src/header.inc.php');

echo "<h3>Edit User</h3>";

if ($error != '') {
    echo "<div class=\"errorbox\">$error</div>";
}

$url = "editor.php";

if (isset($_GET['id'])) {
    $url .= '?id=' . $_GET['id'];
}

echo "<form action=\"$url\" method=\"post\">";

?>

<p><label>Name: <input type="text" name="name" value="<?= $user->getName() ?>" /></label></p>
<p><label>Email: <input type="text" name="email" value="<?= $user->getEmail() ?>" /></label></p>
<p><label>Source: <?php sourceDropdown('source', $user->getSource()) ?></label></p>
<p><label>
<input type="checkbox" name="admin" value="1"  <?= $user->isAdmin() ? 'checked="checked"' : '' ?> />
Administrator</label></p>
<p><label>
<input type="checkbox" name="notify" value="1" <?= $user->getNotify() ? 'checked="checked"' : '' ?> />
Notify of posts</label></p>

<?php
    if (isset($_GET['id'])) {
        echo "<p><label><input type=\"checkbox\" name=\"newpass\" value=\"1\" />"
            . "Send new password</label></p>";
    }
?>

<p>
<input type="submit" class="bigbutton" value="Save" />
<a href="index.php" class="bigbutton">Cancel</a>
</p>


</form>

<?php

function sourceDropdown($name, $select) {
    echo "<select name=\"$name\">";
    
    foreach(Source::getSources() as $source) {
        if ($source->getId() == $select) {
            echo "<option value=\"". $source->getId()
                ."\" selected=\"selected\">"
                . $source->getName() ."</option>";

        } else {
            echo "<option value=\"". $source->getId() ."\">"
                . $source->getName() ."</option>";
        }
    }

    echo "</select>";
}

require_once('../src/footer.inc.php');

?>

