<?php
include_once "access-db.php";
$tut=$_GET['tutor_id'];
$stu=$_GET['user_id'];
$res = mysqli_query($conn,"SELECT * FROM calendar WHERE user_id='" . $_GET['tutor_id'] . "'");
$r=mysqli_fetch_array($res);

$tutorRes= mysqli_query($conn,"SELECT * FROM tutors WHERE user_id=$tut");
$tutarray = mysqli_fetch_array($tutorRes);
$tutorEmail=$tutarray['email'];

$progress= mysqli_query($conn,"SELECT * FROM progress WHERE student_id='" . $_GET['user_id'] . "'");


if (isset($_POST['submit'])){

    $dayTime= $_POST['date'];
    $day=substr($dayTime, 0, 3);
    $time=substr($dayTime,3);
    if($day=="mon"){
        $day="Monday";
    }else if($day=="tue"){
        $day="Tuesday";
    }else if($day=="wed"){
        $day="Wednesday";
    }else if($day=="thu"){
        $day="Thursday";
    }else if($day=="fri"){
        $day="Friday";
    }else if($day=="sat"){
        $day="Saturday";
    }else{
        $day="Sunday";
    }
    $sql = "INSERT INTO appointments (tutor_id, student_id, day, time) VALUES (?,?,?,?)";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("iisi", $tut, $stu, $day, $time);
    $stmt->execute();
    $stmt->close();


    $sql2 = "UPDATE calendar SET $dayTime=0 WHERE user_id=?";
    $stmt2= $conn->prepare($sql2);
    $stmt2->bind_param("i",$tut);
    $stmt2->execute();
    $stmt2->close();

    $studRes= mysqli_query($conn,"SELECT * FROM students WHERE user_id=$stu");
    $stuarray = mysqli_fetch_array($tutorRes);
    $stuEmail=$stuarray['email'];

    $tos=$stuEmail;
    $to=$tutorEmail;
    $subject="Notification of new appointment";
    $message="Dear " . $tutarray['fname'] . " " . $tutarray['lname'] .":\r\nWe are writing to notify that a new appointment at " . $time . ":00 on " . $day . " has been scheduled. No further action is necesary by you.\r\n\r\nUBtutoring\r\n\r\nPlease do not reply to this.";
    $messageS="Dear " . $stuarray['fname'] . " " . $stuarray['lname'] .":\r\nWe are writing to notify that a new appointment at " . $time . ":00 on " . $day . " has been scheduled. No further action is necesary by you.\r\n\r\nUBtutoring\r\n\r\nPlease do not reply to this.";

    $from="no-reply@buffalo.com";
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/plain; charset=iso-8859-1" . "\r\n";
    $headers .= "From: ". $from. "\r\n";
    $headers .= "Reply-To: ". $from. "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "X-Priority: 1" . "\r\n";

    if($tutarray['verified']){
        mail( $toText, '', $message );
    }
    if ($stuarray['verified']){
        mail( $toTextStu, '', $messageS );
    }
    mail($to, $subject, $message, $headers);
    mail($tos, $subject, $messageS, $headers);


    //header('Location: student-appts.php?user_id=' . $stu);
    header('Location: student-appt-info.php?user_id=' . $stu .'&tutor=' . $tut);

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    <title>UB Tutoring Service</title>
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500&family=Noto+Serif:wght@700&family=Roboto+Slab:wght@900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow&family=Fredericka+the+Great&family=Noto+Serif&family=Roboto&display=swap" rel="stylesheet"></head>


<body>

    <div class="header">

        <div class="menu_welcomePage">
            <ul>

                <!-- the line of code commented below is important when we upload the work on a server. for now, i'm using an alternative below -->
                <!-- <li><a href="javascript:loadPage('./login.php')">login</a> </li> -->
                <li><a class="navlink" href="./student-appts.php?user_id=<?php echo $_GET['user_id']; ?>">my appointments</a> </li>
                <li><a class="navlink" href="./search.php?user_id=<?php echo $_GET['user_id']; ?>">find a tutor</a> </li>
                <div class="dropdown">
                        <li><a class="dropbtn">my progress</a>
                            <div class="dropdown-content">
                                <?php 
                                while ($progressInfo = mysqli_fetch_array($progress)){ 
                                    $linkname=$progressInfo['course'];
                                    $link="./student-progress.php?user_id=" . $_GET['user_id'] . "&cid=" . $linkname ; 
                                    echo "<a href=".$link.">".$linkname."</a>";}
                                ?>
                            </div>
                        </li>
                    </div>                <li><a class="navlink" href="./studentprof.php?user_id=<?php echo $_GET['user_id']; ?>">profile</a> </li>
                <li><a class="navlink" href="../index.html">logout</a> </li>

            </ul>
        </div>
           
        <div class="logo">
            <h2 class="logo"> <a href="../index.html">UBtutoring</a> </h2>
        </div>
        
    </div>
    <hr class="hr-navbar">

    <h1 class = "modal-title welcome-page-title">Appointment Slots</h1>
    <a class="center" href="./tutorprof-student.php?user_id=<?php echo $_GET['user_id'];?>&tutor_id=<?php echo $_GET['tutor_id'];?>">back to profile</a>
    <br><br>
    <table id=calendar_tutor rules="all">
            <tr style="height: 40px">
                <th>
                    <span id="calendar_monday"></span>
                </th>
                <th>
                    <span id="calendar_monday">Monday</span>
                </th>
                <th>
                    <span id="calendar_tuesday">Tuesday</span>
                </th>
                <th>
                    <span id="calendar_wednesday">Wednesday</span>
                </th>
                <th>
                    <span id="calendar_thursday">Thursday</span>
                </th>
                <th>
                    <span id="calendar_friday">Friday</span>
                </th>
                <th>
                    <span id="calendar_saturday">Saturday</span>
                </th>
                <th>
                    <span id="calendar_sunday">Sunday</span>
                </th>
            </tr>
            <tr style="height: 40px"><td>9:00AM</td>
                <td ><?php if ($r['mon9']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon9'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue9']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue9'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed9']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed9'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu9']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu9'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri9']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri9'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat9']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat9'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun9']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun9'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>10:00AM</td>
            <td><?php if ($r['mon10']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon10'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue10']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue10'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed10']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed10'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu10']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu10'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri10']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri10'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat10']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat10'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun10']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun10'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>11:00AM</td>
                <td><?php if ($r['mon11']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon11'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue11']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue11'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed11']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed11'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu11']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu11'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri11']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri11'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat11']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat11'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun11']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun11'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>12:00PM</td>
                <td><?php if ($r['mon12']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon12'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue12']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue12'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed12']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed12'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu12']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu12'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri12']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri12'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat12']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat12'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun12']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun12'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>1:00PM</td>
                <td><?php if ($r['mon13']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon13'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue13']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue13'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed13']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed13'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu13']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu13'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri13']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri13'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat13']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat13'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun13']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun13'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>2:00PM</td>
                <td><?php if ($r['mon14']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon14'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue14']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue14'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed14']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed14'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu14']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu14'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri14']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri14'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat14']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat14'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun14']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun14'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td></td>
            </tr>
            <tr style="height: 40px"><td>3:00PM</td>
                <td><?php if ($r['mon15']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon15'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue15']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue15'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed15']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed15'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu15']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu15'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri15']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri15'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat15']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat15'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun15']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun15'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>4:00PM</td>
                <td><?php if ($r['mon16']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon16'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue16']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue16'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed16']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed16'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu16']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu16'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri16']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri16'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat16']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat16'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun16']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun16'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>5:00PM</td>
                <td><?php if ($r['mon17']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon17'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue17']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue17'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed17']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed17'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu17']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu17'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri17']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri17'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat17']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat17'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun17']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun17'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>
            <tr style="height: 40px"><td>6:00PM</td>
                <td><?php if ($r['mon18']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon18'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue18']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue18'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed18']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed18'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu18']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu18'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri18']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri18'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat18']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat18'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun18']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun18'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>            
            <tr style="height: 40px"><td>7:00PM</td>
                <td><?php if ($r['mon19']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon19'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue19']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue19'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed19']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed19'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu19']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu19'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri19']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri19'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat19']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat19'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun19']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun19'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>            
            <tr style="height: 40px"><td>8:00PM</td>
                <td><?php if ($r['mon20']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon20'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue20']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue20'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed20']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed20'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu20']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu20'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri20']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri20'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat20']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat20'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun20']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun20'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>            
            <tr style="height: 40px"><td>9:00PM</td>
                <td><?php if ($r['mon21']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='mon21'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['tue21']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='tue21'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['wed21']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='wed21'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['thu21']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='thu21'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['fri21']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='fri21'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sat21']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sat21'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
                <td><?php if ($r['sun21']==1){ echo "<form method='post'><input class='cancel' type='hidden' name='date' value='sun21'><input class='cancel' type='submit' name='submit' value='claim'></form>";}?></td>
            </tr>

    <script src="../index.js"></script>
    <script>
    </script>
  
</body>

</html>


