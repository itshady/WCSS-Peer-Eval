<?php

    //gets all the details in account for a selected user
    function getUser ($userId) {
        global $pdo;

        $query = $pdo->prepare("SELECT * FROM accounts WHERE acct_id = :id");
        $query->bindParam(":id", $userId);
        $query->execute();

        $userResults = $query->fetchAll(PDO::FETCH_ASSOC);

        return $userResults[0];
    }

    //gets the user role
    function userType($userId) {
        $info = getUser($userId);

        if (empty($info)) return 0;

        return $info['acct_role']; 
    }

    //gets all the id's of the groups a person is in and where the project is enabled
    function findGroupID ($userId) {
        $info = getUser($userId);

        global $pdo;

        $query = $pdo->prepare("SELECT DISTINCT member_group_id FROM projects 
        JOIN `groups` ON proj_id = `groups`.group_proj_id
        JOIN members ON members.member_group_id = `groups`.`group_id`
        WHERE member_acct_id = :id");       //projects.proj_enabled = 1 AND
        $query->bindParam(":id", $info['acct_id']);
        $query->execute();

        $groupResults = $query->fetchAll(PDO::FETCH_ASSOC);

        return $groupResults;
    }

    //gets name of each group for group id
    function findGroupName ($memGroupId) {
        global $pdo;

        $query = $pdo->prepare("SELECT group_name FROM `groups` WHERE group_id = :gId");
        $query->bindParam(":gId", $memGroupId);
        $query->execute();

        $gNameResults = $query->fetchAll(PDO::FETCH_ASSOC);

        return $gNameResults[0];
    }

    //gets details of student: group name, project name, project points, project id, classcode, firstname, lastname, account role and account id
    function getGroupStudDetail ($memGroupId) {
        global $pdo;

        $query = $pdo->prepare("SELECT `group_name`, proj_name, proj_points, proj_enabled, proj_id, proj_classcode, acct_fname, acct_lname, acct_role, acct_id, proj_enabled FROM members 
                                        JOIN `groups` ON `groups`.`group_id`= members.member_group_id
                                        JOIN projects ON `groups`.`group_proj_id` = projects.proj_id
                                        JOIN accounts ON members.member_acct_id = accounts.acct_id
                                        WHERE `group_id` = :gId ");
        $query->bindParam(":gId", $memGroupId);
        $query->execute();

        $groupStudDetail = $query->fetchAll(PDO::FETCH_ASSOC);
        return $groupStudDetail;
    }

    //return the teacher fname, lname and acct_id for a project
    function getGroupTeachDetail ($projId) {
        global $pdo;

        $query = $pdo->prepare("SELECT acct_id, acct_fname, acct_lname FROM accounts 
                                JOIN projects ON projects.proj_acct_id = accounts.acct_id
                                WHERE proj_id = :Id");
        $query->bindParam(":Id", $projId);
        $query->execute();

        $groupTeachDetail = $query->fetchAll(PDO::FETCH_ASSOC);
        return $groupTeachDetail;
    }

    //gets group details but avoids one account id
    function getGroupDetail ($memGroupId, $accountId) {
        global $pdo;

        $query = $pdo->prepare("SELECT `group_name`, proj_name, proj_points, proj_id, proj_classcode, acct_fname, acct_lname, acct_role, acct_id FROM members 
                                        JOIN `groups` ON `groups`.`group_id`= members.member_group_id
                                        JOIN projects ON `groups`.`group_proj_id` = projects.proj_id
                                        JOIN accounts ON members.member_acct_id = accounts.acct_id
                                        WHERE `group_id` = :gId AND acct_id NOT IN (:aID) ");
        $query->bindParam(":gId", $memGroupId);
        $query->bindParam(":aID", $accountId);
        $query->execute();

        $groupStudDetail = $query->fetchAll(PDO::FETCH_ASSOC);
        return $groupStudDetail;
    }

    //enters user submissions from student peer evaluation and redirects back to main student page
    function enterPeerSub ($userResp, $groupStud, $accountId) {

        foreach ($groupStud as $stud) {
            global $pdo;

            $query = $pdo->prepare("INSERT INTO marks (mark_proj_id, mark_acct_id_marker, mark_acct_id_target, mark_score)
                                    VALUES (:proj_id, :userId, :targ, :score)");
            $query->bindParam(":proj_id", $userResp['proj_id']);
            $query->bindParam(":userId", $accountId);
            $query->bindParam(":targ", $stud['acct_id']);
            $query->bindParam(":score", $userResp[$stud['acct_id']] );
            $query->execute();

            header("location: https://wcss.emmell.org/peerC/student.php");
        }
        
    } 

    //gets marks for a user
    function getUserMarks ($studId, $projId) {
        global $pdo;

        $query = $pdo->prepare("SELECT mark_score FROM marks WHERE mark_acct_id_target = :acctId AND mark_proj_id = :projId ");
        $query->bindParam("acctId", $studId);
        $query->bindParam("projId", $projId);
        $query->execute(); 

        $marks = $query->fetchAll(PDO::FETCH_ASSOC); 
        
        return $marks;
    } 

    //gets individual amrks 
    function getIndMarks ($studId, $projId, $indId) {
        global $pdo;
        
        $query = $pdo->prepare("SELECT mark_score, mark_acct_id_target, mark_acct_id_marker FROM marks WHERE mark_acct_id_target = :acctId AND mark_proj_id = :projId AND mark_acct_id_marker = :indId");
        $query->bindParam("acctId", $studId);
        $query->bindParam("projId", $projId);
        $query->bindParam("indId", $indId);
        $query->execute(); 

        $marks = $query->fetchAll(PDO::FETCH_ASSOC); 
        
        return $marks;
    } 

    //creates csv data
    function csvReport ($csvInformation) {

        //print_r($csvInformation);
        $file = fopen("../studentReport.csv", "w+"); 

        if( $file == false ) {
            echo ( "Error in opening file" );
            exit();
        } else {
            foreach ($csvInformation as $info) {
                fputcsv($file, $info);
            }
        } 

        

        fclose($file); 

        /*
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=feedbackReport.csv');
        header('Pragma: no-cache');
        */ 
        
        // download file
        header('Content-type: text/csv');
        header('Content-disposition:attachment; filename="studentReport.csv"');
        readfile("../studentReport.csv");
    }

   

?>