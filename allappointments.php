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

    $appts_exist = false;

    $query = "SELECT a.appt_id, a.patient_id, p.patientName, d.dentist_name, d.clinic_location, a.appt_date, a.appt_time, a.appt_reason, a.status, a.invoice_id FROM appointment a JOIN patient p ON a.patient_id = p.patientID JOIN dentist d ON a.dentist_id = d.dentist_id";
    $result = mysqli_query($db, $query);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
           $appts_exist = true; 
        }
    }

    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        if(isset($_GET['delete']) && $_SESSION['role'] == 'patient') {
            $id = $_GET['delete'];
            $delete_query = "DELETE FROM appointment WHERE appt_id='".$id."'";
            mysqli_query($db, $delete_query);
            unset($_GET['delete']);
        }
            
        if(isset($_GET['reject'])) {
            $id = $_GET['reject'];
            $reject_query = "UPDATE appointment SET status = 'Rejected' WHERE appt_id='".$id."'";
            mysqli_query($db, $reject_query);
            unset($_GET['reject']);

            // Email Notification

        }
        
        if(isset($_GET['cancel'])) {
            $id = $_GET['cancel'];
            $cancel_query = "UPDATE appointment SET status = 'Cancelled' WHERE appt_id='".$id."'";
            mysqli_query($db, $cancel_query);
            unset($_GET['cancel']);

            // Email Notification

            // Redirect
            if(isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                // If HTTP_REFERER is not set, redirect to a default page or homepage
                header('Location: index.php'); // Replace index.php with your default page
            }
        }

        if(isset($_GET['confirm']))
        {
            $id = $_GET['confirm'];
            $update_query = "UPDATE appointment SET status = 'Confirmed' WHERE appt_id='".$id."'";
            mysqli_query($db, $update_query);
            unset($_GET['confirm']);

            // Email Notification
        }

    }
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
        <script>
        let currentAppointmentId = '';
        let currentPopupType = '';
        // Function to show the popup and overlay
        function showPopup(appointmentId, type) {
            currentAppointmentId = appointmentId;
            currentPopupType = type;

            hideAllPopups();
            // console.log(currentAppointmentId);
            if (currentPopupType == 0) {
                document.getElementById('cancel-popup').style.display = 'block';
                // document.getElementById('popup-overlay').style.display = 'block';
            }
            else if (currentPopupType == 1) {
                document.getElementById('confirm-popup').style.display = 'block';
            }
            else if (currentPopupType == 2) {
                document.getElementById('reject-popup').style.display = 'block';
            }
            document.getElementById('popup-overlay').style.display = 'block';    
            return false; // Prevent default link behavior
        }

        function hideAllPopups() {
            // Hide all popups
            var popups = document.querySelectorAll('.popup'); // Adjust class name if necessary

            popups.forEach(function(popup) {
                popup.style.display = 'none';
            });

            // Hide overlay
            document.getElementById('popup-overlay').style.display = 'none';
        }

        // Function to close the popup and overlay
        function closePopup(type) {
            hideAllPopups();
            return false; // Prevent default link behavior
        }

        // Function to handle cancelling the application (replace with your logic)
        function cancelApplication() {
            if (currentAppointmentId) {
                window.location.href = 'allappointments.php?cancel=' + currentAppointmentId;
            }
            else {
                alert('No Appointment ID found.')
            }
            hideAllPopups(); // Close the popup after action
            return false; 
        }
        
        function confirmApplication() {
            if (currentAppointmentId) {
                window.location.href = 'allappointments.php?confirm=' + currentAppointmentId;
            }
            else {
                alert('No Appointment ID found.')
            }
            hideAllPopups();
            return false;
        }
        
        function rejectApplication() {
            if (currentAppointmentId) {
                window.location.href = 'allappointments.php?reject=' + currentAppointmentId;
            }
            else {
                alert('No Appointment ID found.')
            }
            hideAllPopups(); 
            return false;
        }

    </script>
</head>
<body>
  <center>
    <!-- Popup dialog -->
    <div id="cancel-popup" class="popup">
        <p>Are you sure you want to cancel your application? Any unsaved changes will be lost.</p>
        <a href="#" onclick="closePopup()" style="color:red">Keep Application</a>
        <a href="#" onclick="cancelApplication()" style="color:dodgerblue">Cancel Application</a>
    </div>
    
    <div id="confirm-popup" class="popup">
        <p>Approve this application?</p>
        <a href="#" onclick="closePopup()" style="color:red">Cancel</a>
        <a href="#" onclick="confirmApplication()" style="color:dodgerblue">Approve Application</a>
    </div>

    <div id="reject-popup" class="popup">
        <p>Reject this application?</p>
        <a href="#" onclick="closePopup()" style="color:red">Cancel</a>
        <a href="#" onclick="rejectApplication()" style="color:dodgerblue">Reject Application</a>
    </div>

    <!-- Overlay -->
    <div id="popup-overlay" class="popup-overlay"></div>
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
    <a class='cta' style='margin-left:0px;' href='clinics1.php'>Create Appointment</a>
    <br>
    <br>
    <div style='overflow-x:scroll;'>
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
            <?php
            if ($appts_exist) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr class='flex' id='".$row['appt_id']."'>
                    <td>".$row['patientName']."</td>
                    <td>".$row['dentist_name']."</td>
                    <td>".$row['clinic_location']."</td>
                    <td>".$row['appt_date']."<br>".$row['appt_time']."</td>
                    <td>".$row['appt_reason']."</td>
                    <td>".$row['status'];
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
                        if ($row['status'] == 'Pending') {
                            echo "<br>
                            <div style='padding-top:10px;'>
                            <button style='padding:8px;' onClick='showPopup(".$row['appt_id'].", 1)'>
                            <i class='fa fa-check' style='color:green'></i>
                            </button>
                            <button style='padding:8px;' onClick='showPopup(".$row['appt_id'].", 2)'>
                            <i class='fa fa-times' style='color:red'></i>
                            </button>
                            </div>";
                        }
                    }
                    echo "</td>
                    <td>";
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
                        if($row['status'] != 'Cancelled') {
                            if (!$row['invoice_id']) {
                                echo "
                                <form action='createinvoice.php' method='POST' style='margin-bottom:8px'>
                                    <input type='hidden' name='appt_id' value='".$row['appt_id']."'>
                                    <input type='hidden' name='patient_id' value='".$row['patient_id']."'>
                                    
                                    <button type='submit' style='padding:8px;'><a href='#'><i class='fa fa-file-text' style='padding-right:8px'></i></a>Create Invoice</button>
                                </form>
                                ";
                            }
                            else {
                                echo "
                                <button style='padding:8px; margin-bottom:8px;'><a href='invoice.php?appt_id=".$row['appt_id']."'><i class='fa fa-file-text'></i> View Invoice</a></button>
                                ";
                            }
                        }
                        if($row['status'] == 'Pending' || $row['status'] == 'Confirmed') {
                                echo "
                                <div style='display:flex; gap:8px'>
                                <button style='padding:8px;'>
                                <a href='reschedule.php?appt=".$row['appt_id']."' disabled>Reschedule</a>
                                </button>
                                <button style='padding:8px;'>
                                <a href='#' style='color:red' onClick='showPopup(".$row['appt_id'].", 0)'>Cancel</a></td></tr>
                                </button>
                                </div>";
                            }
                    }
                    echo "</td>
                    </tr>";
                }
            } 
            ?>
        </table>
    </div>
    </center>
</body>
</html>                    