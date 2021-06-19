<?php
    require("../functions.php");

    $input = "%".$_GET['input']."%";

    $allLikeGroups = returnLikeGroups($_GET['proj_id'],$input);

    foreach($allLikeGroups as $group) {
        echo "<option value='".$group['group_name']."'>";
    }
?>