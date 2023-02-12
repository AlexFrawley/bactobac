<?php
date_default_timezone_set('America/New_York');
session_start();
if(!isset($_SESSION) || $_SESSION == NULL){
    header("location: ../user.php");
    die();
}

require 'dbh.php';

if(!is_numeric($_POST['mins']) || $_POST['mins'] < 0) {
    $_POST['mins'] = 0;
}

$drinkTime = date("Y-m-d H:i", strtotime('-'.$_POST['mins'].'minutes')).":00";
// die();
// $drinkTime = 
$drinkData = [$_POST['volume'], $_POST['percentABV']];
$drinkData = json_encode($drinkData);

$stmt = $conn->prepare("INSERT INTO `drinksDrank` (`userID`, `dateTime`, `drinkData`) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $_SESSION['userID'], $drinkTime, $drinkData);
if($stmt->execute()) {
    header("Location: ../");
    die();
} else {
    header("Location: ../index.php?error");
    die();
}