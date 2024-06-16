<?php
    session_start();
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    require( 'PHPMailer.php' );
    require("Exception.php");
    require("SMTP.php");
    $db=mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
    // if(!isset($_SESSION['username']))
    // {
    //     $_SESSION['redirect'] = 'appointments.php';
    //     header("location: login.php");
    // }
    if(isset($_GET['delete']) && $_SESSION['role'] == 'patient') #delete pending appointment
    {
        $id = $_GET['delete'];
        $cancel_query = "DELETE FROM appointment WHERE appt_id='".$id."'";
        mysqli_query($db, $cancel_query);
        unset($_GET['cancel']);
    }
    if(isset($_GET['reject']) && $_SESSION['role'] == 'dentist') #reject pending appointment
    {
        $id = $_GET['reject'];
        $cancel_query = "UPDATE appointment SET status = 'Rejected' WHERE appt_id='".$id."'";
        mysqli_query($db, $cancel_query);
        unset($_GET['reject']);
        $q3 = "SELECT * from appointment where appt_id='".$id."'";
        $res3 = mysqli_query($db, $q3);
        $row3 = mysqli_fetch_assoc($res3);
        $q4 = "SELECT name from dentist where username='".$row3['dname']."' limit 1";
        $res4 = mysqli_query($db, $q4);
        $row4 = mysqli_fetch_assoc($res4);
        $q5 = "SELECT email from user where username='".$row3['uname']."' limit 1";
        $res5 = mysqli_query($db, $q5);
        $row5 = mysqli_fetch_assoc($res5);
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "tls://smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = "username@email.com";
        $mail->Password = "password";
        //Sending the actual email
        $mail->setFrom('noreply@demo.com', 'Dental King');
        $mail->addAddress($row5['email']);     // Add a recipient
        $mail->isHTML(false);                                  // Set email format to HTML
        $mail->Subject = 'Appointment Rejection';
        $mail->Body = 'Your pending appointment with Dr. '.$row4['name']. ' at '.$row3['time'].'hrs on '.$row3['date'].' has been rejected';
        if(!$mail->send()) {
            echo 'Message could not be sent. ';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }
    }
    if(isset($_GET['cancel']))
    {
        $id = $_GET['cancel'];
        $cancel_query = "UPDATE appointment SET status = 'Cancelled' WHERE appt_id='".$id."'";
        mysqli_query($db, $cancel_query);
        unset($_GET['cancel']);
        // Get appointment details
        $q3 = "SELECT * from appointment where appt_id='".$id."'";
        $res3 = mysqli_query($db, $q3);
        $row3 = mysqli_fetch_assoc($res3);
        // Get user detail
        // $q4 = "SELECT * from useraccount where username='".$row3['uname']."'";
        // $res4 = mysqli_query($db, $q4);
        // $row4 = mysqli_fetch_assoc($res4);
        // Get dentist name
        $q5 = "SELECT dentist_name from dentist where dentist_id='".$row3['dentist_id']."' limit 1";
        $res5 = mysqli_query($db, $q5);
        $row5 = mysqli_fetch_assoc($res5);
        // Get dentist email
        $q6 = "SELECT dentist_email from dentist where dentist_id='".$row3['dentist_id']."' limit 1";
        $res6 = mysqli_query($db, $q6);
        $row6 = mysqli_fetch_assoc($res6);
        // Get patient email
        $q7 = "SELECT patientEmail from patient where patientID='".$row3['patient_id']."' limit 1";
        $res7 = mysqli_query($db, $q7);
        $row7 = mysqli_fetch_assoc($res7);
        
        // Email Notification
        // $mail = new PHPMailer;
        // $mail->IsSMTP();
        // $mail->SMTPAuth = true;
        // $mail->Host = "tls://smtp.gmail.com";
        // $mail->Port = 587;
        // $mail->Username = "username@email.com";
        // $mail->Password = "password";
        // $mail->setFrom('noreply@demo.com', 'Dental King');
        // // Send to dentist
        // $mail->addAddress($row6['email']);     
        // $mail->isHTML(false);                  
        // $mail->Subject = 'Appointment Cancellation';
        // $mail->Body = 'Your appointment with '.$row4['name']. ' at '.$row3['time'].'hrs on '.$row3['date'].' has been cancelled';
        // if(!$mail->send()) {
        //     echo 'Message could not be sent. ';
        //     echo 'Mailer Error: ' . $mail->ErrorInfo;
        //     exit;
        // }

        // $mail = new PHPMailer;
        // $mail->IsSMTP();
        // $mail->SMTPAuth = true;
        // $mail->Host = "tls://smtp.gmail.com";
        // $mail->Port = 587;
        // $mail->Username = "username@email.com";
        // $mail->Password = "password";
        // $mail->setFrom('noreply@demo.com', 'Dental King');
        // // Send to patient    
        // $mail->addAddress($row7['email']); 
        // $mail->isHTML(false);                  
        // $mail->Subject = 'Appointment Cancellation';
        // $mail->Body = 'Your appointment with Dr. '.$row5['name']. ' at '.$row3['time'].'hrs on '.$row3['date'].' has been cancelled';
        // if(!$mail->send()) {
        //     echo 'Message could not be sent. ';
        //     echo 'Mailer Error: ' . $mail->ErrorInfo;
        //     exit;
        // }
        if(isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            // If HTTP_REFERER is not set, redirect to a default page or homepage
            header('Location: index.php'); // Replace index.php with your default page
        }
                
    }
    if(isset($_GET['confirm']))
    {
        if($_SESSION['role'] == 'dentist')
        {
            $id = $_GET['confirm'];
            $cancel_query = "UPDATE appointment SET status = 'Confirmed' WHERE appt_id='".$id."'";
            mysqli_query($db, $cancel_query);
            unset($_GET['confirm']);
            $q2 = "SELECT name from dentist where username='".$_SESSION['username']."' limit 1";
            $res2 = mysqli_query($db, $q2);
            $row2 = mysqli_fetch_assoc($res2);
            $q3 = "SELECT * from appointment where appt_id='".$id."'";
            $res3 = mysqli_query($db, $q3);
            $row3 = mysqli_fetch_assoc($res3);
            $q4 = "SELECT * from useraccount where username='".$row3['uname']."'";
            $res4 = mysqli_query($db, $q4);
            $row4 = mysqli_fetch_assoc($res4);
            $q6 = "SELECT email from user where username='".$row3['dname']."' limit 1";
            $res6 = mysqli_query($db, $q6);
            $row6 = mysqli_fetch_assoc($res6);
            $q7 = "SELECT email from user where username='".$row3['uname']."' limit 1";
            $res7 = mysqli_query($db, $q7);
            $row7 = mysqli_fetch_assoc($res7);
            $mail = new PHPMailer;
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = "tls://smtp.gmail.com";
            $mail->Port = 587;
            $mail->Username = "username@email.com";
            $mail->Password = "password";
            $mail->setFrom('noreply@demo.com', 'Dental King');
            $mail->addAddress($row7['email']); 
            $mail->isHTML(false);
            $mail->Subject = 'Appointment Confirmation';
            $mail->Body = 'Your pending appointment with Dr. '.$row2['name']." at ".$row3['time']."hrs on ".$row3['date']." has been confirmed";

            if(!$mail->send()) {
                echo 'Message could not be sent. ';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
                exit;
            }
            $mail = new PHPMailer;
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = "tls://smtp.gmail.com";
            $mail->Port = 587;
            $mail->Username = "username@email.com";
            $mail->Password = "password";
            $mail->setFrom('noreply@demo.com', 'Dental King');
            $mail->addAddress($row6['email']);
            $mail->isHTML(false);
            $mail->Subject = 'Appointment Confirmation';
            $mail->Body = 'Your pending appointment with '.$row4['name']." at ".$row3['time']."hrs on ".$row3['date']." has been confirmed";

            if(!$mail->send()) {
                echo 'Message could not be sent. ';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
                exit;
            }
        }
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Appointments</title>
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
        let currentAppointmentId = '';
        // Function to show the popup and overlay
        function showPopup(appointmentId) {
            currentAppointmentId = appointmentId;
            document.getElementById('cancel-popup').style.display = 'block';
            document.getElementById('cancel-overlay').style.display = 'block';
            return false; // Prevent default link behavior
        }

        // Function to close the popup and overlay
        function closePopup() {
            document.getElementById('cancel-popup').style.display = 'none';
            document.getElementById('cancel-overlay').style.display = 'none';
            return false; // Prevent default link behavior
        }

        // Function to handle cancelling the application (replace with your logic)
        function cancelApplication() {
            if (currentAppointmentId) {
                // Add your logic here for cancelling the application
                // alert('Application Cancelled! ID: ' + currentAppointmentId);
                window.location.href = 'appointments1.php?cancel=' + currentAppointmentId;
            }
            else {
                alert('No Appointment ID found.')
            }
            closePopup(); // Close the popup after action
            return false; // Prevent default link behavior
        }
    </script>

</head>
<body>
    <center>
    <!-- Popup dialog -->
    <div id="cancel-popup" class="cancel-popup">
        <p>Are you sure you want to cancel your application? Any unsaved changes will be lost.</p>
        <a href="#" onclick="closePopup()" style="color:dodgerblue">Keep Application</a>
        <a href="#" onclick="cancelApplication()" style="color:red">Cancel Application</a>
    </div>
    <!-- Overlay -->
    <div id="cancel-overlay" class="cancel-overlay"></div>
  <?php require_once("header.php");
  $reqPName = "SELECT patientName FROM patient WHERE patientIC = '".$_GET['patientID']."' LIMIT 1";  
//   echo $reqPName."<br>";
  $resPName = mysqli_query($db, $reqPName);
  $row0 = mysqli_fetch_assoc($resPName);
//   echo $row0['patientName']."<br>";
  ?>

    <div class="content-section not-block" style="width:85%; max-width:fit-content; overflow-x:scroll;">
    <h3>Appointments for <?php echo $row0['patientName'];?></h3><br><br>
    <?php
            // $query = "SELECT * FROM appointment WHERE patient_id='".$_GET['patientID']."'";
            $query = "SELECT a.*, b.patientID, c.clinic_location FROM appointment a JOIN patient b JOIN clinic c ON a.clinic_id = c.clinic_id AND a.patient_id = b.patientID WHERE b.patientIC = '".$_GET['patientID']."'";
            // echo $query."<br>";
            $result = mysqli_query($db, $query);
            if($result == false || mysqli_num_rows($result) == 0)
            echo "<h4>No appointments to show</h4>";
            else
            {
                echo "<h4>Your Appointments</h4>";
                echo "<table>
                <tr>
                    <th>Dentist</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Invoice</th>
                    </tr>";
                while($row = mysqli_fetch_assoc($result))
                {
                    if(date('Y-m-d') < $row['appt_date'])
                    {
                        $query2 = "SELECT dentist_name FROM dentist WHERE dentist_id = '".$row['dentist_id']."' LIMIT 1";
                        // echo $query2."<br>";
                        $dentist_name = mysqli_query($db, $query2);
                        $row2 = mysqli_fetch_assoc($dentist_name);
                        if($row['status'] == 'Pending')
                        $url = "appointments.php?delete=".$row['appt_id'];
                        else
                        $url = "appointments.php?cancel=".$row['appt_id'];
                        // echo $row['appt_id']."<br>";
                        echo "
                        <tr class='flex'>
                            <td>Dr. ".$row2['dentist_name']."</td>
                            <td>".$row['clinic_location']."</td>
                            <td>".$row['appt_date']."</td>
                            <td>".$row['appt_time']."HRS</td>
                            <td>".$row['appt_reason']."</td>
                            <td>".$row['status']."</td>
                            <td><button style='padding:8px;'><a href='appointmentinvoice.php?appt=".$row['appt_id']."'><i class='fa fa-file-text'></i></a></button></td>
                            ";
                        if($row['status'] == 'Pending' || $row['status'] == 'Confirmed')
                        // echo "<td><a href='".$url."' style='color:red' onClick='cancelApplication()'>Cancel Appointment</a></td></tr>";
                        echo "
                        <td><a href='reschedule.php?appt=".$row['appt_id']."' disabled>Reschedule</a></td>
                        <td><a href='#' style='color:red' onClick='showPopup(".$row['appt_id'].")'>Cancel</a></td></tr>";
                        else
                        echo "</tr>";
                    }

                }
            }
        // if($_SESSION['role'] == 'patient') {
        // }
        // elseif ($_SESSION['role'] == 'dentist') {
        //     $query = "SELECT * FROM appointment WHERE dname='".$_SESSION['username']."'";
        //     $result = mysqli_query($db, $query);
        //     if($result == false || mysqli_num_rows($result) == 0)
        //     echo "<h4>No appointments to show</h4>";
        //     else
        //     {
        //         echo "<h4>Your Appointments</h4>";
        //         echo "<table><tr><th>Patient Name</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th></th></tr>";
        //         while($row = mysqli_fetch_assoc($result))
        //         {
        //             if(date('Y-m-d') < $row['date'])
        //             {
        //                 $qu = "SELECT * from useraccount where username='".$row['uname']."'";
        //                 $res = mysqli_query($db, $qu);
        //                 $row_1 = mysqli_fetch_assoc($res);
        //                 echo "<tr><td>".$row_1['name']."</td><td>".$row['date']."</td>";
        //                 echo "<td>".$row['time']."HRS</td><td>".$row['reason']."</td><td>".$row['status']."</td>";
        //                 if($row['status'] == 'Pending')
        //                 {
        //                     $url1 = 'appointments.php?confirm='.$row['appt_id'];
        //                     $url2 = 'appointments.php?reject='.$row['appt_id'];
        //                     echo "<td><a href='".$url1."' style='color:green'>Confirm Appointment</a>";
        //                     echo "<br><br><form action='' method='post'><a href='".$url2."' style='color:red'>Reject Appointment</a></td></tr>";
        //                 }
        //                 elseif($row['status'] == 'Confirmed')
        //                 {
        //                     $url='appointments.php?cancel='.$row['appt_id'];
        //                     echo "<td><form action='' method='post'><a href='".$url."' style='color:red'>Cancel Appointment</a></td></tr>";
        //                 }
        //             }

        //         }
        //     }
        // }
    ?>
    </div>
</body>
</html>