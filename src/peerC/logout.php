<?php
    require("database.php");
    session_start();
    global $pdo;

    //empty acct_hash in db
    $query = $pdo->prepare("UPDATE accounts
    SET acct_hash = :acct_hash
    WHERE acct_id = :acct_id");
    
    $null = '';
    $query->bindParam(":acct_hash",$null);
    $query->bindParam(":acct_id",$_SESSION['user']);
    $query->execute();
    
    //end session
    session_destroy();

    //send back to login page
    header("Location: ./");
?>