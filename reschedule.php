<?php
session_start();
$db=mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
$err = [];
if(isset($_GET['logout'])){

    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['role']);
    //unset($_COOKIE['remember']);
    header("location: login.php");
}

if(isset($_POST['update_appt'])) {
    $notposs = 0;

    $appointment_ID = mysqli_real_escape_string($db, $_POST['apptID']);
    $pic = mysqli_real_escape_string($db, $_POST['id_value']);
    $dname = substr(mysqli_real_escape_string($db, $_POST['dname']), 4);
    $date_val = mysqli_real_escape_string($db, $_POST['txtDate']);
    if (strlen($_POST['time']) == 5)
    $t1 = substr($_POST['time'], 0, 2).substr($_POST['time'], 3, 2);
    else 
    $t1 = substr($_POST['time'], 0, 2).substr($_POST['time'], 2, 2);
    $pmedical = mysqli_real_escape_string($db, $_POST['comments']);
    // echo $date_val." ".$t1." ".$pmedical;

    // TODO Check if new timeslot is booked
    // $checkq = "SELECT * FROM appointment WHERE patient_name = '$pname' AND time='$hr' AND date='$date_val' AND (status='Pending' OR status='Confirmed')";
    $checkq = "SELECT a.* FROM appointment a INNER JOIN patient b ON a.patient_id = b.patientID WHERE patientIC = '$pic' AND appt_time='$t1' AND appt_date='$date_val' AND (status='Pending' OR status='Confirmed')";
    $checkq2 = "SELECT a.* FROM appointment a INNER JOIN dentist b ON a.dentist_id = b.dentist_id WHERE dentist_name = '$dname' AND appt_time='$t1' AND appt_date='$date_val' AND status='Confirmed'";

    $res = mysqli_query($db, $checkq);
    $res2 = mysqli_query($db, $checkq2);
    // echo $checkq2;

    // If appointment already exists,
    if($res != false && mysqli_num_rows($res) > 0)
    {
        $notposs = 1;
        array_push($err, "<h3 style='color:red'>You already have a pending or confirmed appointment at the same time.</h3>");
    }
    if($res2 != false && mysqli_num_rows($res2) > 0){
        $notposs = 1;
        array_push($err, "<h3 style='color:red'>The dentist already has a confirmed appointment at the same time</h3><h3 style='color:red'> Please choose a different date/time</h3>");
    }
    if($notposs == 0)
    {
        // Update appointment
        $reqUpdate = "UPDATE appointment SET appt_date = '$date_val', appt_time = '$t1', status = 'Pending' WHERE appt_id = $appointment_ID";
        // echo $reqUpdate."<br>";
        if(mysqli_query($db, $reqUpdate))
        header("location: appointments1.php?patientID=$pic");
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Reschedule</title>
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
        #dname, #clinic{
            background-color: powderblue;
        }
    </style>

</head>
	<body>
        <center>
            <?php require_once("header.php");?>
            <div class="content-section" style="width:70%">
                <h3>Make Appointment</h3><br><br>
                <?php 
                    if(sizeof($err)>0)
                    {
                        foreach($err as $m)
                        {
                            echo $m;
                        }
                    }
                    $query = "SELECT a.*, b.*, c.dentist_name, c.clinic_location
                    FROM appointment a
                    INNER JOIN patient b ON a.patient_id = b.patientID
                    INNER JOIN dentist c ON a.dentist_id = c.dentist_id
                    WHERE a.appt_id = ".$_GET['appt'].";";
                    $result = mysqli_query($db, $query);
                    if($result == false || mysqli_num_rows($result) == 0)
                    echo "<h3>Invalid Dentist Name</h3>";
                    else
                    {
                        $row = mysqli_fetch_assoc($result);
                        echo "<form action = '' method='post'>";
                        $pname = "<input class='readonly' type='text' name='pname' id='pname' value='".$row['patientName']."' readonly/>";
                        
                        $pic = "<input class='readonly' type='text' name='id_value' id='id_value' value='".$row['patientIC']."' readonly/>";
                        
                        $pemail = "<input class='readonly' type='text' name='email_value' id='email_input' value='".$row['patientEmail']."' placeholder='Email Address' readonly/>";

                        $pphone = "<input class='readonly' type='text' name='phone_value' id='phone_input' value='".$row['patientPhone']."' placeholder='12-3456 789' maxlength='9' readonly/>";
                        
                        $paddress = "<input class='readonly' type='text' name='address_value' id='address_input' value='".$row['patientAddress']."' placeholder='Home Address' readonly/>";
                        
                        $dname = "<input type='text' name='dname' id='dname' value='Dr. ".$row['dentist_name']."' readonly/>";
                        
                        $clinic = "<input type='text' name='clinic' id='clinic' value='".$row['clinic_location']."' readonly/>";
                        
                        $reason = "<input class='readonly' type='text' name='reason' id='reason' value='".$row['appt_reason']."' readonly/>";

                        echo "
                        <div style='display:flex;justify-content:center;'>

                        <table>
                            ";
                                    // echo "<input type='submit' value='Submit'></td></tr>";
                                    // <input type='text' id='patientIC' name='patientIC' placeholder='IC/Passport' required>
                            echo "
                            <tr>
                                <th>Name</th>
                                <td>".$pname."</td>
                                <th>IC/Passport No.</th>
                                <td>".$pic."</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>".$pemail."</td>
                                <th>Phone (+60)</th>
                                <td>".$pphone."</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>".$paddress."</td>
                            </tr>";
                            $d1 = date('Y-m-d');
                            $dt = date('Y-m-d', strtotime($d1.' + 1 days'));
                            echo "
                            <tr>
                                <th>Dentist Name</th>
                                <td>".$dname."</td>
                                <th>Choose Date<span class='req'>*</span></th>
                                <td><input type='date' name='txtDate' id='tdate' min='".$dt."' value='".$row['appt_date']."' required></td>
                                </tr>
                                <tr>
                                <th>Clinic</th>
                                <td>".$clinic."</td>
                                <th>Choose Time<span class='req'>*</span></th>
                                <td><input list='times' name='time' value='".$row['appt_time']."' required>
                                <datalist id='times'>
                                <option value='09:30'>
                                <option value='10:00'>
                                <option value='10:30'>
                                <option value='11:00'>
                                <option value='11:30'>
                                    <option value='12:00'>
                                    <option value='12:30'>
                                    <option value='13:00'>
                                    <option value='13:30'>
                                    <option value='14:00'>
                                    <option value='14:30'>
                                    <option value='15:00'>
                                    <option value='15:30'>
                                    <option value='16:00'>
                                    <option value='16:30'>
                                    <option value='17:00'>
                                    <option value='17:30'>
                                    </datalist>
                                    </td>
                                    </tr>
                                    <tr></tr>
                                <th>Reason For Appointment</th>
                                <td>".$reason."</td>
                                <th>Medical Comments</th>
                                <td><input type='text' id='comments' name='comments' value='".$row['patientMedical']."' placeholder='Medical conditions.' ></td>
                            </tr>
                        </table>
                        <input class='readonly' style='display:none' type='text' name='apptID' id='apptID' value='".$row['appt_id']."' readonly/>
                        </div>
                        <input type='submit' name='update_appt' value='Update Appointment' class='example_e' style='width:50vw'>
                        </form>";
                    }
                ?>
            </div>
        </center>
    </body>
</html>