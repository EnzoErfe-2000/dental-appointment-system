<?php
session_start();
$db=mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
if(isset($_GET['logout'])){

    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['role']);
    unset($_SESSION['dentistName']);
    //unset($_COOKIE['remember']);
    header("location: login1.php");
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
        <div id="mobile__menu" class="overlay">
            <a class="close">&times;</a>
            <div class="overlay__content">
                <a href="index.php">Home</a>
                <!-- <a href="registration.php">Sign up</a> -->
                <a href="schedules.php">Schedules</a>
                <a href="check-appointment.php">My Appointment</a>
                <a href="login.php">Login</a>
            </div>
        </div>
        <div class="homemain">
            <div class="container">
                <div class="column">
                    <div class="sub-div">
                        <div class="caption">
                        <?php if(isset($_SESSION['username'])):?>

                            <h2>Welcome <strong><?php echo $_SESSION['username'] ; ?> </strong>
                        </h2>
                        <?php endif; ?>
                            <a class="w">We prioritise your</a>
                            <h1>NEW SMILE</h1>
                            <!--<button class="ta" href="#">Read more</button>-->    
                        </div>
                    </div>
                    <?php if(isset($_SESSION['success'])):?>
                    <div>
                        <h3>
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success'])
                        ?>
                        </h3>
                    </div>
                    <?php endif;?>
                </div>

                <div class="column">
                    <div class="sub-div">
                        <div class="main">
                            <div>
                                <a><img src="images/main.jpg" alt="main"></a>
                            </div>
                        </div>
                    </div>
                    <div class="sub-div">
                        <div style='display:flex; flex-direction:column;'>
                        <?php 
                        if (isset($_SESSION['username']) && isset($_SESSION['username'])){
                            if ($_SESSION['role'] == 'dentist') {
                                echo "<a class='cta' href='makeappointment1.php?dentist=".$_SESSION['dentistName']."'>Create an appointment for Dr. ".$_SESSION['dentistName']."</a>";
                            } 
                            else if ($_SESSION['role'] == 'admin') {
                                echo "<div style='display:flex; justify-content:center;'>Manage</div><br><div><a class='cta' href='addclinic1.php'>Clinics</a><a class='cta' href='adddentist1.php'>Dentists</a></div>";
                            }
                        } 
                        else {
                            echo "<a class='cta' href='schedules.php'>Book an appointment today!</a>";
                            echo "";
                         }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="mobile.js"></script>
<!-- </center> -->
</body>
</html>