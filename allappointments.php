<?php
    session_start();
    $db=mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
    $msg = [];
    //echo $_SESSION['username'];
    if(!isset($_SESSION['username']))
    {
        $_SESSION['redirect'] = 'allappointments.php';
        header("location: login.php");
    }
    if($_SESSION['role'] != 'admin')
    header("location: index.php");
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>All Appointments</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles-1.css">
    <link href="style10-1.css" type="text/css"rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:500&display=swap" rel="stylesheet">
    <style>
        .menu a{
            color:white;
        }
        a{
            color:dodgerblue;
        }
        </style>
</head>
<body>
  <center>
  <?php require_once("header.php");?>
  <div class="content-section" style="max-width:80vw">
    <h3>All Appointments</h3><br>
    <?php 
    if(sizeof($msg)>0)
    {
        foreach($msg as $m)
        {
            echo $m;
        }
    }
    ?>
    <br>
    <table>
        <tr>
            <th>Patient</th>
            <th>Dentist</th>
            <th>Location</th>
            <th>Date/Time</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <tr class='flex' id=14>
            <td></td>
            <td></td>
            <td></td>
            <td>2024-06-29<br>1500HRS</td>
            <td>Braces Re-aligning</td>
            <td>Pending<br'>
            <div style='padding-top:10px;'>
                <button style='padding:8px;' onClick='showPopup(14, 1)'>
                    <i class='fa fa-check' style='color:green'></i>
                </button>
                <button style='padding:8px;' onClick='showPopup(14, 2)'>
                    <i class='fa fa-times' style='color:red'></i>
                </button>
            </div>
        </td><td>
                    <form action='createinvoice.php' method='POST' style='margin-bottom:8px'>
                        <input type='hidden' name='appt_id' value='14'>
                        <input type='hidden' name='patient_id' value='1'>
                        
                        <button type='submit' style='padding:8px;'><a href='#'><i class='fa fa-file-text' style='padding-right:8px'></i></a>Create Invoice</button>
                    </form>
                    
            <div style='display:flex; gap:8px'>
            <button style='padding:8px;'>
            <a href='reschedule.php?appt=14' disabled>Reschedule</a>
            </button>
            <button style='padding:8px;'>
            <a href='#' style='color:red' onClick='showPopup(14, 0)'>Cancel</a></td></tr>
            </button>
        </tr>
    </center>
</body>
</html>                    