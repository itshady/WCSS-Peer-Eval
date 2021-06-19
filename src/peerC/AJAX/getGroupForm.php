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
<!--<div class="flex"> -->
  <!-- form to submit information about users values -->
<div class="flex">
  <div>
    <form action="AJAX/submitGroupInfo.php" method="POST" id="groupEdit">
      <?php
          echo "<h6> Please edit details for: </h6>";
          echo "<h1>" .$groupStud[0]['group_name']. "</h1><br><br>";
          echo "<center><p>Total Points = " .$numGroupMem * $groupStud[0]['proj_points']. "</p><center>";
          echo "<center><p id='availPoints' class='availPoints'>Available Points = " .$numGroupMem * $groupStud[0]['proj_points']. "</p>";
          echo "<input type='hidden' name='totalPoints' value=".$numGroupMem * $groupStud[0]['proj_points'].">";
          echo "<input type='hidden' name='proj_id' value=".$groupStud[0]['proj_id'].">";   //hidden input to get project id
          echo "<input type='hidden' name='group_id' value=".$groupId. ">";   //hidden input to get group id
          echo "<input type='hidden' name='numStud' id='numStud' value=".$numGroupMem." > </center>";   //hideen input to send number of students to js
          $studId = 0;
          foreach ($groupStud as $g) {
            echo "<label class='studName'>".$g['acct_fname']." " .$g['acct_lname']. "
                  </label>:<div class='rangeDiv d-flex flex-column'> <input type='range' id='studInput".$studId."' class='studInput form-range' step='1' value ='0' min='0' max=".$numGroupMem * $groupStud[0]['proj_points']." name=" .$g['acct_id']. ">
                  <input type='number' class='studInput boxStudInput form-control text-center' id='boxStudInput".$studId."' step='1' min='0' max=".$numGroupMem * $groupStud[0]['proj_points']." value='0'>
                  </div>";
            $studId++;
          }
      ?>
    </form>
  </div>

  <div class="chart">
    <figure class="highcharts-figure">
      <?php
          echo "<div id='container'></div>";
          echo "<center><h4  id='errorMessage'></h3></center>";
          //echo "<p class='highcharts-description'>Graph View:</p>";
      ?>
    </figure>
  </div>

</div>
