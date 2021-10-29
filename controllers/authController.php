<?php
session_start();

if($_SERVER["DOCUMENT_ROOT"]=='C:/xampp/htdocs'){//if local host
    define('ROOT', dirname(__DIR__));
}else{
    define('ROOT', $_SERVER["DOCUMENT_ROOT"]);
}

require ROOT.'/config/db.php';




$errors = array(); //will be available on signup
$username = "";
$email = "";

//if user click on the sign up button
if (isset($_POST['signup-btn'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConf = $_POST['passwordConf'];

    //validation
    if (empty($username)) {
        $errors['username'] = "Username required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //IF TRUE INVALID
        $errors['email'] = "Email address is invalid";
    }
    if (empty($email)) {
        $errors['email'] = "Email required";
    }
    if (empty($password)) {
        $errors['password'] = "Password required";
    }
    if ($password !== $passwordConf) {
        $errors['password'] = "The two passwords do not match";
    }

    $emailQuery = "SELECT * FROM users WHERE email=? LIMIT 1"; // (?) using prepared statements, LIMIT1 if u see one record then stop searching
    $stmt = $conn->prepare($emailQuery);
    $stmt->bind_param('s', $email); //s- string , add email instead of ?
    $stmt->execute();
    $result = $stmt->get_result();
    $userCount = $result->num_rows;
    $stmt->close();

    if ($userCount > 0) {
        $errors['email'] = "Email already exists";
    }

    $userQuery = "SELECT * FROM users WHERE username=? LIMIT 1"; // (?) using prepared statements, LIMIT1 if u see one record then stop searching
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param('s', $username); //s- string , add email instead of ?
    $stmt->execute();
    $result = $stmt->get_result();
    $userCount = $result->num_rows;
    $stmt->close();

    if ($userCount > 0) {
        $errors['username'] = "Username already exists";
    }

    if (count($errors) === 0) {
        $password = password_hash($password, PASSWORD_DEFAULT); //ENCRYPT
        $token = bin2hex(random_bytes(50)); // unique random string of length 100
        $verified = false;

        $sql = "INSERT INTO users(email, username, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $email, $username, $password); //string string boolean.... 
        if ($stmt->execute()) {
            //login user
            $user_id = $conn->insert_id; // get the last inserted id from conn object
            $_SESSION['id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['verified'] = $verified;
            $_SESSION['avatar'] = 'default.png';



            //flash message
            $_SESSION['message'] = "You are now logged in";
            $_SESSION['alert-class'] = "alert-success";
           
            header('location: index.php');
            exit(); // not execute any other thing from here
        } else {
            $errors['db_error'] = "Database error: failed to register";
  
        }
    }
}

//if user clicks on the login button
if (isset($_POST['login-btn'])) {
    $username = $_POST['username']; //either email or username
    $password = $_POST['password'];

    //validation
    if (empty($username)) {
        $errors['username'] = "Username required";
    }
    if (empty($password)) {
        $errors['password'] = "Password required";
    }
    if (count($errors) === 0) {
        $sql = "SELECT * FROM users WHERE email=? OR username=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss',$username, $username); //user might enter either email or username
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc(); //return a user full ROW with * coloumns as an associative array

        //check if the user has the same password as registered

        if ($user != null && password_verify($password, $user['password'])) { //check if the given pwd from user match the encrypted pwd stored in DB
            //login success
            //login user
            settingUserSession($user);
            //flash message
            $_SESSION['message'] = "You are now logged in";
            $_SESSION['alert-class'] = "alert-success";
            header('location: index.php');
            exit(); // dont execute any other thing from here

        } else {
            $errors['login_fail'] = "Wrong username or password!";
        }
    }
}

//logout user
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);

    header('location: login.php?loggedOut=We hope to see you again ❤');
    exit();
}
function settingUserSession($user)
{
    $_SESSION['id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
}

function userNotAuthorizedToProject($id){
    global $conn;
    $pQuery = mysqli_query($conn, "SELECT user_id FROM project WHERE id='$id' LIMIT 1");
    $row = mysqli_fetch_assoc($pQuery);
    if ($row['user_id'] != $_SESSION['id']) {
      return true;
    } else {
      return false;
    }
  }

//verify user by token
function verifyUser($token)
{ // each user has a unique token
    global $conn;
    $sql = "SELECT * FROM users WHERE token='$token' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result); // get row as assoc array
        $update_query = "UPDATE users SET verified=1 WHERE token='$token'";

        if (mysqli_query($conn, $update_query)) { //if executed proberly user is updated/activated
            //log user in
            settingUserSession($user);
            $_SESSION['verified']=1;
            //flash message
            $_SESSION['message'] = "Your email address is successfully verified!";
            $_SESSION['alert-class'] = "alert-success";
            header('location: index.php');
            exit(); // not execute any other thing from here


        }
    } else { // if token not found from any user
        echo 'User not found';
    }
}