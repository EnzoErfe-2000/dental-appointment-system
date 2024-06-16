<?php
session_start();
$db=mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
if(isset($_GET['logout'])){

    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['role']);
    //unset($_COOKIE['remember']);
    header("location: login.php");
}

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Home</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="styles-1.css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:500&display=swap" rel="stylesheet">
    </head>
	<body>
        <?php require_once("header.php");?>
        
        <h2>Check Your Appointments</h2>

        <form action="appointments1.php" method="get">
            <label for="patient_id">Enter Your IC or Passport No.:</label><br>
            <input type="text" id="patientID" name="patientID" required><br><br>
            <input type="submit" value="Check Appointments">
        </form>
        
<!-- </center> -->
</body>
</html>