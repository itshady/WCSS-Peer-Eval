<?php
    require("../functions.php");
    session_start();

    if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['toggle'])) {
        global $pdo;

        $query = $pdo->prepare("UPDATE projects
        SET proj_enabled = :toggle
        WHERE proj_id = :id AND proj_acct_id = :user");
        
        $query->bindParam(":toggle",$_GET['toggle']);
        $query->bindParam(":id",$_GET['id']);
        $query->bindParam(":user",$_SESSION['userID']);
        $query->execute();
    }
?>