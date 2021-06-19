<?php
  require("functions.php");
  session_start();

  //auhtorizes user
  if (!authUser()) {
      echo "You are not authorized";
      die();
  }
  
  //gets groupID
  $groupId = findGroupID($_SESSION['userID']);

 // print_r($groupId);

  //gets detailed information for each group
  $i=0;

  foreach ($groupId as $group) {
  
    //only runs if it gets valid information

    if( findGroupName($group['member_group_id']) ) {    //gets the name of each group
      $groupNames[$i] = findGroupName($group['member_group_id']);
    }

    if( getGroupStudDetail ($group['member_group_id']) ) {    //get detailed information for each student
      $groupMembInfo[$i] = getGroupStudDetail ($group['member_group_id']);
    }

    if( getGroupTeachDetail($groupMembInfo[$i][0]['proj_id']) ) {   //get teacher information
      $groupTeacherInfo[$i] = getGroupTeachDetail($groupMembInfo[$i][0]['proj_id']);
    }
    
    $i++;
  }

/*
  print_r($groupNames);
  print_r($groupMembInfo);
  print_r($groupTeacherInfo);
*/
//print_r($results);
   
?>

<!DOCTYPE html>
<!-- Developed by Hady Ibrahim and Shushawn Saha -->
<html lang="en" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title> <?php echo getCurrentUserName() ?> | Peer Evalutaor</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link href="css/student.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Reenie+Beanie&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link href="css/navbar.css" rel="stylesheet">
	  <link rel="icon" href="favicon.png">
  </head>

  <body>
    <!-- NAVBAR -->
    <div class="position-fixed top-0 start-0 bg-primary myNavbar">
      <!--<div class="tab" id="tab1" onclick="location.href = 'teacher.php';"></div>
      <div class="tab" id="tab2" onclick="location.href = 'teacherSettings.php';"></div>-->
      <div class="tab" id="tab3" onclick="location.href = 'logout.php';"></div>
    </div>

    <div class="position-fixed top-0 start-0 bg-primary myNavbar" id="testing">
      <!--<div class="tab-text rotate" id="header1" onclick="location.href = 'teacher.php';">Evaluations</div>
      <div class="tab-text rotate" id="header2" onclick="location.href = 'teacherSettings.php';">Settings</div>-->
      <div class="tab-text rotate" id="header3" onclick="location.href = 'logout.php';">Logout</div>
    </div>

    <!-- HEADER -->
    <div class="position-absolute top-0 p-1 myHeader">
      <div class="position-absolute top-0 align-items-center d-flex">
        <div>Welcome👋</div>
        <div class="welcome-name mx-1"><?php echo getCurrentUserName() ?>ツ</div>
      </div>
    </div>  



    <!--Displays all evaluations -->
    <br><br><br><br><center><h1>Running Evaluations</h1><hr></center>

    <!-- all cards are organized in a flexbox -->
    <!--notes for not submitted evals -->
    <div class="wrap-all d-flex flex-row justify-content-center">
      <ul>
      <?php
        if ($groupId) {
          $i = 0;
        //print_r($groupNames);
          foreach($groupNames as $gName){
            
            if ( isMarkSubmitted($groupMembInfo[$i][0]['proj_id'], $_SESSION['userID'] ) == 0) {
              if ($groupMembInfo[$i][0]['proj_enabled'] == 1){ 
              
                echo "<li>"; 
                  echo "<a contenditable>";
                  echo "<h1>" .$gName['group_name']. "</h1>";
                  echo "<h4>" .$groupMembInfo[$i][0]['proj_name']. " - " .$groupMembInfo[$i][0]['proj_classcode']. "</h4>";
                  echo "<p> <h6> Teacher: </h6>" .$groupTeacherInfo[$i][0]['acct_fname']. " " .$groupTeacherInfo[$i][0]['acct_lname']. "</p>";
                  echo "<p> <h6> Group Members: </h6>"; 
                  foreach ($groupMembInfo[$i] as $g) {
                    echo " ".$g['acct_fname']. " " .$g['acct_lname']. "<br>";
                  } "</p>";
                  
                    if ( isMarkSubmitted($groupMembInfo[$i][0]['proj_id'], $_SESSION['userID'] ) == 0) {
                      echo "<br><center><button type='button' class='btn btn-outline-danger btn-lg editEval' id='editEval".$groupId[$i]['member_group_id']."' onclick='openEditModal(".$groupId[$i]['member_group_id'].")'> Edit Evaluation </button></center>";
                    } else {
                      echo "<br><center><span class='d-inline-block' tabindex='0' data-bs-toggle='popover' data-bs-trigger='hover focus' data-bs-content='You have already submitted this evaluation!'>";
                        echo "<button class='btn btn-outline-primary' type='button' disabled>Already Submitted</button></center>";
                      echo "</span>";
                    }
  
                  echo "</a>";
                echo "</li>";
              
              } 
            } 
            
            $i++;
          }
        } 
      ?>
      </ul>
    </div>  

    <br><br><br><br><center><h1>Completed Evaluations</h1><hr></center>

    <!-- all cards are organized in a flexbox -->
    <!--notes for not submitted evals -->
    <div class="wrap-all d-flex flex-row justify-content-center"> 
    <ul>
    <?php
      if ($groupId) {
        $i = 0;
      //print_r($groupNames);
        foreach($groupNames as $gName){
          
          
          if ( isMarkSubmitted($groupMembInfo[$i][0]['proj_id'], $_SESSION['userID'] ) != 0) {
            if ($groupMembInfo[$i][0]['proj_enabled'] == 1){ 
            
              echo "<li>"; 
                echo "<a contenditable>";
                echo "<h1>" .$gName['group_name']. "</h1>";
                echo "<h4>" .$groupMembInfo[$i][0]['proj_name']. " - " .$groupMembInfo[$i][0]['proj_classcode']. "</h4>";
                echo "<p> <h6> Teacher: </h6>" .$groupTeacherInfo[$i][0]['acct_fname']. " " .$groupTeacherInfo[$i][0]['acct_lname']. "</p>";
                echo "<p> <h6> Group Members: </h6>"; 
                foreach ($groupMembInfo[$i] as $g) {
                  echo " ".$g['acct_fname']. " " .$g['acct_lname']. "<br>";
                } "</p>";
                
                  if ( isMarkSubmitted($groupMembInfo[$i][0]['proj_id'], $_SESSION['userID'] ) == 0) {
                    echo "<br><center><button type='button' class='btn btn-outline-danger btn-lg editEval' id='editEval".$groupId[$i]['member_group_id']."' onclick='openEditModal(".$groupId[$i]['member_group_id'].")'> Edit Evaluation </button></center>";
                  } else {
                    echo "<br><center><span class='d-inline-block' tabindex='0' data-bs-toggle='popover' data-bs-trigger='hover focus' data-bs-content='You have already submitted this evaluation!'>";
                      echo "<button class='btn btn-outline-primary' type='button' disabled>Already Submitted</button></center>";
                    echo "</span>";
                  }

                echo "</a>";
              echo "</li>";
            
            } 
          } 
          $i++;
        }
      } 
    ?>
    </ul>
    </div>
    

    <!-- Modal -->
    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" id="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="groupModalLabel">Peer Evaluation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body" id="groupModalBody">
            <!-- js prints out AJAX data here -->

          

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick='saveForm()' >Submit Scores</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

 

  

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script> <!-- Bootstrap framework -->
    <!-- high chart scripts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    
    <script src="js/student.js"></script>

  </body>

</html>