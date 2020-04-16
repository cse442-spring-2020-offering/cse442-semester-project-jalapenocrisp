<?php
include_once 'access-db.php';
if (isset($_POST['submit'])) {
    $userid=$_GET['user_id'];
    if (getimagesize($_FILES['imagefile']['tmp_name']) == false) {
        echo "<br />Please Select An Image.";
    } else {
        $image = $_FILES['imagefile']['tmp_name'];
        $name = $_FILES['imagefile']['name'];
        $image = base64_encode(file_get_contents(addslashes($image)));
        mysqli_query($conn, "UPDATE students SET img_name='" . $name . "', user_image='" . $image . "' WHERE user_id='" . $userid . "'");

    }
    header('Location: ./studentprof.php?user_id=' .$userid);
}

$progress= mysqli_query($conn,"SELECT * FROM progress WHERE student_id='" . $_GET['user_id'] . "'");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content ="width=device-width,initial-scale=1,user-scalable=yes" />
    <title>UB Tutoring</title>
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <script type="text/javascript" src="js/modernizr.custom.86080.js"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>UB Tutoring Service</title>
</head>
<body class="main-container">

    <div class="header">

        <div class="menu_welcomePage">
            <ul>
                    <li><a class="navlink" href="./student-appts.php?user_id=<?php echo $_GET['user_id']; ?>">my appointments</a> </li>
                    <div class="dropdown">
                    <li><button onclick="progressclick()" class="dropbtn">my progress</button>
                            <div id="myDropdown" class="dropdown-content">
                                <?php 
                                if (mysqli_num_rows($progress)<1){
                                    echo "<p class='center'>no progress yet</p>";
                                }else{
                                while ($progressInfo = mysqli_fetch_array($progress)){ 
                                    $linkname=$progressInfo['course'];
                                    $link="./student-progress.php?user_id=" . $_GET['user_id'] . "&cid=" . $linkname ; 
                                    echo "<a href=".$link.">".$linkname."</a>";}
                                }
                                ?>
                            </div>
                        </li>
                    </div>
                    <li><a class="navlink" href="./studentprof.php?user_id=<?php echo $_GET['user_id']; ?>">profile</a> </li>
                <li><a class="navlink" href="../index.html">logout</a> </li>

            </ul>
        </div>

        <div class="logo">
            <h2 class="logo"> <a href="../index.html">UBtutoring</a> </h2>
        </div>

    </div>
    <hr class="hr-navbar">

    <div class="center">
        <p> * must be jpeg format * </p>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="file" name="imagefile">
            <input type="submit" name="submit" value="Upload">
        </form>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="index.js"></script>
    <script>
        
    </script>

</body>

</html>
