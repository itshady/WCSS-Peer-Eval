<?php
    require("../functions.php");
    session_start();

    if (!authUser()) {
      echo "You are not authorized!";
      die();
    }

    if (userType($_SESSION['userID']) == "student") {
      echo "Students are not authorized here!";
      die();
    }

    if (serverPOST() && isset($_POST['target']) && $_POST['target'] == 'updateStudent') {
        if ((!empty($_POST['modal_newPass']) && !empty($_POST['modal_confirmPass'])) && $_POST['modal_newPass'] == $_POST['modal_confirmPass']) $newPass = $_POST['modal_newPass'];
        else $newPass = "";
        updateAccountInfo($_POST['modal_acct_id'],$_POST['modal_fname'],$_POST['modal_lname'],"",$newPass,"student");
    }
    else if (serverPOST() && isset($_POST['target']) && $_POST['target'] == 'updateAccount') {
        if ((!empty($_POST['modal_newPass']) && !empty($_POST['modal_confirmPass'])) && $_POST['modal_newPass'] == $_POST['modal_confirmPass']) $newPass = $_POST['modal_newPass'];
        else $newPass = "";
        if (userType($_POST['modal_acct_id']) == "student" && $_POST['modal_role'] != "student") {
          //echo '<script>alert("Welcome to Geeks for Geeks")</script>';
          //get all project id theyre in
          $listOfProj = getAllStudentsProjects($_POST['modal_acct_id']);

          //go through each project and remove them from it
          foreach ($listOfProj as $arr) {
            removeStudentFromProject($arr['proj_id'],$_POST['modal_acct_id']);
          }
        }
        updateAccountInfo($_POST['modal_acct_id'],$_POST['modal_fname'],$_POST['modal_lname'],"",$newPass,$_POST['modal_role']);
    }
    else if (serverPOST() && isset($_POST['target']) && $_POST['target'] == 'updateTeacher') {
        if ((!empty($_POST['newPass']) && !empty($_POST['confirmPass'])) && $_POST['newPass'] == $_POST['confirmPass']) $newPass = $_POST['newPass'];
        else $newPass = "";
        updateAccountInfo($_SESSION['userID'],$_POST['fname'],$_POST['lname'],"",$newPass,"teacher");
        if (!empty($_POST['defaultpoints']) && is_numeric($_POST['defaultpoints']) && $_POST['defaultpoints'] > 0) updateUserSetting("defaultPoints",$_POST['defaultpoints'],$_SESSION['userID']);
    }
    

?>

<!DOCTYPE html>
<!-- Developed by Hady Ibrahim and Shushawn Saha -->
<html lang="en" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Settings | <?php echo getCurrentUserName() ?> | Peer Evalutaor </title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"> <!-- Data Table cs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link href="../css/teacherSettings.css" rel="stylesheet">
    <link href="../css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

	  <link rel="icon" href="../favicon.png">
  </head>

  <body>

    <!-- New Eval Button-->
    <button type="button" class="invisible" data-bs-toggle="modal" data-bs-target="#exampleModal">
    <div class="wrapper visible">
      <div class="pulse"> <i class="fa fa-plus"></i> </div>
    </div>
    </button>

    <!-- NAVBAR -->
    <div class="position-fixed top-0 start-0 bg-primary myNavbar">
      <div class="tab" id="tab1" onclick="location.href = '../teacher.php';"></div>
      <div class="tab" id="tab2" onclick="location.href = '';"></div>
      <div class="tab" id="tab3" onclick="location.href = '../logout.php';"></div>
    </div>

    <div class="position-fixed top-0 start-0 bg-primary myNavbar" id="testing">
      <div class="tab-text rotate" id="header1" onclick="location.href = '../teacher.php';">Evaluations</div>
      <div class="tab-text rotate" id="header2" onclick="location.href = '';">Settings</div>
      <div class="tab-text rotate" id="header3" onclick="location.href = '../logout.php';">Logout</div>
    </div>

    <!-- HEADER -->
    <div class="position-absolute top-0 p-1 myHeader">
      <div class="position-absolute top-0 align-items-center d-flex">
        <div>Welcome👋</div>
        <div class="welcome-name mx-1"><?php echo getCurrentUserName() ?>ツ</div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id='modal-title'>d</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post" id="myModalForm" class='needs-validation'>
          <div class="modal-body" id='modal-body'>
            <!-- =========================== MODAL BODY =============================-->

            <!-- =========================== CLOSE MODAL BODY =============================-->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary myModalSave">Save</button>
          </div>
          </form>
        </div>
      </div>
    </div>


    <!-- WRAPPER -->
  <div class="wrap-all d-flex flex-row justify-content-center">
    <div class="d-flex flex-column left-settings" id='left-settings'>
        <?php
            if (userType($_SESSION['userID']) == 'admin'){
                echo "<table id=\"myTable\" class=\"display myTable\" style=\"width:100%\">\n";
                echo "<thead>\n";
                    echo "<tr>\n";
                        echo "<th>Last Name</th>\n";
                        echo "<th>First Name</th>\n";
                        echo "<th>Email</th>\n";
                        echo "<th>Role</th>\n";
                        echo "<th></th>\n";
                    echo "</tr>\n";
                echo "</thead>\n";
                echo "<tbody>\n";
                    $listOfAllAccounts = getAllNotMe($_SESSION['userID']);

                    //print all students in table
                    foreach($listOfAllAccounts as $acc){
                        echo "<tr>";
                        echo "<td>".$acc['acct_lname']."</td>\n";
                        echo "<td>".$acc['acct_fname']."</td>\n";
                        echo "<td>".$acc['acct_user']."</td>\n";
                        echo "<td>".$acc['acct_role']."</td>\n";
                        echo "<td class='text-center editTD'><i class=\"fas fa-user-edit editAccountInfo\"id='editAccountInfo". $acc['acct_id'] ."' data-bs-toggle=\"modal\" data-bs-target=\"#myModal\"></i></td>\n";
                        echo "</tr>\n";
                    }
                echo "</tbody>\n";
                echo "</table>\n";
            }
            else if (userType($_SESSION['userID']) == 'teacher') {
                echo "<table id=\"myTable\" class=\"display myTable\" style=\"width:100%\">\n";
                echo "<thead>\n";
                    echo "<tr>\n";
                        echo "<th>Last Name</th>\n";
                        echo "<th>First Name</th>\n";
                        echo "<th>Email</th>\n";
                        echo "<th></th>\n";
                    echo "</tr>\n";
                echo "</thead>\n";
                echo "<tbody>\n";
                    //get array of all teachers project ids
                    $projectList = getProjects($_SESSION['userID']);
                    $projectListIDs = array();
                    foreach ($projectList as $proj) {
                        array_push($projectListIDs,$proj['proj_id']);
                    }
                
                    //get list of all students in all projects (3D array)
                    $studentListInProj = array();
                    foreach ($projectListIDs as $proj_id) {
                        array_push($studentListInProj,getStudentList($proj_id));
                    }
                
                    //make it 2D array of all students info (will have duplicate students)
                    $rawStudentList = array();
                    foreach($studentListInProj as $proj) {
                        foreach ($proj as $studentInfo) {
                            array_push($rawStudentList,$studentInfo);
                        }
                    }
                
                    //get rid of duplicate students
                    $rawStudentList = unique_multidim_array($rawStudentList,'acct_id');

                    //print all students in table
                    foreach($rawStudentList as $stud){
                        echo "<tr>";
                        echo "<td>".$stud['acct_lname']."</td>\n";
                        echo "<td>".$stud['acct_fname']."</td>\n";
                        echo "<td>".$stud['acct_user']."</td>\n";
                        echo "<td class='text-center editTD'><i class=\"fas fa-user-edit editStudentInfo\"id='editStudentInfo". $stud['acct_id'] ."' data-bs-toggle=\"modal\" data-bs-target=\"#myModal\"></i></td>\n";
                        echo "</tr>\n";
                    }
                echo "</tbody>\n";
                echo "</table>\n";
            }
        ?>
    </div>
    <div class="d-flex flex-column right-settings align-items-center">
    <?php
        if (userType($_SESSION['userID']) == 'admin') {
            echo "<div class='mySidebar accountSettings'>\n";
            echo    "All Accounts\n";
            echo "</div>\n";
        }
    ?>
        <div class='mySidebar studentSettings'>
            My Students
        </div>
        <div class='mySidebar mySettings'>
            My Settings
        </div>
        <div class='mySidebar' onclick="location.href = '../logout.php';">
            Logout
        </div>
    </div>
  </div>
  


    <!-- Scripts -->
    <script src="../js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> <!-- Data Table js -->
    <script src="../js/teacherSettings.js"></script>
    <script>
        var session_id = <?php echo $_SESSION['userID'];?>;
        var adminBool = <?php
          if (userType($_SESSION['userID']) == "admin") echo 1;
          else echo 0;
        ?>;
    </script>
  </body>

</html>
