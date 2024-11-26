<?php
session_start();

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>