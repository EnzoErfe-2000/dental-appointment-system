<?php
session_start();
$db = mysqli_connect('localhost','root','','dcms') or die("could not connect to database");
if(isset($_SESSION['username']))
header('location:index.php');
// echo $_SESSION['redirect'];
$username="";
$count1=0;
$errors=[];
  if(isset($_POST['login_user'])){
    if(!isset($_SESSION['username']))
    {
      $username = mysqli_real_escape_string($db, $_POST ['username']);
      $password = mysqli_real_escape_string($db, $_POST['password']);
      // $role = mysqli_real_escape_string($db, $_POST['role']);
      if(!empty($username) && !empty($password))
      {
        $password= hash('sha256',$password);
        // $query = "SELECT * FROM user WHERE username='$username' and password='$password' and role = '$role' LIMIT 1";
        $query = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
        $results = mysqli_query($db, $query);

        if ($results) {
            // Check if exactly one row matches the username and password
            if (mysqli_num_rows($results) == 1) {
                $res = mysqli_fetch_assoc($results);

                // Verify username and password match
                if ($username == $res['username'] && $password == $res['password']) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $res['role'];

                    if ($res['role'] == 'dentist') {
                        // Fetch dentist's name if role is dentist
                        $reqDentistName = "SELECT dentist_name FROM dentist WHERE user_username = '$username'";
                        $resDentistName = mysqli_query($db, $reqDentistName);
                        
                        if ($resDentistName) {
                            if (mysqli_num_rows($resDentistName) == 1) {
                                $row = mysqli_fetch_assoc($resDentistName);
                                $_SESSION['dentistName'] = $row['dentist_name'];
                            } else {
                                $_SESSION['dentistName'] = "Unknown Dentist";
                            }
                        } else {
                            echo "Error executing dentist query: " . mysqli_error($db);
                            exit; // Exit script after displaying error
                        }
                    }

                    // Redirect if there's a redirection URL in session
                    if (isset($_SESSION['redirect'])) {
                        $link = $_SESSION['redirect'];
                        unset($_SESSION['redirect']);
                        header("Location: $link");
                        exit; // Ensure script stops after redirection header
                    } else {
                        header('Location: index.php');
                        exit; // Ensure script stops after redirection header
                    }
                } else {
                    // Invalid credentials
                    array_push($errors, "Invalid credentials entered");
                }
            } else {
                // No rows found matching username and password
                array_push($errors, "Invalid credentials entered");
            }
        } else {
            // Query execution failed
            echo "Error executing main query: " . mysqli_error($db);
            exit; // Exit script after displaying error
        }
      }
    }
  }
?>
<html>
<head>
  <title>Log in</title>
  <link rel="stylesheet" href="css1/style1.css">
   <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="styles-1.css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:500&display=swap" rel="stylesheet">
</head>
<body>
<?php require_once("header.php");?>
<section class="sign-in">
    <div class="container">
        <div class="signin-content">
            <div class="signin-image">
                <figure><img src="images/signin-image.jpg" alt="sing up image"></figure>
                <a href="registration.php" class="signup-image-link">New here? Create an account</a>
            </div>

            <div class="signin-form">
                <h2 class="form-title">Log in</h2>
                <?php
                if(sizeof($errors)>0)
                {
                  foreach($errors as $err)
                  {
                    echo "<h3 style='color:red; width:75%'>".$err."</h3><br>";
                  }
                }
                ?>
                <form method="POST" class="register-form" id="login-form">
                    <div class="form-group">
                        <label for="username"><i class="zmdi zmdi-account material-icons-name"></i></label>
                        <input type="text" name="username" id="your_name" placeholder="Username" required/>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="zmdi zmdi-lock"></i></label>
                        <input type="password" name="password" id="your_pass" placeholder="Password" required/>
                    </div>
                    <!-- 
                    <div class="form-group">
                    <label style="font-size:18px">Select the type of user</label><br></div>
                    <div class="form-group">
                    <p style="color:white">Dentist</p>
                    <input type="radio" name="role" id="dentist" value="dentist" required style="width:60%">
                    <label for="dentist" style="font-weight:normal; font-size:16px">Dentist</label><br>
                    </div>
                    <div class="form-group">
                    <p style="color:white">Patient</p>
                    <input type="radio" name="role" id="patient" value="patient" required style="width:60%">
                    <label for="patient" style="font-weight:normal; font-size:16px">Patient</label><br>
                    </div> 
                    <div class="form-group">
                    <p style="color:white">Admin</p>
                    <input type="radio" name="role" id="admin" value="admin" required style="width:60%">
                    <label for="patient" style="font-weight:normal; font-size:16px">Admin</label><br>
                    </div>
                    -->
                    <div class="form-group form-button">
                        <input type="submit" class="example_e" name="login_user" id="signin" value="Login"/>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
  </center>
</body>
</html>