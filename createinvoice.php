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

$appt_id = 0;
$patient_id = 0;

if(isset($_POST['appt_id'])) {
    // echo "Test succesful: Appointment ".$_POST['appt_id']."<br>";
    if(isset($_POST['invoice_total'])) {
        // echo "Test succesful: Appointment ".$_POST['invoice_total']."<br>";
        $appt_id = $_POST['appt_id'];
        $patient_id = $_POST['patient_id'];
        $invoice_total = $_POST['invoice_total'];
    
        // Generate the next invoice number
        $prefix = 'INVREF';
        $query = "INSERT INTO invoice (invoice_number, appt_id, patient_id, invoice_total, invoice_date_created, invoice_status)
                  VALUES ('$prefix', $appt_id, $patient_id, $invoice_total, NOW(), 'Pending')";
        echo $query."<br>";
        
        // Execute the query to insert the new invoice
        if (mysqli_query($db, $query)) {
            $last_insert_id = mysqli_insert_id($db);
            $invoice_number = $prefix . str_pad($last_insert_id, 14, '0', STR_PAD_LEFT);

            // Update the invoice_number with the generated value
            $update_query = "UPDATE invoice SET invoice_number = '$invoice_number' WHERE invoice_id = $last_insert_id";
            mysqli_query($db, $update_query);

            // Update appointment with the new invoice_id
            $update_appt_query = "UPDATE appointment SET invoice_id = $last_insert_id WHERE appt_id = $appt_id";
            mysqli_query($db, $update_appt_query);

            // Proceed with further operations
            header("Location: invoice.php?appt_id=".$_POST['appt_id']."");
            exit;
        } 
        else {
            echo "Error: " . $query . "<br>" . mysqli_error($db);
        }
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Create Invoice</title>
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
        <div class='flex-center'>
            <div class='center-container vw50 invoice-form invoice'>
                <h2>Create Invoice</h2>
                <form action="createinvoice.php" method="post">
                    <div>
                        <label for="patient_id">Patient ID:</label>
                        <input class='readonly' readonly type="text" id="patient_id" name="patient_id" value="<?php echo isset($_POST['patient_id']) ? htmlspecialchars($_POST['patient_id']) : ''; ?>" readonly>
                    </div>
                    <div>
                        <label for="appt_id">Appointment ID:</label>
                        <input class='readonly' readonly type="text" id="appt_id" name="appt_id" value="<?php echo isset($_POST['appt_id']) ? htmlspecialchars($_POST['appt_id']) : ''; ?>" readonly>
                    </div>
                    <div>
                        <label for="invoice_amount">Invoice Amount (RM):</label>
                        <input type="number" id="invoice_total" name="invoice_total" step="0.01" placeholder='1000.00' value='1000.00' required>
                    </div>
                    <button type="submit">Create Invoice</button>
                </form>
            </div>
        </div>
        <!-- </center> -->
    </body>
</html>