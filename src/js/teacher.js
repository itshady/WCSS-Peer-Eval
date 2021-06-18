$(document).ready( function () {  
  //var t = $('.table_id').DataTable();

    $('.table_id').dataTable( {
      /*"columnDefs": [
        { "width": "20%", "targets": 0 }
      ],*/
      "pageLength": 50,
      "language": {
        "lengthMenu": "Show _MENU_ students"
      }
    } );


    //make popovers work in bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    })
  });


  //on click of neweval button populate modal
  $(".newEvalButton").click(function() {
    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        
        $("#proj-points").val(this.responseText); //this.responseText is the response given by the page it was sent to

      }
    };

    xhttp.open("GET", "./AJAX/updateNewEvalModal.php?acct_id="+session_id); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
  });
  
  //AJAX to update group name on save modal
  $("#groupModalSave").click(function(){
    var input = $("#groupModal_acct_id").val();
    var proj_id = input.split(".")[0];
    var acct_id = input.split(".")[1];
    var groupname = $("#groupname").val();

    var currentGroup = $("#"+proj_id+"table_group_name"+acct_id).html().split("<i")[0];

    console.log(currentGroup);
    console.log(currentGroup == groupname);
    if (groupname != currentGroup){
      var xhttp = new XMLHttpRequest(); //Making a new request to another page
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
          
          $("#"+proj_id+"table_group_name"+acct_id).html(this.responseText); //this.responseText is the response given by the page it was sent to

        }
      };

      xhttp.open("GET", "./AJAX/updateGroup.php?id="+acct_id+"&group="+groupname+"&proj_id="+proj_id); //Declaring the method and the file name of which we want to go to
      xhttp.send(); //Sending to file

      var request = new XMLHttpRequest();
      
      request.open("GET", "AJAX/removeStudentEntry.php?stud_id="+acct_id+"&proj_id="+proj_id+"&target=both");
      request.send();

      $("."+proj_id+"submittedCheck"+acct_id).removeClass("text-success fa-check-square").addClass("fa-square");
    }
  });

  //set group modal hidden input value to acct_id of click button
  $(".table_group_name").click(function(){
    var elementID = $(this).attr("id");
    var acct_id = elementID.split("table_group_name")[1];
    
    var proj_id = elementID.split("table_group_name")[0];
    
    $("#groupname").val(this.innerHTML.split("<i")[0]);

    $("#groupModal_acct_id").val(proj_id+"."+acct_id);

    //update class name that start with ...
    //$("#groupname").removeClass("[class^=\"group_proj_id\"]").addClass("group_proj_id121231");
    /*$("#groupname").removeClass (function (index, className) {
      return (className.match (/^(group_proj_id)/g) || []).join(' '); ///(^|\s)ativo\S+/g
    });*/


    var el = document.querySelector('#groupname');
    for (let i = el.classList.length - 1; i >= 0; i--) {
        const className = el.classList[i];
        if (className.startsWith('group_proj_id')) {
            el.classList.remove(className);
        }
    }
    $("#groupname").addClass("group_proj_id"+proj_id);

  });

  //give groups list on keyup
  $(document).on("keyup", "#groupname", function (e) {
    var className = $("#groupname").attr('class');
    var proj_id = className.split("group_proj_id")[1];
    var input = $("#groupname").val(); 
    //change datalist innerHTML
    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        $("#datalistOptions").html(this.responseText); //this.responseText is the response given by the page it was sent to
      }
    };

    xhttp.open("GET", "./AJAX/groupDataList.php?proj_id="+proj_id+"&input="+input); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
  });

  $(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() != $(document).height()) {
      var scroll = $(window).scrollTop();
      $("#scroll").val(scroll);
    }
 });

  //AJAX to update defaultPoints on save modal
  $("#pointsModalSave").click(function(){
    var proj_id = $("#pointsModal_proj_id").val();
    var classcode = $("#points_classcode").val();
    var proj_name = $("#points_proj_name").val();
    var points = $("#points").val();

    var xhttp = new XMLHttpRequest(); //Making a new request to another page
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) { //If state is correct and it doesn't error (404)
        var str = this.responseText;
        
        $("#defaultPoints"+proj_id).html(str.split("🌟")[0]); //this.responseText is the response given by the page it was sent to
        $("#accordion-classcode"+proj_id).html(str.split("🌟")[1]); //this.responseText is the response given by the page it was sent to

      }
    };

    xhttp.open("GET", "./AJAX/updateProjectPoints.php?proj_id="+proj_id+"&points="+points+"&classcode="+classcode+"&proj_name="+proj_name); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file

  });

  //set points modal input values to proj_id, proj_name, proj_classcode, proj_points of clicked button
  $(".accordion-inner-flex").click(function(){
    var elementID = $(this).attr("id");
    var proj_id = elementID.split("accordion-inner-flex")[1];
    
    var points = $("#defaultPoints"+proj_id).html();
    var classAndProjName = $("#accordion-classcode"+proj_id).html();
    
    $("#points_classcode").val(classAndProjName.split("&nbsp;-&nbsp;")[0]);
    $("#points_proj_name").val(classAndProjName.split("&nbsp;-&nbsp;")[1]);
    $("#points").val(points.split("Default Points: ")[1]);
    $("#pointsModal_proj_id").val(proj_id);
  });

  

  //set student modal proj_id hidden input value
  $(".stud_btn").click(function(){
    var elementID = $(this).attr("id");
    
    var proj_id = elementID.split("stud_btn")[1];

    $("#studModal_proj_id").val(proj_id);
  });

  //set confirm modal proj_id hidden input value
  $(".proj_remove_btn").click(function(){
    var elementID = $(this).attr("id");
    
    var proj_id = elementID.split("proj_remove_btn")[1];

    $(".areYouSure").html("Are you sure you want to delete this project?");
    $("#confirmModal_proj_id").val(proj_id);
    $("#confirmTarget").val("rmvProject");
  });

  //set confirm modal proj_id hidden input value
  $(".stud_remove_btn").click(function(){
    var elementID = $(this).attr("id");

    var proj_id = elementID.split("stud_remove_btn")[0];
    var stud_id = elementID.split("stud_remove_btn")[1];

    $(".areYouSure").html("Are you sure you want to remove this student from the project?");
    $("#confirmModal_proj_id").val(proj_id);
    $("#confirmModal_stud_id").val(stud_id);
    $("#confirmTarget").val("rmvStudent");
  });

  //gets CSV report 
$(".report_btn").click(function() {
  var elementID = $(this).attr("id");

  var proj_id = elementID.split("report_btn")[1];

  var xhttp = new XMLHttpRequest(); //Making a new request to another page
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

      var str = JSON.parse(this.responseText);
      
      let csvContent = "data:text/csv;charset=utf-8," 
      + str.map(e => e.join(",")).join("\n");
      
      var d = new Date();
      var date = d.getFullYear()+"-";
      date += (d.getMonth()+1)+"-";
      date += d.getDate()+"-";
      date += d.getHours()+"-";
      date += d.getMinutes()+"-";
      date += d.getSeconds();

      var filename = $("#accordion-classcode"+proj_id).html().split("&nbsp")[0];
      filename += "_studentReport_"
      filename += date;
      filename += ".csv";

      var encodedUri = encodeURI(csvContent);
      var link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", filename);
      $(link).css("display","none");
      document.body.appendChild(link); // Required for FF
      
      link.click(); // This will download the data file named "my_data.csv".
    } 
  };
  xhttp.open("GET", "./AJAX/getCSV.php?projId="+proj_id); //Declaring the method and the file name of which we want to go to
  xhttp.send(); //Sending to file
}); 


  //prevent accordion open when on toggle
  $(".form-check-input").mouseover(function(){
    var num = $(this).attr('id').split('.').pop();
    $(".accordion-wrapper"+num).attr('data-bs-toggle',''); 
  });

  $(".form-check-input").mouseleave(function(){
    var num = $(this).attr('id').split('.').pop();
    $(".accordion-wrapper"+num).attr('data-bs-toggle','collapse'); 
  });

  //prevent accordion open when on change proj
  $(".accordion-inner-flex").mouseover(function(){
    var num = $(this).attr('id').split('accordion-inner-flex').pop();
    $(".accordion-wrapper"+num).attr('data-bs-toggle',''); 
  });

  $(".accordion-inner-flex").mouseleave(function(){
    var num = $(this).attr('id').split('accordion-inner-flex').pop();
    $(".accordion-wrapper"+num).attr('data-bs-toggle','collapse'); 
  });

  


  //when clicking toggle button
  $(".form-check-input").click(function(e){
    var num = $(this).attr('id').split('.').pop();
    

    if($(this).prop("checked") == true) var toggle = 1;
    else var toggle = 0;

    var xhttp = new XMLHttpRequest(); //Making a new request to another page

    xhttp.open("GET", "./AJAX/updateToggle.php?id="+num+"&toggle="+toggle); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
  });

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

  //opens off canvas and shows student report
  function openOffcanvas(memberId, groupId) {

    var groupOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasRight'));
    var groupOffcanvasBody = document.getElementById("offcanvas-body");
  
    var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          // this.responseText is the response
          groupOffcanvasBody.innerHTML = this.responseText;
          groupOffcanvas.show();
          // openHighChart ();
        } 
      };
    xhttp.open("GET", "AJAX/getStudentReport.php?acctId="+memberId + "&groupId="+groupId);
  
    xhttp.send();
  }

  

  //set confirm modal hidden values
  $(".entry_remove_btn").click(function(){
    var id = $(this).attr("id");
    
    var proj_id = id.split("entry_remove_btn")[0];
    var stud_id = id.split("entry_remove_btn")[1];
    console.log(stud_id);
    console.log(proj_id);
    $(".areYouSure").html("Are you sure you want to delete this student's entry?");
    $("#confirmModal_proj_id").val(proj_id);
    $("#confirmModal_stud_id").val(stud_id);
    $("#confirmTarget").val("rmvEntry");
  });

  //delete things
  $("#confirmModalDelete").click(function() {
    if ($("#confirmTarget").val() == "rmvEntry"){
      stud_id = $("#confirmModal_stud_id").val();
      proj_id = $("#confirmModal_proj_id").val();
      
      var xhttp = new XMLHttpRequest();
    
      xhttp.open("GET", "AJAX/removeStudentEntry.php?stud_id="+stud_id+"&proj_id="+proj_id+"&target=marker");
      xhttp.send();
      $("."+proj_id+"submittedCheck"+stud_id).removeClass("text-success fa-check-square").addClass("fa-square");
    }
    else {
      stud_id = $("#confirmModal_stud_id").val();
      proj_id = $("#confirmModal_proj_id").val();
      target = $("#confirmTarget").val();

      var xhttp = new XMLHttpRequest();
    
      xhttp.open("GET", "AJAX/formRemoveProject.php?stud_id="+stud_id+"&proj_id="+proj_id+"&target="+target);
      xhttp.send();
      
      if (target == "rmvProject"){
        $(".accordion-wrapper"+proj_id).css("display","none");
        $(".accordion-body"+proj_id).css("display","none");
      }
      else if (target == "rmvStudent") {
        $("."+proj_id+"studRow"+stud_id).css("display","none");
      }

    }
  });


  //fill student add modal with student
  $(document).on("keyup", "#stud_email", function (e) {

    var email = $("#stud_email").val(); 
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = this.responseText;
        if (response != "no") {

          $("#stud_lname").val(response.split("/")[0]);
          $("#stud_fname").val(response.split("/")[1]);
        }
      } 
    };
    xhttp.open("GET", "AJAX/checkForStudent.php?email="+email);
    xhttp.send();
  });

  $(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() != $(document).height()) {
      var scroll = $(window).scrollTop();
      $("#scroll").val(scroll);
    }
 });

 $(document).ready(function() {
  document.documentElement.scrollTop = document.body.scrollTop = scroll;
 }); 


 