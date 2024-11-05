<?php
session_start();
include('dbconfig.php');

if(isset($_POST['login']))
{
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT * FROM user where username='$username' and password='$password' LIMIT 1";
    $query_run = mysqli_query($con, $query);

    if(mysqli_num_rows($query_run) > 0)
    {
        $row = mysqli_fetch_array($query_run);

        // Authenticating Logged In User
        $_SESSION['authentication'] = true;

        // Storing Authenticated User data in Session
        $_SESSION['auth_user'] = [
            'user_id'=>$row['id'],
            'username'=>$row['username'],
            'password'=>$row['password'],
        ];

        $_SESSION['message'] = "You are Logged In Successfully"; //message to show
        header("Location: ../../underdev/index.html");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Invalid Email or Password"; //message to show
        header("Location: ../index.php");
        exit(0);
    }
}
?>