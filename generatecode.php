<?php
    session_start();
    $db=mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
    $msg = [];
    $msg1 = [];

    //echo $_SESSION['username'];
    if(!isset($_SESSION['username']))
    {
        $_SESSION['redirect'] = 'generatecode.php';
        header("location: login.php");
    }
    if($_SESSION['role'] == 'dentist' || $_SESSION['role'] == 'patient')
    header("location: index.php");

    function generateRandomCode($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    if(isset($_POST['add_code'])) {
        $codeToAdd = mysqli_real_escape_string($db, $_POST['new_code']);
        $query = "INSERT INTO registration_code (regCode_number, regCode_validity) VALUES ('$codeToAdd', '1')";
        // echo $query;

        $res = mysqli_query($db, $query);
        if ($res) {
            array_push($msg, "<h3 style='color:green'>Registration Code Successfully Added to Database!</h3>");
        }
    }

    $duplicateCode = true;
    while ($duplicateCode) {
        $generatedCode = generateRandomCode();
        $query = "SELECT `regCode_number` FROM `registration_code` WHERE `regCode_number` = '$generatedCode'";
        // echo $query;

        $res = mysqli_query($db, $query);
        if ($res && mysqli_num_rows($res) == 0) {
                $duplicateCode = false;
                // echo $generatedCode; // Outputs something like "3aB9z"
        }
    }

    $all_codes_query = "SELECT * FROM registration_code";
    // echo $all_codes_query;
    $res_all_codes = mysqli_query($db, $all_codes_query);
    if (!$res_all_codes) {
        array_push($msg1, "<h3 style='color:Red'>Error with retrieving registration codes</h3>");
    }
    else {
        if (mysqli_num_rows($res_all_codes) <= 0) {
            echo $all_codes_query;
            array_push($msg1, "<h3 style='color:Red'>No Existing Registration Codes</h3>");
        }
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Generate Registration Code</title>
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
    <?php require_once("header.php");?>
    <center>
        <div class="content-section" style="width:70%">
            <h3>New Registration Code</h3><br>
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
            <form method='POST'>
                <table>
                    <tr>
                        <th>Randomly Generated Code</th>
                        <td><input class='readonly' type='text' name='new_code' value='<?php echo $generatedCode?>' required readonly></td>
                    </tr>
                </table>
                <br><input type='submit' name='regenerate_code' class='example_e' style='width:50%' value='Regenerate Code'>
                <br><input type='submit' name='add_code' value='Add Code to Database' class='example_e' style='width:50%'>
            </form>
        </div>
        <br>
        <br>
        <div class="content-section" style="width:70%">
            <h3>List of Registration Codes</h3><br>
            <?php 
            if(sizeof($msg1) > 0)
            {
                foreach($msg1 as $m1)
                {
                    echo $m1;
                }
            }
            ?>
            <br>
            <table>
                <tr>
                    <th>Code Number</th>
                    <th>Code Validity</th>
                </tr>
                <?php
                if ($res_all_codes && mysqli_num_rows($res_all_codes) > 0) {
                    while ($row = mysqli_fetch_assoc($res_all_codes)) {
                        echo "
                        <tr>
                            <td><input class='' type='text' name='new_code' value='".$row['regCode_number']."' required readonly></td>
                            <td><input class='readonly' type='text' name='new_code' value='".($row['regCode_validity'] == 1 ? "Valid" : "Invalid")."' required readonly></td>
                        </tr>
                        ";
                    }
                } 
                ?>
            </table>
        </div>
    </center>
</body>
</html>