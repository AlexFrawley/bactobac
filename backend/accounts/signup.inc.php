<?php
if (isset($_POST)) {
    //  exit("Not accepting new accounts");
    require '../dbh.php';
    
    $email = mysqli_real_escape_string($conn, $_POST['mail']);
    $username = mysqli_real_escape_string($conn, $_POST['uid']);
    // $password = mysqli_real_escape_string($conn, $_POST['pwd']);
    // $passwordRepeat = mysqli_real_escape_string($conn, $_POST['pwd-repeat']);
    $password = $_POST['pwd'];
    $passwordRepeat = $_POST['pwdRepeat'];
    $userData = "";

    
    if (empty($username) || empty($email)) {
        header("Location: ../../user.php?error=emptyfields&uid=".$username."&mail=".$email); 
        exit();
    }
    
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../../user.php?error=invalidmailuid"); 
        exit();
    }
    
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../../user.php?error=invalidemail&uid=".$username); 
        exit();
    }
    else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../../user.php?error=invaliduid&mail=".$email); 
        exit();
    }
    
    else {
        $sql = "SELECT uidUsers FROM users WHERE uidUsers=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../../user.php?error=sqlerror"); 
            exit();
        }
        else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: ../../user.php?error=usertaken&mail=".$email); 
                exit();
            }else {
            $sql = "INSERT INTO users (uidUsers, emailUsers, pwdUsers, userData) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../../user.php?error=sqlerror1"); 
                exit();
        } else {
                $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                
                mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $hashedPwd, $userData);
                mysqli_stmt_execute($stmt);
                
                header("Location: ../../user.php?signup=success"); 
                exit();
                
            }
        }
        }
        
    }
    mysqli_stmt_close($stmt);
    mysqli_close($close);
        
}
else {
    header("Location: ../../user.php"); 
                exit();
}
