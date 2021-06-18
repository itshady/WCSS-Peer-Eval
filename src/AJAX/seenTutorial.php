<?php
    require("../functions.php");
    session_start();

    setSeenTutorial($_SESSION['userID'],$_GET['newValue']);

?>