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

$invoice_id = 0;
$payment_balance = 0;
$amount_remaining = 0;
$invoice_status = '';
$appt_id = 0;

if(isset($_POST['invoice_id'])) {
    $appt_id = intval($_POST['appt_id']);
    // echo $appt_id;
    // echo "Test succesful: Appointment ".$_POST['invoice_id']."<br>";
    $invoice_id = intval($_POST['invoice_id']);
    $query = "SELECT (invoice_total - invoice_amount_paid) AS invoice_balance FROM invoice WHERE invoice_id = ".$invoice_id.""; 
    // echo $query;
    
    $result = mysqli_query($db, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $payment_balance = $row['invoice_balance'];
            // echo "Balance is ".$payment_balance."<br>";
        }
        else {
            echo "Invoice not found or no balance information available.";
        }
    }
    else {
        echo "Error: " . $query . "<br>" . mysqli_error($db);
    }
}
else {
    header("Location: appointments1.php");
}

if (isset($_POST['payment_amount'])) {
    // echo "Info is <br>".$_POST['invoice_id']."<br>".$_POST['payment_amount']."<br>NOW()<br>".($_POST['payment_balance'] - $_POST['payment_amount'])."<br>";

    $invoice_id = intval($_POST['invoice_id']);
    $invoice_status = $_POST['invoice_status'];
    $amount_remaining = ($_POST['payment_balance'] - $_POST['payment_amount']);
    $full_payment_status = ($invoice_status == 'Completed' ? 'NOW()' : 'NULL' );
    $query = "INSERT INTO payment (invoice_id, payment_amount, payment_date, payment_balance) VALUES (".$_POST['invoice_id'].", ".$_POST['payment_amount'].", NOW(), ".$amount_remaining.")"; 
    // echo $query."<br>";
    
    $query2 = "UPDATE invoice SET invoice_amount_paid = (invoice_total - $amount_remaining), invoice_status = '$invoice_status', invoice_date_paid = $full_payment_status WHERE invoice_id = $invoice_id";
    // echo $query2."<br>";

    $result = mysqli_query($db, $query);
    $result2 = mysqli_query($db, $query2);
    if($result && $result2){
        $redirect_url = "invoice.php?appt_id=$appt_id";
        $_SESSION['redirect'] = $redirect_url;
        // echo $_SESSION['redirect'];
        header("location: " . $_SESSION['redirect']);
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Create Payment</title>
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
                <h2>Create Payment</h2>
                <form method="post">
                    <div>
                        <label for="invoice_id">Invoice ID:</label>
                        <input class='readonly' readonly type="text" id="invoice_id" name="invoice_id" value="<?php echo isset($_POST['invoice_id']) ? htmlspecialchars($_POST['invoice_id']) : ''; ?>" readonly>
                    </div>
                    <div>
                        <label for="payment_balance">Payment Balance:</label>
                        <input class='readonly' readonly type="text" id="payment_balance" name="payment_balance" value="<?php echo isset($payment_balance) ? htmlspecialchars($payment_balance) : ''; ?>" readonly>
                    </div>
                    <div>
                        <label for="payment_amount">Payment Amount (RM):</label>
                        <input type="number" id="payment_amount" name="payment_amount" step="0.01" placeholder='0.00' value='50.00' required>
                    </div>
                    <div>
                        <label>Status:</label>
                        <table style='margin-top:0px'>
                            <tbody>
                                <tr style='display:flex; flex-direction:row;'>
                                    <td style='display:flex; flex-direction:row; align-items:center; width:50%;'>
                                        <label for="status_pending">Pending</label>
                                        <input type="radio" id="status_pending" name="invoice_status" value="Pending" checked>
                                    </td>
                                    <td style='display:flex; flex-direction:row; align-items:center; width:50%;'>
                                        <label for="status_completed">Completed</label>
                                        <input type="radio" id="status_completed" name="invoice_status" value="Completed">
                                    </td>
                                </tr>
                            </tbody>
                        </table>        
                        <br>
                    </div>
                    <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">    
                    <input type="hidden" name="appt_id" value="<?php echo $appt_id; ?>">    
                    <button type="submit">Create Payment</button>
                </form>
            </div>
        </div>
        <!-- </center> -->
    </body>
</html>