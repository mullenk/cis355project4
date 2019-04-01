<?php
session_start();
require "database.php";
require "customers.class.php";
if($_GET) $errorMessage = $_GET['errorMessage'];
else $errorMessage = '';
if($_POST){
    $success = false;
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM customers4 WHERE email = '$username' AND password_hash = '$password'";
    $q = $pdo->prepare($sql);
    $q->execute(array());
    $data = $q->fetch(PDO::FETCH_ASSOC);
    if($data){
        $_SESSION["username"] = $username;
        header("Location: customers4.php");
    }
    else{
        header("Location: login.php?errorMessage=Invalid");
        exit();
    }
}


?>
<html>
<h1>Log In </h1>
<form class ="form-horizontal" action="login.php" method="post">

        <input name="username" type="text" placeholder="me@email.com" required>
        <input name="password" type="password" required>
        <button type="submit" class="btn btn-success">Sign in</button>
        <p><a href='join.php' class='btn btn-success'>Join</a></p>
        <p style='color: red;'><?php echo $errorMessage; ?></p>
   
</form>
</html>