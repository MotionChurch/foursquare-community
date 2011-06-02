<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once('../src/base.inc.php');

$error = '';

// Handle form?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_SESSION['currentUser'];

    // Change password
    if (isset($_POST['oldpassword'])      and trim($_POST['oldpassword']) != ""
        and isset($_POST['newpassword'])  and trim($_POST['newpassword']) != ""
        and isset($_POST['newpassword2']) and trim($_POST['newpassword2']) != "") {

        if ($user->authenticate($_POST['oldpassword'])) {
            if ($_POST['newpassword'] == $_POST['newpassword2']) {
                $user->setPassword($_POST['newpassword']);
            
            } else {
                $error .= '<p>Passwords do not match.</p>';
            }
        } else {
            $error .= '<p>"Old Password" does not match your current password.</p>';
        }
    }

    // Set Notify
    $notify = isset($_POST['notify']) and $_POST['notify'] == '1';
    $user->setNotify($notify);

    $user->save();
}


require_once('src/header.inc.php');

echo "<h3>Your Account</h3>";

if ($error != '') {
    echo "<div class=\"errorbox\">$error</div>";
}

?>

<form action="" method="post">
<p>To change your password, enter your old and new passwords
below.</p>
<p><label>Old Password:
    <input type="password" name="oldpassword" /></label></p>
<p><label>New Password:
    <input type="password" name="newpassword" /></label></p>
<p><label>Confirm Password:
    <input type="password" name="newpassword2" /></label></p>

<div style="margin-top: 2em; margin-bottom: 2em;">
<p><label><input type="checkbox" name="notify" value=\"1\" <?php
echo $_SESSION['currentUser']->getNotify() ? 'checked="checked"' : '';
?>/>
    Notify when posts are created.</label></p>
</div>

<p><input type="submit" value="Update Account" /></p>

</form>

<?php


require_once('src/footer.inc.php');

?>
