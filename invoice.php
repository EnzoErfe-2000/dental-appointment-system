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

$invoiceNum = ''; 
$invoice_id = ''; 
$appt_id = ''; 
$patient_id = ''; 
$total = ''; 
$remaining = ''; 
$date_created = '';
$date_paid = '';
$status = '';

if(isset($_GET['appt_id'])) {
    $appt_id = mysqli_real_escape_string($db, $_GET['appt_id']);
    $query = "SELECT * FROM invoice WHERE appt_id = '".$appt_id."' LIMIT 1";
    $result = mysqli_query($db, $query); 
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            // echo "INVOICE NUMBER: " . $row['invoice_number'];
            $invoiceNum = $row['invoice_number'];
            $invoice_id = $row['invoice_id'];
            $appt_id = $row['appt_id'];
            $patient_id = $row['patient_id'];
            $total = $row['invoice_total'];
            $remaining = $row['invoice_amount_paid'];
            $date_created = $row['invoice_date_created'];
            $date_paid = $row['invoice_date_paid'];
            $status = $row['invoice_status'];
        }
        else {
            echo "No invoice found for appointment ID: $appt_id";
        }
    }
    else {
        echo "Error: " . $query . "<br>" . mysqli_error($db);
    }
}
else {
    echo "appt_id parameter is not set in the URL.";
    header("Location: appointments1.php");
}

$isDentist = false;
if (isset($_SESSION['role'])){
    if ($_SESSION['role'] == 'dentist') {
        $isDentist = true;
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Invoice</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles-1.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:500&display=swap" rel="stylesheet">
</head>

	<body>
        <!-- <center> -->
        <?php require_once("header.php");?>
        <div class='flex-center' style='height:auto'>
            <div class='center-container vw50 invoice'>
                <h2>Invoice</h2>
                <br>
                <table>
                    <tr>
                        <th>Invoice Number</th>
                        <td>#<?php echo $invoiceNum;?></td>
                    </tr>
                    <tr>
                        <th>Appointment ID</th>
                        <td><?php echo $appt_id?></td>
                    </tr>
                    <tr>
                        <th>Patient ID</th>
                        <td><?php echo $patient_id?></td>
                    </tr>
                    <tr>
                        <th>Total Amount</th>
                        <td>RM <?php echo $total?></td>
                    </tr>
                    <tr>
                        <th>Amount Paid</th>
                        <td>RM <?php echo $remaining?></td>
                    </tr>
                    <tr>
                        <th>Date Created</th>
                        <td><?php echo $date_created?></td>
                    </tr>
                    <tr>
                        <th>Date Paid</th>
                        <td><?php echo $date_created?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?php echo $status?></td>
                    </tr>
                </table>
                <br>
                <?php
                if ($isDentist == true) {
                    echo "<form action='createpayment.php' method='POST'>
                    <input type='hidden' name='invoice_id' value='".$invoice_id."'>    
                    <input type='hidden' name='appt_id' value='".$appt_id."'>    
                    <button type='submit'>Create Payment</button>
                    </form>";
                } 
                ?>
            </div>
        </div>
        <br><br>
        <div class='flex-center' style='height:auto'>
            <div class='center-container payments vw50'>
                <h2>Payments</h2>
                <?php
                $query = "SELECT * FROM payment WHERE invoice_id = ".$invoice_id;
                // echo $query;
                $res = mysqli_query($db, $query);
                if ($res) {
                    if (mysqli_num_rows($res) > 0) {
                        echo "
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                        ";
                        while($row = mysqli_fetch_assoc($res)) {
                            echo "
                            <tr>
                                <td>".$row['payment_id']."</td>
                                <td>".$row['payment_amount']."</td>
                                <td>".$row['payment_date']."</td>
                            </tr>";
                        }
                        echo "
                            </tbody>
                        </table>"; 
                    }
                }
                ?>
                <br>
            </div>
        </div>
        <br><br>
        <!-- </center> -->
    </body>
</html>