<?php 
    include("../functions.php");

    session_start();

    //auhtorizes user
    if (!authUser()) {
        echo "You are not authorized";
        die();
    }
    
    //puts input information from from into userResp and gets all student details
    $userResp = $_POST;
    $groupStud = getGroupStudDetail ($userResp['group_id'], $_SESSION['userID']);

    print_r($_POST);
    print_r($_SESSION);

/*
    Array
    (
        [proj_id] => 1
        [6] => 1
        [7] => 2
        [8] => 3
    )
    Array
    (
        [userID] => 8
        [hash] => 87e5c9f239b59c55a31d317d3cda2b0234fc03cb
    )
*/
    //enters information into peer submissions
    enterPeerSub ($userResp, $groupStud, $_SESSION['userID']);
?>