<?php
  include("../functions.php");
  session_start();

  //auhtorizes user
  if (!authUser()) {
      echo "You are not authorized";
      die();
  }

  //validates user id is validate
  if (!isset($_GET['projId']) || !is_numeric($_GET['projId']) || $_GET['projId'] == "") {
    die("Please use properly");
  } 

  $projId = $_GET['projId'];

  $studentList = getStudentList($projId);
  //print_r($studentList); 
  $csvInformation[0][0] = "Last Name";
  $csvInformation[0][1] = "First Name";
  $csvInformation[0][2] = "Average Score";
  $i = 1;
  foreach ( $studentList as $students ) {

    $marks = getUserMarks ($students['acct_id'], $projId);
    //print_r($marks);
    $numMarks = count($marks);
    if ($marks) {

        $totalMarks = 0;
        foreach ($marks as $m) {
            $totalMarks += $m['mark_score'];
        }

        $averageScore = $totalMarks / $numMarks;

        $csvInformation[$i][0] = $students['acct_lname']. "," .$students['acct_fname']; 
        $csvInformation[$i][1] = round($averageScore);

    } else {
        $csvInformation[$i][0] = $students['acct_lname']. "," .$students['acct_fname']; 
        $csvInformation[$i][1] = " ";
    }

    $groupMem = getGroupStudDetail ($students['group_id']);
    $x = 2;
    foreach ($groupMem as $g) {
        $indMarks = getIndMarks ($students['acct_id'], $projId, $g['acct_id']);
        //print_r($indMarks);
        if($indMarks) {
            $csvInformation[$i][$x] = "\"" . $g['acct_lname']. "," .$g['acct_fname']. " ==> " .$indMarks[0]['mark_score'] . "\"";
        } else {
            $csvInformation[$i][$x] = "\"" . $g['acct_lname']. "," .$g['acct_fname']. " ==> \"";
        }

        $x++;
    }
    
    $i++;
  } 
  //print_r($csvInformation);
  /*foreach ($csvInformation as $inner_arr) {
      foreach ($inner_arr as $value){
          echo $value. "     ";
      }
      echo "\n";
  }*/
  
  echo json_encode($csvInformation);
  
  
  
  
  
  
  
  //header("wcss.emmell.org/peerC/teacher.php");
  //csvReport ($csvInformation);
  
?> 
