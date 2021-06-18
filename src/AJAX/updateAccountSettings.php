<?php
    require('../functions.php');
    
    if ($_GET['target'] == 'studentInfo') {
        //populate modal with form
        $userInfo = getUser($_GET['acct_id']);

        echo "<div class=\"form-floating mb-3 modal_fname_div\">\n";
        echo "    <input type=\"text\" class=\"form-control\" id=\"modal_fname\" name='modal_fname' placeholder=\"First Name\" value='". $userInfo['acct_fname'] ."'>\n";
        echo "    <label for=\"modal_fname\">First Name</label>\n";
        echo "    <div class=\"valid-feedback\">\n";
        echo "        Looks good!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"form-floating mb-3 modal_lname_div\">\n";
        echo "    <input type=\"text\" class=\"form-control\" id=\"modal_lname\" name='modal_lname' placeholder=\"Last Name\" value='". $userInfo['acct_lname'] ."'>\n";
        echo "    <label for=\"modal_lname\">Last Name</label>\n";
        echo "    <div class=\"valid-feedback\">\n";
        echo "        Looks good!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"input-group mb-3\">\n";
        echo "    <input type=\"text\" class=\"form-control\" placeholder=\"". explode("@",$userInfo['acct_user'])[0] ."\" aria-label=\"readonly Recipient's username\" aria-describedby=\"basic-addon2\" readonly>\n";
        echo "    <span class=\"input-group-text\" id=\"basic-addon2\">@ocdsb.ca</span>\n";
        echo "</div>\n";
        echo "<div class='d-flex flex-row justify-content-between'>";
        echo "<div class=\"form-floating mb-3 flex-password modal_newPass_div\">\n";
        echo "    <input type=\"password\" class=\"form-control\" id=\"modal_newPass\" name='modal_newPass' placeholder=\"New Password\">\n";
        echo "    <label for=\"modal_newPass\">New Password</label>\n";
        echo "    <div class=\"invalid-feedback\">\n";
        echo "        Must be 6 letters or longer!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"form-floating mb-3 flex-password modal_confirmPass_div\">\n";
        echo "    <input type=\"password\" class=\"form-control\" id=\"modal_confirmPass\" name='modal_confirmPass' placeholder=\"Confirm Password\">\n";
        echo "    <label for=\"modal_confirmPass\">Confirm Password</label>\n";
        echo "    <div class=\"invalid-feedback\">\n";
        echo "        Doesn't match!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div>";
        echo "<input hidden type='text' id='modal_acct_id' name='modal_acct_id' value='". $userInfo['acct_id'] ."'>\n";
        echo "<input hidden type='text' id='target' name='target' value='updateStudent'>\n";
    }
    if ($_GET['target'] == 'accountInfo') {
        //populate modal with form
        $userInfo = getUser($_GET['acct_id']);

        echo "<div class=\"form-floating mb-3 modal_fname_div\">\n";
        echo "    <input type=\"text\" class=\"form-control\" id=\"modal_fname\" name='modal_fname' placeholder=\"First Name\" value='". $userInfo['acct_fname'] ."'>\n";
        echo "    <label for=\"modal_fname\">First Name</label>\n";
        echo "    <div class=\"valid-feedback\">\n";
        echo "        Looks good!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"form-floating mb-3 modal_lname_div\">\n";
        echo "    <input type=\"text\" class=\"form-control\" id=\"modal_lname\" name='modal_lname' placeholder=\"Last Name\" value='". $userInfo['acct_lname'] ."'>\n";
        echo "    <label for=\"modal_lname\">Last Name</label>\n";
        echo "    <div class=\"valid-feedback\">\n";
        echo "        Looks good!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"input-group mb-3\">\n";
        echo "    <input type=\"text\" class=\"form-control\" placeholder=\"". explode("@",$userInfo['acct_user'])[0] ."\" aria-label=\"readonly Recipient's username\" aria-describedby=\"basic-addon2\" readonly>\n";
        echo "    <span class=\"input-group-text\" id=\"basic-addon2\">@ocdsb.ca</span>\n";
        echo "</div>\n";
        echo "<div class='d-flex flex-row justify-content-between'>";
        echo "<div class=\"form-floating mb-3 flex-password modal_newPass_div\">\n";
        echo "    <input type=\"password\" class=\"form-control\" id=\"modal_newPass\" name='modal_newPass' placeholder=\"New Password\">\n";
        echo "    <label for=\"modal_newPass\">New Password</label>\n";
        echo "    <div class=\"invalid-feedback\">\n";
        echo "        Must be 6 letters or longer!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "<div class=\"form-floating mb-3 flex-password modal_confirmPass_div\">\n";
        echo "    <input type=\"password\" class=\"form-control\" id=\"modal_confirmPass\" name='modal_confirmPass' placeholder=\"Confirm Password\">\n";
        echo "    <label for=\"modal_confirmPass\">Confirm Password</label>\n";
        echo "    <div class=\"invalid-feedback\">\n";
        echo "        Doesn't match!\n";
        echo "    </div>\n";
        echo "</div>\n";
        echo "</div>\n";
        echo "<div class=\"input-group mb-3\">\n";
        echo "<select class=\"form-select\" id='modal_role' name='modal_role' aria-label=\"Default select example\">\n";
        if (userType($_GET['acct_id']) == 'student') echo "<option value=\"Student\" selected>Student</option>\n";
        else echo "<option value=\"Student\">Student</option>\n";
        if (userType($_GET['acct_id']) == 'teacher') echo "<option value=\"Teacher\" selected>Teacher</option>\n";
        else echo "<option value=\"Teacher\">Teacher</option>\n";
        if (userType($_GET['acct_id']) == 'admin') echo "<option value=\"Admin\" selected>Admin</option>\n";
        else echo "<option value=\"Admin\">Admin</option>\n";
        echo "</select>\n";
        echo "</div>\n";
        if (userType($_GET['acct_id']) == 'student'){
            echo "<div class=\"input-group mb-3\">\n";
            echo "    <div class=\"alert alert-danger w-100\" role=\"alert\">";
            echo "      Changing the role will remove this student from all projects!";
            echo "    </div>";
            echo "</div>";
        }
        echo "<input hidden type='text' id='modal_acct_id' name='modal_acct_id' value='". $userInfo['acct_id'] ."'>\n";
        echo "<input hidden type='text' id='target' name='target' value='updateAccount'>\n";
    }



?>