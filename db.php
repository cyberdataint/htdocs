<?php
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Connect to the database
    $con = mysqli_connect("localhost", "root", "root", "recipes");

    // Check connection
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Set UTF-8 character encoding
    mysqli_set_charset($con, "utf8");
?>
