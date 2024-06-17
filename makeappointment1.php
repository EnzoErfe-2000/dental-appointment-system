<?php
    session_start();
    $db=mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
    $err = [];
    // if(!isset($_SESSION['username']))
    // header("location: login.php");
    // if(!isset($_GET['dentist']))
    // header("location: dentist.php");
    // if($_SESSION['role'] == 'dentist')
    // header("location: index.php");
    $dentist_name = $_GET['dentist'];
    
    if(isset($_POST['make_appt']))
    {
        $notposs = 0;
        // TODO
        // $uname = mysqli_real_escape_string($db, $_SESSION['username']);
        $pname = mysqli_real_escape_string($db, $_POST['pname']);
        $pic = mysqli_real_escape_string($db, $_POST['id_value']);
        $pemail = mysqli_real_escape_string($db, $_POST['email_value']);
        $pphone = mysqli_real_escape_string($db, $_POST['phone_value']);
        $paddress = mysqli_real_escape_string($db, $_POST['address_value']);
        $pmedical = mysqli_real_escape_string($db, $_POST['comments']);
        $dname = substr(mysqli_real_escape_string($db, $_POST['dname']), 4);
        $locn = mysqli_real_escape_string($db, $_POST['clinic']);
        $date_val = mysqli_real_escape_string($db, $_POST['txtDate']);
        if (strlen($_POST['time']) == 5)
        $t1 = substr($_POST['time'], 0, 2).substr($_POST['time'], 3, 2);
        else 
        $t1 = substr($_POST['time'], 0, 2).substr($_POST['time'], 2, 2);
        $hr = mysqli_real_escape_string($db, $t1);
        $reason = mysqli_real_escape_string($db, $_POST['reason']);

        // $checkq = "SELECT * FROM appointment WHERE uname = '$uname' AND time='$hr' AND date='$date_val' AND (status='Pending' OR status='Confirmed')";
        // $checkq = "SELECT * FROM appointment WHERE patient_name = '$pname' AND time='$hr' AND date='$date_val' AND (status='Pending' OR status='Confirmed')";
        // $checkq2 = "SELECT * FROM appointment WHERE dname = '$dname' AND time='$hr' AND date='$date_val' AND status='Confirmed'";
        $checkq = "SELECT a.* FROM appointment a INNER JOIN patient b ON a.patient_id = b.patientID WHERE patientIC = '$pic' AND appt_time='$t1' AND appt_date='$date_val' AND (status='Pending' OR status='Confirmed')";
        $checkq2 = "SELECT a.* FROM appointment a INNER JOIN dentist b ON a.dentist_id = b.dentist_id WHERE dentist_name = '$dname' AND appt_time='$t1' AND appt_date='$date_val' AND status='Confirmed'";
        // echo $checkq2;
        // TODO
        // If patient's first appointment, create new patient 
        $checkExUser = "SELECT * FROM patient WHERE patientIC = '$pic'";
        // echo $checkExUser."<br>";
        
        $res = mysqli_query($db, $checkq);
        $res2 = mysqli_query($db, $checkq2);
        $resExUser = mysqli_query($db, $checkExUser);
        
        
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
            // TODO Check if new user
            if ($resExUser != false) {
                if (mysqli_num_rows($resExUser) <= 0){ 
                    echo "New User";
                    echo "<br>";
                    // TODO Create new patient
                    $reqNewUser = "INSERT INTO patient(patientName, patientIC, patientEmail, patientPhone, patientAddress, patientMedical) VALUES ('$pname', '$pic', '$pemail', '$pphone', '$paddress', '$pmedical')";
                    echo $reqNewUser."<br>";
                    $resNewUser = mysqli_query($db, $reqNewUser);
                    
                    if ($resNewUser != false){
                        echo "New User created<br>";
                    }
                    else if (mysqli_errno($db)) {
                        echo "User already Exists (Same Name)";
                    }
                    else { echo "Error with inserting new user";}
                }
                else
                { 
                    echo "Existing User<br>";
                }
            }
            else { echo "Error with checking existing user";}
            
            // TODO Get the necessary IDs
            // $pid = "SELECT patientID FROM patient WHERE patientIC = '$pic'";
            // $did = "SELECT dentist_id FROM dentist WHERE dentist_name = '$dname'";
            // $cid = "SELECT clinic_id FROM clinic WHERE clinic_location = '$locn'";
            // echo $pid."<br>".$did."<br>".$cid."<br>";
            
            $reqID = "SELECT (SELECT patientID FROM patient WHERE patientIC = '$pic') AS patientID, (SELECT dentist_id FROM dentist WHERE dentist_name = '$dname') AS dentist_id, (SELECT clinic_id FROM clinic WHERE clinic_location = '$locn') AS clinic_id";
            // echo $reqID."<br>";
            
            $resID = mysqli_query($db, $reqID);
            if ($resID != false && mysqli_num_rows($resID) > 0){
                $rowID = mysqli_fetch_assoc($resID);
                $pid = $rowID['patientID']; 
                $did = $rowID['clinic_id'];
                $cid = $rowID['dentist_id'];
                echo $pid."<br>";
                echo $did."<br>";
                echo $cid."<br>";
            }
            
            // TODO Create appointment 
            $reqAppt = "INSERT INTO appointment(dentist_id, patient_id, clinic_id, appt_date, appt_time, appt_reason) VALUES ('$did', '$pid', '$cid', '$date_val', '$hr', '$reason')"; 
            // echo $reqAppt."<br>";
            
            if(mysqli_query($db, $reqAppt))
            // header("location: appointments.php?patient=$pic");
            header("location: appointments1.php?patientID=$pic");
            // Make appointments.php not redirect to index.php
        }
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Make Appointment</title>
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

    <script>
        function toggleInput() {
        var icInput = document.getElementById("ic_input");
        var passportInput = document.getElementById("passport_input");
        var icRadio = document.getElementById("ic_radio");
        var passportRadio = document.getElementById("passport_radio");
        
        if (icRadio.checked) {
            icInput.disabled = false;
            passportInput.disabled = true;
            // Clear the value of the passport input
            passportInput.value = ""; 
            passportInput.style.display = 'none'; 
            icInput.style.display = 'block'; 
        } else if (passportRadio.checked) {
            passportInput.disabled = false;
            icInput.disabled = true;
            // Clear the value of the IC input
            icInput.value = ""; 
            passportInput.style.display = 'block'; 
            icInput.style.display = 'none'; 
        }    
    }    
    </script>

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
        $query = "SELECT * from dentist WHERE dentist_name='$dentist_name'";
        $result = mysqli_query($db, $query);
        if($result == false || mysqli_num_rows($result) == 0)
        echo "<h3>Invalid Dentist Name</h3>";
        else
        {
            $row = mysqli_fetch_assoc($result);
            echo "<form action = '' method='post'>";
            $name = "<input type='text' name='dname' id='dname' value='Dr. ".$row['dentist_name']."' readonly/>";
            $clinic = "<input type='text' name='clinic' id='clinic' value='".$row['clinic_location']."' readonly/>";
            echo "
            <div style='display:flex;justify-content:center;'>

            <table>
                ";
                        // echo "<input type='submit' value='Submit'></td></tr>";
                        // <input type='text' id='patientIC' name='patientIC' placeholder='IC/Passport' required>
                echo "
                <tr>
                    <th>Name<span class='req'>*</span></th>
                    <td>
                        <input type='text' id='pname' name='pname' placeholder='Patient Name' required>
                    </td>
                    <th>
                        <div class='flex-h'>
                            <input type='radio' id='ic_radio' name='id_type' value='ic' onchange='toggleInput()' checked>
                            <label for='ic_radio'>IC<span class='req'>*</span></label>
                            </div>
                            <br>
                            <div class='flex-h'>
                            <input type='radio' id='passport_radio' name='id_type' value='passport' onchange='toggleInput()'>
                            <label for='passport_radio'>Passport<span class='req'>*</span></label>
                        </div>
                    </th>
                    <td style='display:flex;'>
                        <input type='text' id='ic_input' name='id_value' placeholder='IC No.' required>
                        <br>
                        <input type='text' id='passport_input' name='id_value' placeholder='Passport No.' required disabled style='display:none;'>
                        <br>
                    </td>
                </tr>
                <tr>
                    <th>Email<span class='req'>*</span></th>
                    <td><input type='text' id='email_input' name='email_value' placeholder='Email Address' required></td>
                    <th>Phone (+60)</th>
                    <td><input type='text' id='phone_input' name='phone_value' placeholder='12-3456 789' maxlength='9'></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><input type='text' id='address_input' name='address_value' placeholder='Home Address'</td>
                </tr>";
                $d1 = date('Y-m-d');
                $dt = date('Y-m-d', strtotime($d1.' + 1 days'));
                echo "
                <tr>
                    <th>Dentist Name<span class='req'>*</span></th>
                    <td>".$name."</td>
                    <th>Choose Date<span class='req'>*</span></th>
                    <td><input type='date' name='txtDate' id='tdate' min='".$dt."'required></td>
                    </tr>
                    <tr>
                    <th>Clinic<span class='req'>*</span></th>
                    <td>".$clinic."</td>
                    <th>Choose Time<span class='req'>*</span></th>
                    <td><input list='times' name='time' required>
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
                    <th>Reason For Appointment<span class='req'>*</span></th>
                    <td><input type='text' id='reason' name='reason' value='Check-Up' required></td>
                    <th>Medical Comments</th>
                    <td><input type='text' id='comments' name='comments' placeholder='Medical conditions.' ></td>
                </tr>
            </table>
            </div>
            <input type='submit' name='make_appt' value='Make Appointment' class='example_e' style='width:50vw'>
            </form>";
        }
        ?>
    </div>
    </center>
</body>
</html>