<?php
// Start the session
session_start();

// Destroy the session, logging the user out
session_unset(); // Removes all session variables
session_destroy(); // Destroys the session

// Redirect the user to the login page (or any other page you prefer)
header("Location: login.php");
exit();
?>
