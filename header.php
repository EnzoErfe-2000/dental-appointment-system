<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<header>
    <div class="mobile-nav-links" id="mobile-nav-links">
        <a href="#schedules.php">Schedules</a>
        <a href="#check-appointment.php">Check My Appointments</a>
        <a href="#login.php">Login</a>
    </div>
    
    <a class="logo" href="index.php"><img src="images/logo.jpg" alt="logo"></a>
    
    <a href="javascript:void(0);" class="burger-icon" onclick="myFunction()">
        <i class="fa fa-bars"></i>
    </a>

    <script>
    function myFunction() {
        const navLinks = document.getElementById('mobile-nav-links');
        
        if (navLinks.style.display === "block") {
            navLinks.style.display = "none";
        }
        else {
            navLinks.style.display = "block";
        };
    }
    </script>

    <nav>
        <ul class="nav__links">
            <li><a href="index.php">Home</a></li>
            <?php if(!isset($_SESSION['username'])){?> 
            <!-- <li><a href="registration.php">Sign up</a></li>	 -->
            <li><a href="schedules.php">Schedules</a></li>	
            <li><a href="check-appointment.php">Check My Appointment</a></li>
            <li><a class="cta" href="login.php">Login</a></li>
            <?php }?>
            <?php if(isset($_SESSION['username'])){
                if($_SESSION['role'] == 'admin')
                {
                    echo "<li><a href='addclinic.php'>Add Clinic</a></li>";
                    echo "<li><a href='adddentist.php'>Add Dentist</a></li>";
                }
                else {
                if($_SESSION['role'] == 'patient')
                {
                    echo "<li><a href='clinics.php'>Clinics </a></li>";
                }
            ?> 	
            
            <li><a href="appointments.php">Appointments</a></li>
            <li><a href="pastappointments.php">Past Appointments</a></li>
            <?php if($_SESSION['role'] == 'patient'){
                echo "<li><a href='updateaccount.php'>Update Account</a></li>";
            }}?>
        
            <li><a href="index.php?logout='1'">Logout</a> </li>	
            <?php }?>           
                </ul>
            </nav>
           
            <p class="menu cta">Menu</p>
        </header>
  <br><br>