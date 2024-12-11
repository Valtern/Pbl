<?php
session_start();
session_destroy();

// Remove all session variables
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

header("Location: ../index.php");
exit();
?>
