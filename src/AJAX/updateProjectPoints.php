<?php
    require("../functions.php");

    if (!empty($_GET['proj_id'])) {
        updateProjectPoints($_GET['points'],$_GET['proj_id']);
        updateProjectInfo($_GET['proj_name'],$_GET['classcode'],$_GET['proj_id']);
        echo "Default Points: ". $_GET['points'] . "🌟". $_GET['classcode'] . "&nbsp-&nbsp" . $_GET['proj_name'];
    }
?>