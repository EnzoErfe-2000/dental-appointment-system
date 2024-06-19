<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Close button -->
    <button class="close-btn" onclick="toggleSidebar()">Close</button>

    <!-- Sidebar menu -->
    <ul>
        <li><a href="index.php">Home</a></li>
        <?php if(!isset($_SESSION['username'])){?> 
        <!-- <li><a href="registration.php">Sign up</a></li>	 -->
        <li><a href="schedules.php">Book Appointment</a></li>	
        <li><a href="checkappointment.php">Check Appointments</a></li>
        <li><a class="cta" href="login1.php">Admin Login</a></li>
        <?php }?>
        <?php if(isset($_SESSION['username'])){?>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'dentist'){?>
            <li><a href="pastappointments1.php">Past Appointments</a></li>
        <?php }?>    
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){?>
            <li><a href='addclinic1.php'>Add Clinic</a></li>
            <li><a href='adddentist1.php'>Add Dentist</a></li>
            <li><a href='allappointments.php'>View Appointments</a></li>
            <li><a href='generatecode.php'>Manage  Registration Codes</a></li>
        <?php }?>    
        <li><a href="index.php?logout='1'">Logout</a> </li>	
        <?php }?>           
    </ul>
</div>

<header>
    <a class="logo" href="index.php"><img src="images/logo.jpg" alt="logo"></a>
    
    <a href="javascript:void(0);" class="burger-icon" onclick="toggleSidebar()">
        <i class="fa fa-bars" style="color:white;"></i>
    </a>

    <script>
    function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");
    }
    </script>

    <nav>
        <ul class="nav__links" <?php if(isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {echo "style='font-size:small;'";}?>>
            <li><a href="index.php">Home</a></li>
            <?php if(!isset($_SESSION['username'])){?> 
                <!-- <li><a href="registration.php">Sign up</a></li>	 -->
                <li><a href="schedules.php">Book Appointment</a></li>	
                <li><a href="checkappointment.php">Check Appointments</a></li>
                <li><a class="cta" href="login1.php">Admin Login</a></li>
            <?php }?>
            <?php if(isset($_SESSION['username'])){
                if($_SESSION['role'] == 'admin')
                {

                    echo "<li><a href='addclinic1.php'>Add Clinic</a></li>";
                    echo "<li><a href='adddentist1.php'>Add Dentist</a></li>";
                    echo "<li><a href='allappointments.php'>View Appointments</a></li>";
                    echo "<li><a href='generatecode.php'>Manage  Registration Codes</a></li>";
                }
                else {
                if($_SESSION['role'] == 'patient')
                {
                    echo "<li><a href='clinics.php'>Clinics </a></li>";
                }
                
                echo "<li><a href='appointments1.php?username'".$_SESSION['username'].">Appointments</a></li>";
            ?> 	
            <li><a href="pastappointments1.php">Past Appointments</a></li>
            
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