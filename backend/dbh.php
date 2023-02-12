<?php
$servername = "localhost"; //used because i used localhost for online insert ip address

// $dBUsername = "NGR-main"; //godaddy
// $dBPassword = ""; //godaddy

$dBUsername = "root"; // mamp
$dBPassword = "root"; // mamp

// $dBUsername = "btwobte1_TES"; // bluehost
// $dBPassword = ""; // bluehost

$dBName = "bactobac"; //godaddy & mamp
// $dBName = "btwobte1_TES"; //bluehost
$conn = mysqli_connect( $servername, $dBUsername, $dBPassword, $dBName);
if (!$conn) {
die("CONNECTION FAILED: " .mysqli_connect_error());  // check if connection is FAILED

}