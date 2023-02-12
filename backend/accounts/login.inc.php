<?php
if (isset($_POST['login-submit'])) {
    
    require '../dbh.php';
    
    $mailuid = mysqli_real_escape_string($conn, $_POST['mailuid']);
    $password = mysqli_real_escape_string($conn, $_POST['pwd']);

    if (empty ($mailuid) || empty($password)) {
        echo $mailuid;
        echo $password;
       header("Location: ../../user.php?error=empty fields"); 
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE uidUsers=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
            header("Location: ../../user.php?error=sqlerror"); 
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $mailuid);;
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $pwdCheck = password_verify($password, $row['pwdUsers']);
                if ($pwdCheck == false) {
                    header("Location: ../../user.php?error=wrongpwd&uid=". $mailuid); 
                    exit();
                } else if ($pwdCheck == true) {
                    session_start(['cookie_lifetime' => 172800,]);
                    $_SESSION['userUID'] = $row['uidUsers'];
                    $_SESSION['uidEmail'] = $row['emailUsers'];
                    $_SESSION['userData'] = $row['userData'];
                    $_SESSION['token'] = bin2hex(random_bytes(32));
                    header("Location: ../../user.php?login=success"); 
                    exit();
                } else {
                    header("Location: ../../user.php?error=wrongpwd"); 
                    exit();
                }
            }
            else{
                header("Location: ../../user.php?error=nouser"); 
                exit();
            }
        }
    }
}else {
    header("Location: ../../user.php"); 
    exit();
}
?>