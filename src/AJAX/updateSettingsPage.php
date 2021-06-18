<?php

    require("../functions.php");

    if ($_GET['target'] == 'studentSettings') {
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
            $projectList = getProjects($_GET['userID']);
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
    else if ($_GET['target'] == 'mySettings') {
        $acct_id = $_GET['userID'];
        $userInfo = getUser($acct_id);
        $defaultPoints = getUserSetting("defaultPoints",$acct_id);
        echo "<form method='post' action='' id='myTeacherForm' class='needs-validation'>\n";
        echo "<div class=\"form-floating mb-3 fname_div\">\n";
        echo "    <input type=\"text\" class=\"form-control\" id=\"fname\" name='fname' placeholder=\"First Name\" value='". $userInfo['acct_fname'] ."'>\n";
        echo "    <label for=\"fname\">First Name</label>\n";
        echo "    <div class=\"valid-feedback\">\n";
        echo "        Looks good!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"form-floating mb-3 lname_div\">\n";
        echo "    <input type=\"text\" class=\"form-control\" id=\"lname\" name='lname' placeholder=\"Last Name\" value='". $userInfo['acct_lname'] ."'>\n";
        echo "    <label for=\"lname\">Last Name</label>\n";
        echo "    <div class=\"valid-feedback\">\n";
        echo "        Looks good!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"input-group mb-3\">\n";
        echo "    <input type=\"text\" class=\"form-control\" placeholder=\"". explode("@",$userInfo['acct_user'])[0] ."\" aria-label=\"readonly Recipient's username\" aria-describedby=\"basic-addon2\" readonly>\n";
        echo "    <span class=\"input-group-text\" id=\"basic-addon2\">@ocdsb.ca</span>\n";
        echo "</div>\n";
        echo "<div class='d-flex flex-row justify-content-between mb-4'>";
        echo "<div class=\"form-floating mb-3 flex-password newPass_div\">\n";
        echo "    <input type=\"password\" class=\"form-control\" id=\"newPass\" name='newPass' placeholder=\"New Password\">\n";
        echo "    <label for=\"newPass\">New Password</label>\n";
        echo "    <div class=\"invalid-feedback\">\n";
        echo "        Must be 6 letters or longer!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"form-floating mb-3 flex-password confirmPass_div\">\n";
        echo "    <input type=\"password\" class=\"form-control\" id=\"confirmPass\" name='confirmPass' placeholder=\"Confirm Password\">\n";
        echo "    <label for=\"confirmPass\">Confirm Password</label>\n";
        echo "    <div class=\"invalid-feedback\">\n";
        echo "        Doesn't match!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "</div>";
        echo "<div class=\"form-floating mb-3 defaultpoints_div\">\n";
        echo "    <input type=\"number\" class=\"form-control\" id=\"defaultpoints\" name='defaultpoints' placeholder=\"Default Points\" value='". $defaultPoints ."'>\n";
        echo "    <label for=\"defaultpoints\">Default Points</label>\n";
        echo "    <div class=\"invalid-feedback\">\n";
        echo "        Must be a value over 0!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"modal-footer\" style='padding-left:0;'>\n";
        echo "    <button class=\"btn btn-primary me-auto ms-0 rewatchTutorial\" type=\"button\" style='width:40%;'>Rewatch Tutorial</button>\n";
        echo "    <button class=\"btn btn-primary me-md-2 w-25 resetButton\" type=\"button\">Reset</button>\n";
        echo "    <button class=\"btn btn-success w-25 teacherSubmit\" type=\"button\">Save</button>\n";
        echo "</div>\n";
        echo "<input hidden type='text' id='target' name='target' value='updateTeacher'>\n";
        echo "</form>\n";
    }
    else if ($_GET['target'] == 'accountSettings'){
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
            $listOfAllAccounts = getAllNotMe($_GET['userID']);
            
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

?>