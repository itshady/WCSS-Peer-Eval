//retrieve all tab colors
$( document ).ready(function() {
    var tab1Color = $(":root").css("--tab-1");
    var tab2Color = $(":root").css("--tab-2");
    var tab3Color = $(":root").css("--tab-3");

    var tab_width_min = $(":root").css("--tab-width-min");
    var tab_width_max = $(":root").css("--tab-width-max");

    //when hovering over one of the headers, animate the corresponding navbar tab
    $("#header1").mouseenter(function() {
      $("#tab1").css("border-left", tab_width_max+" solid "+tab1Color);
    });

    $( "#header1" ).mouseleave(function() {
      $("#tab1").css("border-left", tab_width_min+" solid "+tab1Color);
    });

    $("#header2").mouseenter(function() {
      $("#tab2").css("border-left", tab_width_max+" solid "+tab2Color);
    });

    $( "#header2" ).mouseleave(function() {
      $("#tab2").css("border-left", tab_width_min+" solid "+tab2Color);
    });

    $("#header3").mouseenter(function() {
      $("#tab3").css("border-left", tab_width_max+" solid "+tab3Color);
    });

    $( "#header3" ).mouseleave(function() {
      $("#tab3").css("border-left", tab_width_min+" solid "+tab3Color);
    });
  });

$(document).ready( function () {
  $('.myTable').dataTable( {
    "bLengthChange": false
  } );
});

$(".editStudentInfo").click(function(){
    var elementID = $(this).attr("id");
    var stud_id = elementID.split("editStudentInfo")[1];

    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        
        document.getElementById("modal-body").innerHTML = this.responseText;
        document.getElementById("modal-title").innerHTML = "Update Student Info";

      }
    };

    xhttp.open("GET", "../AJAX/updateAccountSettings.php?target=studentInfo&acct_id="+stud_id); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
});

$("#myModalSave").click(function(){
    document.getElementById("myForm").submit();
});

$(".mySettings").click(function(){
    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        
        document.getElementById("left-settings").innerHTML = this.responseText;
        
        $(".resetButton").click(function(){
            $("#fname").val("");
            $("#lname").val("");
            $("#newPass").val("");
            $("#confirmPass").val("");
        });


        $(".teacherSubmit").click(function(){
            var valid = 1;

            //check fname valid
            if ($("#fname").val() != "" && /^[a-zA-Z]+$/.test($("#fname").val())) {
                $(".fname_div > .valid-feedback").css("display","block");
                $( "#fname" ).removeClass("is-invalid").addClass( "is-valid" );
                //valid = 1;
            }
            else {
                $(".fname_div > .valid-feedback").css("display","none");
                $( "#fname" ).removeClass("is-valid").addClass( "is-invalid" );
                valid = 0;
            }

            //check lname valid
            if ($("#lname").val() != "" && /^[a-zA-Z]+$/.test($("#lname").val())) {
                $(".lname_div > .valid-feedback").css("display","block");
                $( "#lname" ).removeClass("is-invalid").addClass( "is-valid" );
                //valid = 1;
            }
            else {
                $(".lname_div > .valid-feedback").css("display","none");
                $( "#lname" ).removeClass("is-valid").addClass( "is-invalid" );
                valid = 0;
            }

            //check newpass valid
            if (($("#newPass").val() != "" && $("#newPass").val().length >= 1) || $("#newPass").val() == "") {
                $(".newPass_div > .invalid-feedback").css("display","none");
                $( "#newPass" ).removeClass("is-invalid").addClass( "is-valid" );
                //valid = 1;
            }
            else {
                $(".newPass_div > .invalid-feedback").css("display","block");
                $( "#newPass" ).removeClass("is-valid").addClass( "is-invalid" );
                valid = 0;
            }

            //check confirmpass valid
            if ($("#confirmPass").val() == $("#newPass").val()) {
                $(".confirmPass_div > .invalid-feedback").css("display","none");
                $( "#confirmPass" ).removeClass("is-invalid").addClass( "is-valid" );
                //valid = 1;
            }
            else {
                $(".confirmPass_div > .invalid-feedback").css("display","block");
                $( "#confirmPass" ).removeClass("is-valid").addClass( "is-invalid" );
                valid = 0;
            }

            //check defaultpoints valid
            if ($.isNumeric( $("#defaultpoints").val() ) && $("#defaultpoints").val() > 0 && $("#defaultpoints").val() != "") {
                $(".defaultpoints_div > .invalid-feedback").css("display","none");
                $( "#defaultpoints" ).removeClass("is-invalid").addClass( "is-valid" );
                //valid = 1;
            }
            else {
                $(".defaultpoints_div > .invalid-feedback").css("display","block");
                $( "#defaultpoints" ).removeClass("is-valid").addClass( "is-invalid" );
                valid = 0;
            }
            
            if (valid == 1) $("#myTeacherForm").submit();
            //console.log(valid);

        });
        
        
      }
    };

    xhttp.open("GET", "../AJAX/updateSettingsPage.php?target=mySettings&userID="+session_id); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
});

$(".studentSettings").click(function(){
    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        
        document.getElementById("left-settings").innerHTML = this.responseText;
        $('.myTable').dataTable( {
          "bLengthChange": false
        } );

        //redeclare the edit student info click thingy
        $(".editStudentInfo").click(function(){
            var elementID = $(this).attr("id");
            var stud_id = elementID.split("editStudentInfo")[1];
        
            var xhttp = new XMLHttpRequest(); //Making a new request to another page
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
                
                document.getElementById("modal-body").innerHTML = this.responseText;
                document.getElementById("modal-title").innerHTML = "Update Student Info";
        
              }
            };
        
            xhttp.open("GET", "../AJAX/updateAccountSettings.php?target=studentInfo&acct_id="+stud_id); //Declaring the method and the file name of which we want to go to
            xhttp.send(); //Sending to file
        });
        
      }
    };

    xhttp.open("GET", "../AJAX/updateSettingsPage.php?target=studentSettings&userID="+session_id); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
});

$(".accountSettings").click(function(){
    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        
        document.getElementById("left-settings").innerHTML = this.responseText;
        $('.myTable').dataTable( {
          "bLengthChange": false
        } );

        //redeclare the edit student info click thingy
        $(".editAccountInfo").click(function(){
            var elementID = $(this).attr("id");
            var acct_id = elementID.split("editAccountInfo")[1];
        
            var xhttp = new XMLHttpRequest(); //Making a new request to another page
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
                
                document.getElementById("modal-body").innerHTML = this.responseText;
                document.getElementById("modal-title").innerHTML = "Update Account Info";
        
              }
            };
        
            xhttp.open("GET", "../AJAX/updateAccountSettings.php?target=accountInfo&acct_id="+acct_id); //Declaring the method and the file name of which we want to go to
            xhttp.send(); //Sending to file
        });
        
      }
    };

    xhttp.open("GET", "../AJAX/updateSettingsPage.php?target=accountSettings&userID="+session_id); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
});

//declare the edit student info click thingy
$(".editAccountInfo").click(function(){
    var elementID = $(this).attr("id");
    var acct_id = elementID.split("editAccountInfo")[1];

    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        
        document.getElementById("modal-body").innerHTML = this.responseText;
        document.getElementById("modal-title").innerHTML = "Update Account Info";

      }
    };

    xhttp.open("GET", "../AJAX/updateAccountSettings.php?target=accountInfo&acct_id="+acct_id); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
});


$(".myModalSave").click(function(){
    var valid = 1;

    //check fname valid
    if ($("#modal_fname").val() != "" && /^[a-zA-Z]+$/.test($("#modal_fname").val())) {
        $(".modal_fname_div > .valid-feedback").css("display","block");
        $( "#modal_fname" ).removeClass("is-invalid").addClass( "is-valid" );
        //valid = 1;
    }
    else {
        $(".modal_fname_div > .valid-feedback").css("display","none");
        $( "#modal_fname" ).removeClass("is-valid").addClass( "is-invalid" );
        valid = 0;
    }

    //check lname valid
    if ($("#modal_lname").val() != "" && /^[a-zA-Z]+$/.test($("#modal_lname").val())) {
        $(".modal_lname_div > .valid-feedback").css("display","block");
        $( "#modal_lname" ).removeClass("is-invalid").addClass( "is-valid" );
        //valid = 1;
    }
    else {
        $(".modal_lname_div > .valid-feedback").css("display","none");
        $( "#modal_lname" ).removeClass("is-valid").addClass( "is-invalid" );
        valid = 0;
    }

    //check newpass valid
    if (($("#modal_newPass").val() != "" && $("#modal_newPass").val().length >= 1) || $("#modal_newPass").val() == "") {
        $(".modal_newPass_div > .invalid-feedback").css("display","none");
        $( "#modal_newPass" ).removeClass("is-invalid").addClass( "is-valid" );
        //valid = 1;
    }
    else {
        $(".modal_newPass_div > .invalid-feedback").css("display","block");
        $( "#modal_newPass" ).removeClass("is-valid").addClass( "is-invalid" );
        valid = 0;
    }

    //check confirmpass valid
    if ($("#modal_confirmPass").val() == $("#modal_newPass").val()) {
        $(".modal_confirmPass_div > .invalid-feedback").css("display","none");
        $( "#modal_confirmPass" ).removeClass("is-invalid").addClass( "is-valid" );
        //valid = 1;
    }
    else {
        $(".modal_confirmPass_div > .invalid-feedback").css("display","block");
        $( "#modal_confirmPass" ).removeClass("is-valid").addClass( "is-invalid" );
        valid = 0;
    }

    if (valid == 1) $("#myModalForm").submit();
    
});

$(document).ready(function() {
  if (adminBool == 1) {
    $(".accountSettings").css("border-right","3px solid black");
    $(".accountSettings").css("background-color","#f9f9f9");
  }
  else {
    $(".studentSettings").css("border-right","3px solid black");
    $(".studentSettings").css("background-color","#f9f9f9");
  }
});


$(".studentSettings").click(function() {
  $(this).css("border-right","3px solid black");
  $(this).css("background-color","#f9f9f9");

  $(".mySettings").css("border-right","1px solid lightgrey");
  $(".mySettings").css("background-color","white");

  $(".accountSettings").css("border-right","1px solid lightgrey");
  $(".accountSettings").css("background-color","white");
});

$(".mySettings").click(function() {
  $(this).css("border-right","3px solid black");
  $(this).css("background-color","#f9f9f9");

  $(".studentSettings").css("border-right","1px solid lightgrey");
  $(".studentSettings").css("background-color","white");

  $(".accountSettings").css("border-right","1px solid lightgrey");
  $(".accountSettings").css("background-color","white");

});

$(".accountSettings").click(function() {
  $(this).css("border-right","3px solid black");
  $(this).css("background-color","#f9f9f9");

  $(".studentSettings").css("border-right","1px solid lightgrey");
  $(".studentSettings").css("background-color","white");

  $(".mySettings").css("border-right","1px solid lightgrey");
  $(".mySettings").css("background-color","white");

});

//rewatch tutorial
$(document).on("click", ".rewatchTutorial", function (e) {
  var xhttp = new XMLHttpRequest(); //Making a new request to another page

  xhttp.open("GET", "../AJAX/seenTutorial.php?newValue=0"); //Declaring the method and the file name of which we want to go to
  xhttp.send(); //Sending to file

  window.location.replace("../teacher.php");
});

//border-right: 3px solid black;
//background-color: #f9f9f9;