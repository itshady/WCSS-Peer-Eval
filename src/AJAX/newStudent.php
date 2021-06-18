<?php
    require("../functions.php");

    if (!empty($_GET['proj_id']) && !empty($_GET['stud_email'])) {
        //check if that username already exists in the db
        //if it does, check that acct_id is a student's account
        $acct_id = doesUserAlreadyExist($_GET['stud_email']);
        echo $acct_id;

        if ($acct_id && userType($acct_id) == "student" && isStudentAlreadyInProject($acct_id,$_GET['proj_id']) == 0) {                  //CHECK IF STUDENT IS ALREADY IN PROJECT
            //do smt if student already exists
            echo "I'm a student";
            updateAccountInfo($acct_id,$_GET['stud_fname'],$_GET['stud_lname'],$_GET['stud_email'],$_GET['stud_pass'],"student");
            addStudentToProject($acct_id,$_GET['proj_id']);
            
        }
        else if ($acct_id && userType($acct_id) != "student"){
            //do nothing because this means it's a teacher/admin
            echo "Im a teacher";
        }
        else {
            //do smt if student does not exist
            echo "I dont exist!";
            if (!empty($_GET['stud_fname']) && !empty($_GET['stud_lname']) && !empty($_GET['stud_email']) && !empty($_GET['stud_pass'])){
                $acct_id = createNewAccount($_GET['stud_fname'],$_GET['stud_lname'],$_GET['stud_email'],$_GET['stud_pass'],"student");
                addStudentToProject($acct_id,$_GET['proj_id']);
            }
        }
    }

?>