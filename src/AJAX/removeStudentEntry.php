<?php
    require("../functions.php");

    if ($_GET['target'] == "marker") removeAllMarkerSubmissionFromProject($_GET['proj_id'],$_GET['stud_id']);
    else if ($_GET['target'] == "both") removeAllStudentMarksFromProject($_GET['proj_id'],$_GET['stud_id']);

    echo $_GET['target'];
?>