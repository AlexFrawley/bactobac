<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login</title>
<?php
require 'assets/inc/head.inc.php';
?>
</head>
<body>
<?php
require 'assets/inc/navbar.inc.php';
?>
<main id="main" class="main">
<div class="container px-4 py-5" id="featured-3">
    <?php
    if ($loggedIn) {
      ?>
      <p>You're logged in as <b><?=$_SESSION['userUID']?></b></p>
      <h3>Enter your info here</h3>
      <form action="" class="mb-3">
        <div class="mb-1">
          <label> Height</label>
          <input class="form-control" type="text" name="height" placeholder="Height" autofocus required>
        </div>
        <div class="mb-1">
          <label> Weight</label>
          <input class="form-control" type="text" name="weight" placeholder="Height" autofocus required>
        </div>
        <div class="mb-1">
          <label> Sex</label>
          <select class="form-select" name="sex">
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>
      </form>
      <a href="backend/accounts/logout.inc.php" class="btn btn-danger">Logout</a>
      <?php
    } else {
    ?>
    
    <h1 class="pb-2 border-bottom">Login</h1>
    <form action="backend/accounts/login.inc.php" method="post">
      <div class="mb-1">
        <label> Username</label>
        <input class="form-control" type="text" name="mailuid" placeholder="Username" class="nav-username-password" autofocus required>
      </div>
      <div class="mb-1">
        <label> Password</label>
        <input class="form-control" type="password" name="pwd" placeholder="Password" class="nav-username-password" required>
      </div>
        <button type="submit" name="login-submit" class="btn btn-primary login-btn">Login</button></form><br>
    </form>
    <h1>Signup! </h1>
    <form method="post" action="backend/accounts/signup.inc.php">
        <div class="mb-1">
            <label> Username</label>
            <input class="form-control" type="text" name="uid" placeholder="Username" class="nav-username-password" required>
        </div>
        <div class="mb-1">
            <label> Email</label>
            <input class="form-control" type="email" name="mail" placeholder="Email" class="nav-username-password" required>
        </div>
        <div class="mb-1">
            <label> Password</label>
            <input class="form-control" type="password" name="pwd" placeholder="Password" class="nav-username-password" required>
        </div>
        <div class="mb-1">
            <label> Repeat Password</label>
            <input class="form-control" type="password" name="pwdRepeat" placeholder="Password" class="nav-username-password" required>
        </div>
        <button type="submit" name="login-submit" class="btn btn-primary login-btn">Login</button></form><br>
    </form>
    <?php } ?>