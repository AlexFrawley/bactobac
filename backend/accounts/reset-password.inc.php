<?php
 
if (!isset($_POST['reset-password-submit'])) {
    header("Location: ../../user.php");
    die();
}

require '../dbh.php';

$selector = mysqli_real_escape_string($conn, $_POST["selector"]);
$validator = mysqli_real_escape_string($conn, $_POST["validator"]);
$password = mysqli_real_escape_string($conn, $_POST["pwd"]);
$passwordRepeat = mysqli_real_escape_string($conn, $_POST["pwd-repeat"]);

if (empty($password) || empty($passwordRepeat)) {    
    header ("Location: ../../create-new-password.php?newpwd=empty");  
    exit();
} else if ($password != $passwordRepeat) {
    header ("Location: ../../create-new-password.php?newpwd=pwdnotsame");  
    exit();
}

$currentDate = date("U");


$sql = "SELECT * FROM pwdreset WHERE pwdResetSelector=? AND pwdResetExpires >= ?";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt,$sql)) {
    header("Location: ../../reset-password.php?alert=Server Error, please contact us");
    exit();
} else {
    mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    if (!$row = mysqli_fetch_assoc($result)) {
        header("Location: ../../reset-password.php?alert=re-submit your request");
        exit();
    } else { // if its valid-ish
        $tokenBin = hex2bin($validator);
        $tokenCheck = password_verify($tokenBin,$row["pwdResetToken"]);
        if ($tokenCheck === false) {
            header("Location: ../../reset-password.php?alert=resubmit your request");
            exit();
        } else if ($tokenCheck === true){
            $tokenEmail = $row['pwdResetEmail'];
                
            $sql = "SELECT * FROM users WHERE emailUsers=?;";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt,$sql)) {
                header("Location: ../../reset-password.php?error=ERROR");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (!$row = mysqli_fetch_assoc($result)) {
                    header("Location: ../../reset-password.php?error=Server Error");
                    exit();
                }
                else{
                    $sql = "UPDATE users SET pwdUsers=? WHERE emailUsers=?";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt,$sql)) {
                        header("Location: ../../reset-password.php?error=Server Error");
                        // echo "There was an error";
                        exit();
                    } else {
                        $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                        mysqli_stmt_bind_param($stmt, "ss", $newPwdHash, $tokenEmail);
                        mysqli_stmt_execute($stmt);
                            
                        $sql = "DELETE FROM pwdreset WHERE pwdResetEmail=?";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt,$sql)) {
                            header("Location: ../../reset-password.php?error=error 9");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                            mysqli_stmt_execute($stmt);
                            header("Location: ../../user.php?newpwd=passwordupdated");
                        }
                    }
                }
            }
        }
    }
}