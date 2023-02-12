<?php
 
if (!isset($_POST['reset-request-submit'])) {
    header("Location: ../../user.php");
}
require '../dbh.php';

$selector = bin2hex(random_bytes(8));
$token = random_bytes(32);

$url = "https://newgateportal.com/portal/user.php?selector=" . $selector . "&validator=" . bin2hex($token);

$expires = date("U") + 3600;

$userEmail = trim($_POST['email']);
if(empty($userEmail)){
    header("Location: ../../user.php?alert=Empty Field");
    exit();
}
if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../user.php?alert=Invalid Email");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE emailUsers=?");
$stmt->bind_param("s", $userEmail);
if ($stmt->execute() && $r = $stmt->get_result()) {
    if ($r->num_rows == 0) {
        header("Location: ../../user.php?alert=User does not exist");
        die();
    }
}
$userInfo = $r->fetch_assoc();

$sql = "DELETE FROM pwdreset WHERE pwdResetEmail=?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)){
    header("Location: ../../user.php?alert=Server Error, please contact us");
    exit();
} else {
    mysqli_stmt_bind_param($stmt, "s", $userEmail);
    mysqli_stmt_execute($stmt);
}

$sql = "INSERT INTO pwdreset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";

if (!mysqli_stmt_prepare($stmt,$sql)) {
    header("Location: ../../user.php?alert=Server Error, please contact us.");
    exit();
} else {
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
    mysqli_stmt_execute($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

$to = $userEmail;

$subject = 'Reset Your Password for Trade Entry System';

$message = '<p>We received a password reset request.  The link to reset your password is below.  If you did not make this request, ignore this email</p>';
$message .= '<p>Here is your password reset link <br>';  
$message .= '<a href=" '. $url . ' ">' . $url . '</a></p>';  

$headers = "From: Newgate support <support@newgateportal.com>\r\n";
$headers .= "Content-type: text/html\r\n";

$r = mail($to, $subject, $message, $headers);

header("Location: ../../user.php?reset=Request Successfully Submitted ".$r);