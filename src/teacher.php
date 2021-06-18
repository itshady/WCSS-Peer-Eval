<?php
    require("functions.php");
    session_start();
    error_reporting(0);
    if (!authUser()) {
      echo "You are not authorized!";
      die();
    }

    if (userType($_SESSION['userID']) == "student") {
      echo "Students are not authorized here!";
      die();
    }
    $scroll = 0;

    //check if the user has seen the tutorial
    $seenTutorial = hasSeenTutorial($_SESSION['userID']);


    //check if file was uploaded
    if (serverPOST()) {
      $wasFileUploaded = 1;
      if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) $wasFileUploaded = 0;
    }

    if(serverPOST() && !empty($_POST['classcode']) && !empty($_POST['proj-name']) && $wasFileUploaded == 0) {
      createNewEval($_POST['classcode'], $_POST['proj-name'], $_SESSION['userID'],$_POST['proj-points']);
    }
    else if (serverPOST() && !empty($_POST['classcode']) && !empty($_POST['proj-name']) && $wasFileUploaded == 1){
      $tmpName = $_FILES['file']['tmp_name'];
      //get file extension
      $FileType = strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
      //check if its a csv
      if ($FileType == 'csv') {
        //map csv as an array of arrays
        $csvAsArray = array_map('str_getcsv', file($tmpName));
      }
      $newProjID = createNewEval($_POST['classcode'], $_POST['proj-name'], $_SESSION['userID'],$_POST['proj-points']);
      
      foreach ($csvAsArray as $stud){
        $stud_email = $stud[0];
        $stud_pass = $stud[1];
        $stud_fname = $stud[2];
        $stud_lname = $stud[3];
        $stud_group = $stud[4];

        //check if that username already exists in the db
        //if it does, check that acct_id is a student's account
        $stud_acct_id = doesUserAlreadyExist($stud_email);
        
        if ($stud_acct_id && userType($stud_acct_id) == "student" && isStudentAlreadyInProject($stud_acct_id,$newProjID) == 0) {                  //CHECK IF STUDENT IS ALREADY IN PROJECT
            //do smt if student already exists
            
            updateAccountInfo($stud_acct_id,$stud_fname,$stud_lname,$stud_email,$stud_pass,"student");
            $stud_member_id = addStudentToProject($stud_acct_id,$newProjID);
            
            //check if group exists, if not make it and add them
            $newGroupID = doesGroupExist($newProjID,$stud_group);
            if ($newGroupID == 0){
              $newGroupID = createNewGroup($newProjID,$stud_group);
            }
            //add student to group
            updateCurrentGroup($newGroupID,$stud_member_id);
        }
        else if ($stud_acct_id && userType($stud_acct_id) != "student"){
            //do nothing because this means it's a teacher/admin
            
        }
        else {
            //do smt if student does not exist
            
            if (!empty($stud_fname) && !empty($stud_lname) && !empty($stud_email) && !empty($stud_pass)){
              $stud_acct_id = createNewAccount($stud_fname,$stud_lname,$stud_email,$stud_pass,"student");
              $stud_member_id = addStudentToProject($stud_acct_id,$newProjID);
              
              //check if group exists, if not make it and add them
              $newGroupID = doesGroupExist($newProjID,$stud_group);
              if ($newGroupID == 0){
                $newGroupID = createNewGroup($newProjID,$stud_group);
              }
              //add student to group
              updateCurrentGroup($newGroupID,$stud_member_id);
            }
        }
      } 
    }
    else if (serverPOST() && !empty($_POST['studModal_proj_id']) && !empty($_POST['stud_email'])) {
        $openProjAccordion = $_POST['studModal_proj_id'];
        $scroll = $_POST['scroll'];
        //check if that username already exists in the db
        //if it does, check that acct_id is a student's account
        $acct_id = doesUserAlreadyExist($_POST['stud_email']);

        if ($acct_id && userType($acct_id) == "student" && isStudentAlreadyInProject($acct_id,$_POST['studModal_proj_id']) == 0) {                  //CHECK IF STUDENT IS ALREADY IN PROJECT
            //do smt if student already exists
            updateAccountInfo($acct_id,$_POST['stud_fname'],$_POST['stud_lname'],$_POST['stud_email'],$_POST['stud_pass'],"student");
            addStudentToProject($acct_id,$_POST['studModal_proj_id']);
            
        }
        else if ($acct_id && userType($acct_id) != "student"){
            //do nothing because this means it's a teacher/admin
        }
        else {
            //do smt if student does not exist
            if (!empty($_POST['stud_fname']) && !empty($_POST['stud_lname']) && !empty($_POST['stud_email']) && !empty($_POST['stud_pass'])){
                $acct_id = createNewAccount($_POST['stud_fname'],$_POST['stud_lname'],$_POST['stud_email'],$_POST['stud_pass'],"student");
                addStudentToProject($acct_id,$_POST['studModal_proj_id']);
            }
        }
    }

?>

<!DOCTYPE html>
<!-- Developed by Hady Ibrahim and Shushawn Saha -->
<html lang="en" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Evaluation | <?php echo getCurrentUserName() ?> | Peer Evalutaor</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"> <!-- Data Table cs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link href="css/teacher.css" rel="stylesheet">
    <link href="css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

	<link rel="icon" href="favicon.png">

  </head>

  <body>

    <!-- New Eval Button-->
    <button type="button" class="newEvalButton invisible" data-bs-toggle="modal" data-bs-target="#exampleModal">
    <div class="wrapper visible">
      <div class="pulse"> <i class="fa fa-plus"></i> </div>
    </div>
    </button>

    <!-- NAVBAR -->
    <div class="position-fixed top-0 start-0 bg-primary myNavbar">
      <div class="tab" id="tab1" onclick="location.href = 'teacher.php';"></div>
      <div class="tab" id="tab2" onclick="location.href = 'settings';"></div>
      <div class="tab" id="tab3" onclick="location.href = 'logout.php';"></div>
    </div>

    <div class="position-fixed top-0 start-0 bg-primary myNavbar" id="testing">
      <div class="tab-text rotate" id="header1" onclick="location.href = 'teacher.php';">Evaluations</div>
      <div class="tab-text rotate" id="header2" onclick="location.href = 'settings';">Settings</div>
      <div class="tab-text rotate" id="header3" onclick="location.href = 'logout.php';">Logout</div>
    </div>

    <!-- HEADER -->
    <div class="position-absolute top-0 p-1 myHeader">
      <div class="position-absolute top-0 align-items-center d-flex">
        <div>Welcome👋</div>
        <div class="welcome-name mx-1"><?php echo getCurrentUserName() ?>ツ</div>
      </div>
    </div>

    <!--<br><br><br><br><center><h1>Evaluations</h1><hr></center> -->

    <!-- Offcanvas Student Report -->
    <!--<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Toggle right offcanvas</button>-->

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
      <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Student Report</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body" id="offcanvas-body">
        ...
      </div>
    </div>

    

    <!-- Tutorial Modal -->
    <div class="modal fade" id="tutorialModal" tabindex="-1" aria-labelledby="tutorialModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title align-self-center" id="tutorialModalLabel">Tutorial</h5>
          </div>
          <div class="modal-body">
            <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
              <!--<div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2" class="secondLastButton"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
              </div>-->
              <div class="carousel-inner">
                <div class="carousel-item active">
                  
                
                </div>
                <div class="carousel-item">
                  
                  <div class="carousel-caption d-none d-md-block">
                    <h5>Creating an Evaluation</h5>
                    <p>Click the red button in the top right and add project details. You may also add a csv file with all student information.</p>
                  </div>
                </div>
                <div class="carousel-item">
                  
                  <div class="carousel-caption d-none d-md-block">
                    <h5>Changing Project Detail</h5>
                    <p>Click on the icon beside the project name to change the project details. Use to toggle to activate the project.</p>
                  </div>
                </div>
                <div class="carousel-item">
                  
                  <div class="carousel-caption d-none d-md-block">
                    <h5>Changing Group Detail</h5>
                    <p>Within the table you can: change group names, add new students, check submission and print a CSV. There is also many more functionality!</p>
                  </div>
                </div>
                <div class="carousel-item finalSlide">
                  
                  <div class="carousel-caption d-none d-md-block">
                    <h5>Settings</h5>
                    <p>Here you can change the details of your students and you. You can also rewatch the tutorial here 😊</p>
                  </div>
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id='tutorialDone' style='display:none;'>Done</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal are you sure? -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
          <div class="modal-body">
              <div class="mb-3 areYouSure">
                Are you sure?
              </div>
          </div>
          <input hidden type="text" id="confirmModal_proj_id" name="confirmModal_proj_id" value="">
          <input hidden type="text" id="confirmModal_stud_id" name="confirmModal_stud_id" value="">
          <input hidden type="text" id="confirmTarget" name="confirmTarget" value="">
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="confirmModalDelete">Delete</button>
          </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal CreateNewEval -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create New Evaluation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post" enctype="multipart/form-data">
          <div class="modal-body">
              <div class="mb-3">
                <label for="classcode" class="col-form-label">Class Code:</label>
                <input type="text" class="form-control" id="classcode" name="classcode">
              </div>
              <div class="mb-3">
                <label for="proj-name" class="col-form-label">Project Name:</label>
                <input type="text" class="form-control" id="proj-name" name="proj-name">
              </div>
              <div class="mb-3">
                <label for="proj-points" class="col-form-label">Points:</label>
                <input type="number" min=0 class="form-control" id="proj-points" name="proj-points">
              </div>
              <div class="mb-3">
                <label for="file" class="form-label">CSV: (email, pass, firstname, lastname, groupname)</label>
                <input class="form-control" type="file" id="file" name="file">
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Rename Group -->
    <div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Change Group</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
          <div class="modal-body">
              <div class="mb-3">
                <label for="groupname" class="col-form-label">Group Name: </label>
                <input type="text" class="form-control group_proj_id12321" list="datalistOptions" id="groupname" name="groupname" value="">
                <datalist id="datalistOptions">
                  
                </datalist>
              </div>
              <div class="alert alert-danger" role="alert">
                Group Name is Case Sensitive!
              </div>
              <div class="alert alert-danger" role="alert">
                Change in group name might affect previous submissions' total points.
              </div>
              <input hidden type="text" id="groupModal_acct_id" name="groupModal_acct_id" value="">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="groupModalSave">Save</button>
          </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Set Default Points -->
    <div class="modal fade" id="pointsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Project</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
          <div class="modal-body">
              <div class="mb-3">
                <label for="points_classcode" class="col-form-label">Course Code:</label>
                <input type="text" class="form-control" id="points_classcode" name="points_classcode">
              </div>
              <div class="mb-3">
                <label for="points_proj_name" class="col-form-label">Project Name:</label>
                <input type="text" class="form-control" id="points_proj_name" name="points_proj_name">
              </div>
              <div class="mb-3">
                <label for="points" class="col-form-label">Points:</label>
                <input type="number" class="form-control" id="points" name="points">
              </div>
              <input hidden type="text" id="pointsModal_proj_id" name="pointsModal_proj_id" value="">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="pointsModalSave">Save</button>
          </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Add New Student -->
    <div class="modal fade" id="studModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Student</h5>    <!--ADD HERE THAT IF STUDENT EXISTS YOU CAN ONLY PUT EMAIL-->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
          <div class="modal-body">
              <div class="mb-3">
                <label for="stud_fname" class="col-form-label">First Name:</label>
                <input type="text" class="form-control" id="stud_fname" name="stud_fname">
              </div>
              <div class="mb-3">
                <label for="stud_lname" class="col-form-label">Last Name:</label>
                <input type="text" class="form-control" id="stud_lname" name="stud_lname">
              </div>
              <div class="mb-3">
                <label for="stud_email" class="col-form-label">Email(username):</label>
                <input type="text" class="form-control" id="stud_email" name="stud_email" required>
              </div>
              <div class="mb-3">
                <label for="stud_pass" class="col-form-label">Password:</label>
                <input type="password" class="form-control" id="stud_pass" name="stud_pass">
              </div>
              <input hidden type="text" id="scroll" name="scroll" value="0">
              <input hidden type="text" id="studModal_proj_id" name="studModal_proj_id" value="">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="studModalSave">Save</button>
          </div>
          </form>
        </div>
      </div>
    </div>



    

    <!-- Eval List Accordion -->
  <div class="wrap-all d-flex flex-column">
    
<?php
  //retrieve all proj_id, proj_name, proj_classcode, proj_points, proj_enabled in array of EVERY project
  $myProjects = getProjects($_SESSION['userID']);

  if ($myProjects) {
    //sort myProjects array based on classcodes
    $sortedProjects = array();
    foreach ($myProjects as $arr){ 
      array_push($sortedProjects, $arr['proj_classcode']);
    }
    array_multisort($sortedProjects,$myProjects);

    //print each accordion with the projects
    echo "<div class=\"accordion\" id=\"accordionExample\">";
    foreach($myProjects as $proj) {
      $studentList = getStudentList($proj['proj_id']);


      //======================= print project info in header ====================================
      echo "<div class=\"accordion-item\">\n";
        echo "<h2 class=\"accordion-header\" id=\"heading". $proj['proj_id'] ."\">\n";
          echo "<button class=\"accordion-button accordion-wrapper". $proj['proj_id'];
          if ($openProjAccordion != $proj['proj_id']) echo " collapsed";
          echo "\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapse" . $proj['proj_id'] . "\" 
          aria-expanded=\"true\" aria-controls=\"collapse". $proj['proj_id'] ."\">\n";
            echo "<div class='d-flex flex-row accordion-flex'>";
            echo "<div class='d-flex flex-column'><div class='m-1 fw-bold accordion-classcode' id='accordion-classcode" . $proj['proj_id'] . 
            "'>" . $proj['proj_classcode'] . "&nbsp-&nbsp" . $proj['proj_name'] . 
            "</div><div class='m-1 defaultPoints' id='defaultPoints". $proj['proj_id']  ."'>Default Points: " . $proj['proj_points'] . "</div></div>";
            echo "<div class=\"p-1 align-self-start accordion-inner-flex\" id='accordion-inner-flex" . $proj['proj_id'] . "' data-bs-toggle=\"modal\" 
            data-bs-target=\"#pointsModal\"><i class=\"fas fa-edit\"></i></div>";
            echo "<div class=\"form-check form-switch m-1 ms-auto align-self-center\">";
              echo "<input class=\"form-check-input\" type=\"checkbox\" id=\"";
              if ($proj['proj_enabled']) echo "flexSwitchCheckChecked." . $proj['proj_id'] . "\" checked data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Toggle Project\">";
              else echo "flexSwitchCheckDefault." . $proj['proj_id'] . "\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Toggle Project\">";
            echo "</div>";
            echo "</div>\n";
          echo "</button>\n";
        echo "</h2>\n";
        echo "<div id=\"collapse". $proj['proj_id'] ."\" class=\"accordion-collapse collapse";
        if ($openProjAccordion == $proj['proj_id']) echo " show"; 
        echo "\" aria-labelledby=\"heading". $proj['proj_id'] ."\" data-bs-parent=\"#accordionExample\">\n";
          echo "<div class=\"accordion-body accordion-body".$proj['proj_id']."\">\n";
              //======================= print student info in table of body ====================================
              echo "<table class=\"table_id stripe\" id='table_id' class=\"display\">\n";
                echo "<thead>\n
                    <tr>\n
                        <th>Last Name</th>\n
                        <th>First Name</th>\n
                        <th>Group Name</th>\n
                        <th class='text-center'>Submitted?</th>\n
                        <th class='text-center'>Delete Submission</th>\n
                        <th class='text-center'>Remove Student</th>\n
                        <th class='text-center'>See Report</th>\n
                    </tr>\n
                </thead>\n";
                echo "<tbody>\n";
                    $studentList = getStudentList($proj['proj_id']);

                    foreach ($studentList as $stud) {

                      //========================================= echo lname, fname, groupname=============================================
                      echo "<tr class='".$proj['proj_id']."studRow".$stud['acct_id']."'><td>" . $stud['acct_lname'] . "</td><td>" . $stud['acct_fname'] . "</td>
                      <td class='table_group_name' id='". $proj['proj_id'] ."table_group_name". $stud['acct_id'] ."' data-bs-toggle=\"modal\" data-bs-target=\"#groupModal\">" . $stud['group_name'] . "<i class=\"p-1 fas fa-edit\"></i></td>";
                      //=================================== echo submitted ==========================================
                      if (isMarkSubmitted($proj['proj_id'],$stud['acct_id'])) echo "<td class='text-center'><i class=\"fas fa-check-square text-success ". $proj['proj_id'] ."submittedCheck". $stud['acct_id'] ."\"></i></td>"; //if mark submitted
                      else echo "<td class='text-center'><i class=\"fas fa-square\"></i></td>"; 
                      
                      //================================== echo remove entry and remove student =============================================
                      echo "<td class=\"entry_remove_btn_parent text-center\"><i class=\"fas fa-trash entry_remove_btn\" id='". $proj['proj_id'] ."entry_remove_btn". $stud['acct_id'] ."' data-bs-toggle=\"modal\" data-bs-target=\"#confirmModal\"></i></td>";
                      echo "<td class=\"stud_remove_btn_parent text-center\"><i class=\"fas fa-minus-circle stud_remove_btn\" id='". $proj['proj_id'] ."stud_remove_btn". $stud['acct_id'] ."' data-bs-toggle=\"modal\" data-bs-target=\"#confirmModal\"></i></td>";
                      //=================================== echo if report available ==========================================
                      if (isMarkSubmitted($proj['proj_id'],$stud['acct_id'])) echo "<td class='stud_report_btn_parent text-center'><i class=\"fas fa-file iconColour\" onclick='openOffcanvas(".$stud['acct_id'].",".$stud['group_id'].")'></i></td></tr>"; 
                      else echo "<td class='stud_report_btn_parent text-center' ><i class=\"fas fa-file iconColour\" onclick='openOffcanvas(".$stud['acct_id'].",".$stud['group_id'].")'></i></td>";
                    }
                      
                    /* EXAMPLE OF HOW PARSED HTML LOOKS
                    <tr>
                        <td>Ibrahim</td>
                        <td>Hady</td>
                        <td>Panada's</td>
                    </tr>
                    <tr>
                        <td>Saha</td>
                        <td>Shushawn</td>
                        <td>Wakanda</td>
                    </tr>*/
                echo "</tbody>\n";
            echo "</table>\n";
            
            echo "<button type=\"button\" class=\"btn btn-primary m-auto w-100 mt-1 stud_btn\" id='stud_btn". $proj['proj_id'] ."' data-bs-toggle=\"modal\" data-bs-target=\"#studModal\">Add Student<i class=\"ms-1 fas fa-user-graduate\"></i></button>";
            echo "<button type='button' class='btn btn-success m-auto w-100 mt-1 report_btn' id='report_btn". $proj['proj_id'] ."' >Get CSV Report <i class='fas fa-file-csv'></i></button>";
            echo "<button type=\"button\" class=\"btn btn-danger m-auto w-100 mt-1 proj_remove_btn\" id='proj_remove_btn". $proj['proj_id'] ."' data-bs-toggle=\"modal\" data-bs-target=\"#confirmModal\">Remove Project<i class=\"ms-1 fas fa-trash\"></i></button>";      
            echo "</div>\n";
        echo "</div>\n";
      echo "</div>\n";
    }
  }
  else {
    //print something saying create new project top right

    echo "<button type=\"button\" class=\"btn btn-lg btn-danger\" data-bs-toggle=\"popover\" title=\"How to create an evaluation\" data-bs-content=\"Click on the button at the top right!\">You have not created any evaluations😞</button>";
  }

?>
    </div>
  </div>


    <!-- Scripts -->
    <script src="js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> <!-- Data Table js -->
    <script src="js/tutorial.js"></script>
    <script src="js/teacher.js"></script>
    <script>
        var session_id = <?php echo $_SESSION['userID'];?>;
        var scroll = <?php echo $scroll; ?>;
        var seenTutorial = <?php echo $seenTutorial; ?>;
    </script>
  </body>

</html>
