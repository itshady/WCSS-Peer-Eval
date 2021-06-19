<?php
    require("../functions.php");

    if ($_GET['target'] == "rmvProject") {
        $proj_id = $_GET['proj_id'];
        //remove project
        removeProject($proj_id);
    }
    else if ($_GET['target'] == "rmvStudent") {
        $proj_id = $_GET['proj_id'];
        $stud_id = $_GET['stud_id'];
        echo $proj_id."\n\n".$stud_id;
        //remove student
        removeStudentFromProject($proj_id,$stud_id);
    }
?>