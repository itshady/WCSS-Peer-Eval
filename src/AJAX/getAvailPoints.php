<?php
    include("../functions.php");
    session_start();

  //auhtorizes user
  if (!authUser()) {
      echo "You are not authorized";
      die();
  }

  //validates user id is validate
  if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] == "") {
  die("Please use properly");
  }

  //user groupId to get student information
  $groupId = $_GET['id'];
  $groupStud = getGroupStudDetail ($groupId);

  //gets number of group members in each group
  $numGroupMem = count($groupStud);
  echo  $numGroupMem * $groupStud[0]['proj_points'] ;

/*
[0] => Array
        (
            [group_name] => Wakanda
            [proj_name] => HTML/CSS/JS Group Project
            [proj_points] => 90
            [proj_id] => 1
            [proj_classcode] => ICS4U
            [acct_fname] => Norman
            [acct_lname] => Bui
            [acct_role] => student
            [acct_id] => 6
        )
*/
?>  
