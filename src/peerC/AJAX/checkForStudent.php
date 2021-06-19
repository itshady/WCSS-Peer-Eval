<?php
    require("../functions.php");

    if (doesUserAlreadyExist($_GET['email'])) {
        $acct_id = doesUserAlreadyExist($_GET['email']);

        $userInfo = getUser($acct_id);

        echo $userInfo['acct_lname']."/".$userInfo['acct_fname'];
    }
    else echo "no";
?>