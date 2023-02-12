<header id="header" class="header fixed-top d-flex align-items-center">

<div class="d-flex align-items-center justify-content-between" style="width:100%">
  <a href="." class="nav-link logo d-flex align-items-center">
    <!-- <img src="" alt="logo"><b style="padding-left:1rem">Home</b> -->
    <b>Home</b>
  </a>

  <nav class="header-nav ms-auto">
    <a href="user.php" class="nav-link nav-icon"><?=$loggedIn ? $_SESSION['userUID'] : 'login'?></a>
    
  </nav>
</div>

</header>