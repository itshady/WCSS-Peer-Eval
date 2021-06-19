<?php
    require("../functions.php");

    global $pdo;

    if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['group']) && !empty($_GET['proj_id'])) {
        //returns group_id where group = groupname in a certain project
        $query = $pdo->prepare("SELECT `group_id` FROM `groups` JOIN projects ON projects.proj_id = `groups`.`group_proj_id` WHERE `groups`.`group_name` = :groupname AND projects.proj_id = :proj_id");
        $query->bindParam(":groupname",$_GET['group']);
        $query->bindParam(":proj_id",$_GET['proj_id']);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        //return what member_id student was in for project
        $query = $pdo->prepare("SELECT member_id FROM members JOIN `groups` ON `groups`.group_id = members.member_group_id JOIN projects ON projects.proj_id = `groups`.`group_proj_id` WHERE member_acct_id = :id AND projects.proj_id = :proj_id");
        $query->bindParam(":id",$_GET['id']);
        $query->bindParam(":proj_id",$_GET['proj_id']);
        $query->execute();
        
        $results2 = $query->fetchAll(PDO::FETCH_ASSOC);
        //print_r($results2);
        $currentMemberID = $results2[0]['member_id'];
        //case 1: groupinput exists, but student not in it
        //case 2: groupinput doesnt exist, and student is not in it
        //in both cases, student start in empty group

        if (!empty($results)){
            //if the group that the teacher input for said student exists, we want to update that students group
            $retrievedGroupId = $results[0]['group_id'];
            updateCurrentGroup($retrievedGroupId,$currentMemberID);
        }
        else {
            //if group teacher input does not exist for that project, make it then put student in
            $newGroupID = createNewGroup($_GET['proj_id'],$_GET['group']);
            updateCurrentGroup($newGroupID,$currentMemberID);
        }
        echo $_GET['group']."<i class=\"p-1 fas fa-edit\"></i>";
    }
?>