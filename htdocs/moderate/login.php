<?php

require_once('../src/base.inc.php');

$error = "";

if (isset($_GET['logout'])) {
    unset($_SESSION['currentUser']);
}

if (isset($_POST['login_email']) and isset($_POST['login_password'])) {
    $user = User::getByEmail(addslashes($_POST['login_email']));

    if ($user and $user->authenticate($_POST['login_password'])) {
        $_SESSION['currentUser'] = $user;

        header("Location: index.php");

    } else {
        $error = "<div class=\"error\">Invalid Username/Password</div>";
    }
}

require_once('header.inc.php');

?>


<h2>Login</h2>

<?php echo $error; ?>

<form method="post">

<label>Email</label>
<div class="element">
    <input type="text" name="login_email" />
</div>

<label>Password</label>
<div class="element">
    <input type="password" name="login_password" />
</div>

<div>
    <input type="submit" value="Login" />
</div>

</form>

<?php
    
require_once('footer.inc.php');

?>
