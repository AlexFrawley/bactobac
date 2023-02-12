<?php
session_start();
if(!isset($_SESSION) || $_SESSION == NULL){
    header("location: ../user.php?");
    die();
}
require 'dbh.php';

$ud = [$_POST['height'], $_POST['weight'], $_POST['sex']];
$userData = json_encode($ud);


$stmt = $conn->prepare("UPDATE `users` SET `userData` = ? WHERE `idUsers` = ?");
$stmt->bind_param("si", $userData, $_SESSION['userID']);
if($stmt->execute()) {
    $_SESSION['userData'] = $ud;
    header("Location: ../user.php");
    die();
}
header("../user.php?error");