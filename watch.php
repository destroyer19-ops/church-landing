<?php
// This script acts as a gateway for the "Watch Service" link.
// It checks if the user is a returning visitor by looking for a cookie.

$cookie_name = 'viewer_id';

if (isset($_COOKIE[$cookie_name])) {
    // The user has a viewer_id cookie, so they are a returning visitor.
    // Redirect them to the "welcome back" page.
    header("Location: returning-viewer.html");
} else {
    // The user does not have the cookie, so they are a new visitor.
    // Redirect them to the full registration page.
    header("Location: stream-register.html");
}
exit();
?>
