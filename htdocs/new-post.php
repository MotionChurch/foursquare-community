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

echo "<h2>Submit Post</h2>";

echo "<form action=\"submit-post.php\" method=\"post\">";
echo "<p><label>Category: <select name=\"category\">";
foreach (Category::getCategories() as $short => $name) {
    echo "<option name=\"$short\">$name</option>";
}
echo "</select></label</p>";

echo "<p><label>Title: <input type=\"text\" name=\"title\" /></label></p>";

echo "<p><label for=\"desc\">Description:</label></p>";
echo "<p><textarea name=\"description\" id=\"desc\" rows=\"10\""
        . " cols=\"80\"></textarea></p>";

// TODO: Link to terms of service.
echo "<p><label><input type=\"checkbox\" name=\"tos\" value=\"1\" /> I agree to the terms of service.</label></p>";

echo "<p><input type=\"submit\" value=\"Submit\" /></p></form>";

require_once "src/footer.inc.php";

?>
