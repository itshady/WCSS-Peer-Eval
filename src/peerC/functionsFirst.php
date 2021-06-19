<?php

    //hashes a string and returns it
    function hashPass($inputPass) {
        $hashedPass = password_hash($inputPass, PASSWORD_DEFAULT);
        
        return $hashedPass;
    }

    //returns 1 if server method is post
    function serverPOST() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') return 1;
    }

    //return 1 if the user is authenticated from login page and 0 if not
    function loginAuth($username,$password) {
        //return 0 if user or pass was empty
        if (empty($username) || empty($password)) return 0;
        
        //_POST was sent automatically
        global $pdo;

        //retrieve the password of given user from db
        $query = $pdo->prepare("SELECT acct_pass FROM accounts WHERE acct_user = :user");
        $query->bindParam(":user",$username);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        //if there is no user like that, then return 0
        if (empty($results)) return 0;

        /*Array (
            [0] => Array (
                [acct_pass] = testing(hashed)
            )
        )*/

        $retrievedPass = $results[0]['acct_pass'];

        //if the input pass from form == the db pass, then return 1 else 0
        if(password_verify($password,$retrievedPass)) {
            //set retrievedId to userID from db
            $query = $pdo->prepare("SELECT acct_id FROM accounts WHERE acct_user = :user");
            $query->bindParam(":user",$username);
            $query->execute();

            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            $retrievedId = $results[0]['acct_id'];
            
            // set session['hash'] to same as temporary acct_hash in db and session[user] to user id from db
            $_SESSION['userID'] = $retrievedId;
            $_SESSION['hash'] = bin2hex(random_bytes(20));
            
            $query = $pdo->prepare("UPDATE accounts
            SET acct_hash = :acct_hash
            WHERE acct_user = :acct_user");
            
            $query->bindParam(":acct_hash",$_SESSION['hash']);
            $query->bindParam(":acct_user",$username);
            $query->execute();

            return 1;
        }
        return 0;
    }

    //if session hash is same as db hash, then return 1, else 0
    function authUser() {
        global $pdo;
        
        //if session variables are empty return 0
        if (empty($_SESSION['userID']) || empty($_SESSION['hash'])) return 0;

        //retrieve acct_hash from user db
        $query = $pdo->prepare("SELECT acct_hash FROM accounts WHERE acct_id = :id");
        $query->bindParam(":id",$_SESSION['userID']);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        //if there is no user like that, then return 0
        if (empty($results)) return 0;

        $retrievedHash = $results[0]['acct_hash'];

        if ($_SESSION['hash'] == $retrievedHash) return 1;
        return 0;
    }

    //create a new evaluation manually, returns its proj_id
    function createNewEval($class,$proj,$teacherID,$points) {
        global $pdo;

        //insert into projects table
        $query = $pdo->prepare("INSERT INTO projects (proj_acct_id, proj_name, proj_points, proj_classcode, proj_enabled)
        VALUES (:id, :proj_name, :points, :classcode, 1)");
        $query->bindParam(":id",$teacherID);
        $query->bindParam(":proj_name",$proj);
        $query->bindParam(":classcode",$class);
        $query->bindParam(":points",$points);

        $query->execute();

        //retrieve most recent entry
        $query = $pdo->prepare("SELECT proj_id FROM projects");
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        sort($results);
        $retrievedProjID = end($results)['proj_id'];

        //somehow get the ID of that project
        createNewGroup($retrievedProjID,"");
        return $retrievedProjID;
    }

    //create new group and return its group_id
    function createNewGroup($projID,$groupName) {
        global $pdo;
        
        //create a new group
        $query = $pdo->prepare("INSERT INTO `groups` (`group_name`,`group_proj_id`)
        VALUES (:group_name, :proj_id)");
        $query->bindParam(":group_name",$groupName);
        $query->bindParam(":proj_id",$projID);

        $query->execute();

        //return the new group_id
        $query = $pdo->prepare("SELECT `group_id` FROM `groups`");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return end($results)['group_id'];
    }

    //check if group exists, if so then return group_id
    function doesGroupExist($proj_id,$groupname) {
        global $pdo;
        
        //checks if there is a group with that name in the project
        $query = $pdo->prepare("SELECT `group_id` FROM `groups` WHERE `group_proj_id` = :proj_id AND `group_name` = :groupname");
        $query->bindParam(":proj_id",$proj_id);
        $query->bindParam(":groupname",$groupname);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) return $results[0]['group_id'];
        return 0;
    }

    //get the setting value for specific settings and users
    function getUserSetting($settingName,$userID) {
        global $pdo;

        //get setting value from db for specfic setting and user
        $query = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_name = :setting AND setting_acct_id = :id");
        $query->bindParam(":setting",$settingName);
        $query->bindParam(":id",$userID);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        //if there is no user like that, then return 0
        if (empty($results)) return null;

        $retrievedValue = $results[0]['setting_value'];
        return $retrievedValue;
    }

    //updates the current users default points setting
    function updateUserSetting($settingName,$newValue,$userID) {
        global $pdo;

        $query = $pdo->prepare("UPDATE settings
        SET setting_value = :setting_value
        WHERE setting_name = :setting_name AND setting_acct_id = :id");
        $query->bindParam(":setting_value",$newValue);
        $query->bindParam(":setting_name",$settingName);
        $query->bindParam(":id",$userID);
        $query->execute();
    }

    //get all projects from user and return array
    function getProjects($userID) {
        global $pdo;

        $query = $pdo->prepare("SELECT proj_id, proj_name, proj_classcode, proj_points, proj_enabled FROM projects WHERE proj_acct_id = :id");
        $query->bindParam(":id",$userID);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        //if there is no user like that, then return 0
        if (empty($results)) return array();
        
        return $results;
    }

    //return list of students and groups etc for an eval
    function getStudentList($projID) {
        //ask mr emmell to do  the massive sequel command
        global $pdo;

        $query = $pdo->prepare("SELECT accounts.acct_id, accounts.acct_fname, accounts.acct_lname, accounts.acct_user, `groups`.`group_name`, `groups`.`group_id` FROM accounts
        JOIN members ON members.member_acct_id = accounts.acct_id
        JOIN `groups` ON `groups`.`group_id`= members.member_group_id
        JOIN projects ON projects.proj_id = `groups`.`group_proj_id`
        WHERE projects.proj_id = :proj_id");
        $query->bindParam(":proj_id",$projID);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        //print_r($results);

        //if there is no user like that, then return 0
        if (empty($results)) return array();
        
        return $results;
    }

    //update a students group in a certain project
    function updateCurrentGroup($newGroupID,$memberID) {
        global $pdo;

        $query = $pdo->prepare("UPDATE members
        SET member_group_id = :groupID
        WHERE member_id = :id");
        
        $query->bindParam(":groupID",$newGroupID);
        $query->bindParam(":id",$memberID);
        $query->execute();
    }

    //return the first and last name of the current user as one string ("Hady Ibrahim")
    function getCurrentUserName() {
        global $pdo;

        $query = $pdo->prepare("SELECT acct_fname, acct_lname FROM accounts WHERE acct_id = :id");
        $query->bindParam(":id",$_SESSION['userID']);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return $results[0]['acct_fname']." ".$results[0]['acct_lname'];
    }

    //updates the project's default points
    function updateProjectPoints($points,$proj_id){
        global $pdo;
        
        $query = $pdo->prepare("UPDATE projects
        SET proj_points = :points
        WHERE proj_id = :proj_id");
        
        $query->bindParam(":points",$points);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
    }

    //update project name and course code
    function updateProjectInfo($proj_name,$classcode,$proj_id) {
        global $pdo;
        
        $query = $pdo->prepare("UPDATE projects
        SET proj_name = :proj_name,
        proj_classcode = :classcode
        WHERE proj_id = :proj_id");
        $query->bindParam(":proj_name",$proj_name);
        $query->bindParam(":classcode",$classcode);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
    }

    //returns acct_id if user email is already in database, else returns 0
    function doesUserAlreadyExist($acct_user) {
        global $pdo;
        
        //checks if there is a user with that account
        $query = $pdo->prepare("SELECT acct_id FROM accounts WHERE acct_user = :acct_user");
        $query->bindParam(":acct_user",$acct_user);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) return $results[0]['acct_id'];
        return 0;
    }

    //updates an existing accounts's info
    //send empty parameters if you dont want that updated
    function updateAccountInfo($acct_id,$acct_fname,$acct_lname,$acct_user,$acct_pass,$acct_role) {
        global $pdo;
        
        //update fname
        if (!empty($acct_fname)) {
            $query = $pdo->prepare("UPDATE accounts
            SET acct_fname = :acct_fname
            WHERE acct_id = :acct_id");
            $query->bindParam(":acct_fname",$acct_fname);
            $query->bindParam(":acct_id",$acct_id);
            $query->execute();
        }
        //update lname
        if (!empty($acct_lname)) {
            $query = $pdo->prepare("UPDATE accounts
            SET acct_lname = :acct_lname
            WHERE acct_id = :acct_id");
            $query->bindParam(":acct_lname",$acct_lname);
            $query->bindParam(":acct_id",$acct_id);
            $query->execute();
        }
        //update email
        if (!empty($acct_user)) {
            $query = $pdo->prepare("UPDATE accounts
            SET acct_user = :acct_user
            WHERE acct_id = :acct_id");
            $query->bindParam(":acct_user",$acct_user);
            $query->bindParam(":acct_id",$acct_id);
            $query->execute();
        }
        //update pass
        if (!empty($acct_pass)) {
            $query = $pdo->prepare("UPDATE accounts
            SET acct_pass = :acct_pass
            WHERE acct_id = :acct_id");
            $newPass = hashPass($acct_pass);
            $query->bindParam(":acct_pass",$newPass);
            $query->bindParam(":acct_id",$acct_id);
            $query->execute();
        }
        //update account role
        if (!empty($acct_role)) {
            $query = $pdo->prepare("UPDATE accounts
            SET acct_role = :acct_role
            WHERE acct_id = :acct_id");
            $query->bindParam(":acct_role",$acct_role);
            $query->bindParam(":acct_id",$acct_id);
            $query->execute();
        }
    }

    //add a student to project's empty group if it exists, else create it and add them and return member_id
    function addStudentToProject($acct_id,$proj_id) {
        global $pdo;
        
        //check if empty group is in project and retrieve group_id of it
        $query = $pdo->prepare("SELECT `group_id` FROM `groups` WHERE `group_proj_id` = :proj_id AND `group_name` = ''");
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        //if no empty group exists for that project make one
        if (empty($results)) {
            createNewGroup($proj_id,"");
            //retrieve most recent group_id
            $query = $pdo->prepare("SELECT `group_id` FROM `groups`");
            $query->execute();
            
            $results2 = $query->fetchAll(PDO::FETCH_ASSOC);
            sort($results2);
            $group_id = end($results2)['group_id'];
        }
        else $group_id = $results[0]['group_id']; //else set group_id to found group_id earlier 

        //insert student into project's empty group
        $query = $pdo->prepare("INSERT INTO members (member_acct_id,member_group_id)
        VALUES (:acct_id, :group_id)");
        $query->bindParam(":acct_id",$acct_id);
        $query->bindParam(":group_id",$group_id);

        $query->execute();

        //retrieve most recent member_id
        $query = $pdo->prepare("SELECT member_id FROM members");
        $query->execute();
        
        $results3 = $query->fetchAll(PDO::FETCH_ASSOC);
        sort($results3);
        $member_id = end($results3)['member_id'];

        return $member_id;
    }

    //creates new account with given info, returns new acct_id 
    function createNewAccount($acct_fname,$acct_lname,$acct_user,$acct_pass,$acct_role) {
        global $pdo;

        //if any of the params are empty, return 0
        if (empty($acct_fname) || empty($acct_lname) || empty($acct_user) || empty($acct_pass) || empty($acct_role)) return 0;

        //insert account into accounts table
        $query = $pdo->prepare("INSERT INTO accounts (acct_role,acct_user,acct_pass,acct_fname,acct_lname,acct_hash)
        VALUES (:acct_role, :acct_user, :acct_pass, :acct_fname, :acct_lname,'')");
        $newPass = hashPass($acct_pass);
        $query->bindParam(":acct_role",$acct_role);
        $query->bindParam(":acct_user",$acct_user);
        $query->bindParam(":acct_pass",$newPass);
        $query->bindParam(":acct_fname",$acct_fname);
        $query->bindParam(":acct_lname",$acct_lname);

        $query->execute();

        //retrieve most recent acct_id
        $query = $pdo->prepare("SELECT acct_id FROM accounts");
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        sort($results);

        return end($results)['acct_id'];
    }

    //checks if student is already in a group in that project
    function isStudentAlreadyInProject($acct_id,$proj_id) {
        global $pdo;

        $query = $pdo->prepare("SELECT member_acct_id FROM members JOIN `groups` ON `groups`.`group_id` = members.member_group_id WHERE `group_proj_id` = :proj_id AND member_acct_id = :acct_id");
        $query->bindParam(":acct_id",$acct_id);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) return 0;//0
        return 1;//1
    }

    //remove project and all attached to it from db
    function removeProject($proj_id) {
        global $pdo;

        $query = $pdo->prepare("DELETE FROM projects WHERE proj_id = :proj_id");
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
    }

    //removes all marks from a student in a project that THEY submitted (marker)
    function removeAllMarkerSubmissionFromProject($proj_id,$stud_id){
        global $pdo;
        
        //delete from marks
        $query = $pdo->prepare("DELETE FROM marks
        WHERE mark_acct_id_marker = :stud_id AND mark_proj_id = :proj_id");
        $query->bindParam(":stud_id",$stud_id);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
    }

    //removes all the marks associated to a student from a project (both target and marker)
    function removeAllStudentMarksFromProject($proj_id,$stud_id) {
        global $pdo;
        
        //delete from marks
        $query = $pdo->prepare("DELETE FROM marks
        WHERE (mark_acct_id_marker = :stud_id OR mark_acct_id_target = :stud_id2) AND mark_proj_id = :proj_id");
        $query->bindParam(":stud_id",$stud_id);
        $query->bindParam(":stud_id2",$stud_id);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
    }

    //remove student from project and all marks attached to it
    function removeStudentFromProject($proj_id,$stud_id) {
        global $pdo;

        //delete from members table
        $query = $pdo->prepare("DELETE members FROM members 
        JOIN `groups` ON `groups`.`group_id` = members.member_group_id
        JOIN projects ON projects.proj_id = `groups`.`group_proj_id`
        WHERE member_acct_id = :stud_id AND proj_id = :proj_id");
        $query->bindParam(":stud_id",$stud_id);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();

        removeAllStudentMarksFromProject($proj_id,$stud_id);
    }

    //return 1 if student submitted scores for certain project, else 0
    function isMarkSubmitted($proj_id,$stud_id) {
        global $pdo;

        $query = $pdo->prepare("SELECT mark_id FROM marks WHERE mark_acct_id_marker = :acct_id AND mark_proj_id = :proj_id");
        $query->bindParam(":acct_id",$stud_id);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) return 0;
        return 1; 
        
    }

    //go through 2D array and return array of only unique inputs for specific field
    function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();
    
        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    //Get all accounts except user 
    function getAllNotMe($userID) {
        global $pdo;

        $query = $pdo->prepare("SELECT acct_id, acct_role, acct_user, acct_fname, acct_lname FROM accounts WHERE acct_id NOT IN (:id)");
        $query->bindParam(":id",$userID);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    //get all projects a student is in
    function getAllStudentsProjects($acct_id) {
        global $pdo;

        $query = $pdo->prepare("SELECT proj_id FROM projects 
        JOIN `groups` ON `groups`.`group_proj_id` = projects.proj_id
        JOIN members ON members.member_group_id = `groups`.`group_id` 
        WHERE member_acct_id = :id");
        $query->bindParam(":id",$acct_id);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($results)) return array();
        return $results;
    }

    function returnLikeGroups($proj_id,$groupname) {
        global $pdo;

        $query = $pdo->prepare("SELECT `group_name` FROM `groups`
        WHERE `group_name` LIKE :groupname AND `group_proj_id` = :proj_id");
        $query->bindParam(":groupname",$groupname);
        $query->bindParam(":proj_id",$proj_id);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($results)) return array();
        return $results;
    }

    //return 1 if they have seen tutorial, else return 0 (returns wtv is in db)
    function hasSeenTutorial($acct_id) {
        global $pdo;

        $query = $pdo->prepare("SELECT acct_seenTutorial FROM accounts
        WHERE acct_id = :acct_id");
        $query->bindParam(":acct_id",$acct_id);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $results[0]["acct_seenTutorial"];
    }

    function setSeenTutorial($acct_id,$seenTutorial) {
        global $pdo;
        
        $query = $pdo->prepare("UPDATE accounts
        SET acct_seenTutorial = :seenTutorial
        WHERE acct_id = :acct_id");
        
        $query->bindParam(":seenTutorial",$seenTutorial);
        $query->bindParam(":acct_id",$acct_id);
        $query->execute();
    }

?>