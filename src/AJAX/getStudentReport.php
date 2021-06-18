<?php 
    include("../functions.php");
    session_start();

    //auhtorizes user
    if (!authUser()) {
        echo "You are not authorized";
        die();
    }

    //validates user id is validate
    if (!isset($_GET['acctId']) || !is_numeric($_GET['acctId']) || $_GET['acctId'] == "") {
        die("Please use properly");
    }

    if (!isset($_GET['groupId']) || !is_numeric($_GET['groupId']) || $_GET['groupId'] == "") {
        die("Please use properly");
    }

    //student and group id's
    $studId = $_GET['acctId'];  
    $groupId = $_GET['groupId'];

    //get group names and users details
    $groupName = findGroupName($groupId);
    $userDetails = getUser($studId); 
    $groupMem = getGroupStudDetail ($groupId); 
    $marks = getUserMarks ($studId, $groupMem[0]['proj_id']);

    //gets number of group members & number of marks
    $numMem = count($groupMem);
    $numMarks = count($marks);
    $totalPoints = $numMem * $groupMem[0]['proj_points'];

    $totalMarks = 0;
    foreach ($marks as $m) {
        $totalMarks += $m['mark_score'];
    }

    

    echo "<center><h1>" .$userDetails['acct_fname']. "<br>" .$userDetails['acct_lname']. "</h1><hr></center>";
    //echo "<h4>" .$groupName['group_name']. "</h4>";

    echo "<br><br><h4>Average Score:</h4><br>";
    if ($marks) {
        $averageScore = $totalMarks / $numMarks;
        echo "<center><h1>" .round($averageScore). "</h1><hr>" .$totalPoints."</center>";
    } 
    else  echo "<center><h1> N/A </h1><hr>" .$totalPoints."</center>";
    
    echo "<br><br><h4>Score Breakdown:</h4><br>";
    foreach($groupMem as $g) {
        echo "<p><b>" .$g['acct_fname']. " " .$g['acct_lname']. "</b><br><center>";
        $indMarks = getIndMarks ($studId, $groupMem[0]['proj_id'], $g['acct_id']);
        if ($indMarks) {
            echo $indMarks[0]['mark_score'];
        } else {
            echo "N/A";
        }
        echo "</center></p>";
       
    } 
?>

