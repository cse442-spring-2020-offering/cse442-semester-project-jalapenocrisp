<?php
$message="";
if(count($_POST)>0) {
	$conn = mysqli_connect("tethys.cse.buffalo.edu","nekesame","50278839","cse442_542_2020_spring_teami_db");
	$result = mysqli_query($conn,"SELECT * FROM students WHERE email='" . $_POST["email"] . "' and paswd = '". $_POST["paswd"]."'");
	$count  = mysqli_num_rows($result);
	if($count==0) {
		$message = "Invalid email or password!";
	} else {

        $row = mysqli_fetch_array($result);
        $message = "You are successfully authenticated!";
        $var1=$row['user_id'];

        
        $ress2 = mysqli_query($conn, "SELECT complete, cancel from students where user_id=$var1 ;");
        $arr_ = mysqli_fetch_array($ress2);
        $num_of_complete = $arr_["complete"];  
        $num_of_cancel = $arr_["cancel"];
        if($num_of_complete >= $num_of_cancel){
            header('Location: ./student-appts.php?user_id=' .$var1);
        }else{
            $message= " It seems you cancellation rate is too high <br> You're currently being banned. For more info <a href=\"student-appeal.php?user_id=$var1\"> contact us </a>";
        }
	}
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <script type="text/javascript" src="js/modernizr.custom.86080.js"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500&family=Noto+Serif:wght@700&family=Roboto+Slab:wght@900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow&family=Fredericka+the+Great&family=Noto+Serif&family=Roboto&display=swap" rel="stylesheet">
    <title>UB Tutoring Service</title>
</head>

<body>
    <div class="header">
        <div class="menu_welcomePage">
            <ul>
                <!-- the line of code commented below is important when we upload the work on a server. for now, i'm using an alternative below -->
                <!-- <li><a href="javascript:loadPage('./login.php')">login</a> </li> -->
                <li>
                    <a class="navlink" href="../create-account.html">create account</a> </li>
                <li>
                    <a class="navlink" href="../index.html">home</a> </li>


            </ul>
        </div>

        <div class="logo">
            <h2 class="logo"> <a href="../index.html">UBtutoring</a> </h2>
        </div>
    </div>
    <hr class="hr-navbar">

    
    <br>
    <br>
    <hr class="hr-navbar">
    <br>
    <br>
    <div class="modal">
    <h1 class="welcome-page-title modal-title">Student Log In</h1>
    <br> <br>

    <div id="tutor_signup_div">
        <form name="frmUser" method='post' action="">

        <div class="message">
    
        <?php if($message!="") { 
            echo $message; 
            
            } ?> 
        </div> 
        
        <div class="modal-input">

            <label for="email">Email</label>
            <input class="log_in_input" type="text" id="email" name="email" placeholder="email" autofocus>

            <label for="password">Password</label>
            <input class="log_in_input" type="password" id="password" name="paswd" placeholder="password">
            
            <input id="log_in_button" name="submit" type="submit" value="Submit">
            <br>
            <br>
            <br>
            <a href="user-forgot-student.php" id="forgot_link_id"> forgot password? </a>
            <br><br>
        </form>
    </div>
    
    </div>
    <!-- <button class="selectButton" onclick="window.location.href = '../create-account.html';">Register</button> -->

    <script src="../index.js"></script>
    
</body>

</html>
