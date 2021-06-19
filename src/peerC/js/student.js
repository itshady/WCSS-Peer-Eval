/*const options = {
    series: [{
      data: [0],
      type: 'pie'
    }]
  }

var chart = ""
var chartIsOpen = false;*/

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


var totalPoints = 0;

//creates new modal and prints our details on AJAX page
function openEditModal(memberId) {
  var groupModal = new bootstrap.Modal(document.getElementById('groupModal'));
  var groupModalBody = document.getElementById("groupModalBody");

  var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        // this.responseText is the response
        groupModalBody.innerHTML = this.responseText;
        groupModal.show();
        //openHighChart ();



        //get the number of student inputs in the modal
        var numOfInputs = $(".studInput").map(function() {
          return this.value;
        }).get();
        
        numOfInputs = numOfInputs.length / 2;


        //declare the options variable for the pie chart
        const options = {
          series: [{
            data: [
          ],
            type: 'pie'
          }],
          plotOptions: {
            pie: {
              allowPointSelect: true,
              cursor: "pointer",
              dataLabels: {
                format: "<b>{point.name}</b>: {point.percentage:.1f} %"
              },
            }
          }
        }

        //declare the number of arrays within data based off number of students
        for (var y=0; y<numOfInputs ; y++){
          options.series[0].data[y] = {};
        }

        //get the student names in an array
        var names = $(".studName").map(function() {
          return this.innerHTML;
        }).get();

        //set the values of name and y in the pie chart (y is the number value)
        for (var x=0 ; x<numOfInputs ; x++) {
          options.series[0].data[x]["y"] = 0;
          options.series[0].data[x]["name"] = names[x];
        }
        
        //creates the highchart
        const chart = Highcharts.chart('container', options);
      
        //add an onchange event listener for each input that links to the updatePie function
        options.series[0].data.forEach((data, i) => {
          const el = document.getElementById("studInput"+i);
          el.addEventListener('change', updatePie(i));
        })

        //add onchanges for the number inputs as well
        options.series[0].data.forEach((data, i) => {
          const el = document.getElementById("boxStudInput"+i);
          el.addEventListener('keyup', updatePie(i));
        })

        options.series[0].data.forEach((data, i) => {
          const el = document.getElementById("boxStudInput"+i);
          el.addEventListener('change', updatePie(i));
        })
      
        //the updatePie function that runs on input change
        function updatePie (i) {
          return function (e) {
            var value = Number(e.target.value) || null
            if (!$.isNumeric(value)) value = 0;
            chart.series[0].data[i].update(value)
          }
        }

        //fade pie chart in
        $(".chart").fadeIn("slow");

      }
    };
  xhttp.open("GET", "AJAX/getGroupForm.php?id="+memberId);

  xhttp.send();
}

function getUserPoints () {

    var numInput = $(".studInput").map(function() {
      return this.value;
    }).get();

    var numStud = document.getElementById("numStud").value;


    var userPoints = 0;
    for ( i = 0; i < numStud*2; i++ ) {
      if ( numInput[i] == "" ) userPoints += 0;
      else userPoints += parseInt(numInput[i]);
      i++;
    }

    
    return userPoints; 
    
}

function getAvailPoints (numInput, numStud) {
    //uses new input to calc new avail points
    var availPoints = parseInt(totalPoints);
    for ( i = 0; i < numStud; i++) {
        var currentVal = numInput[i];
        availPoints = availPoints - Number(numInput[i]);
    }

    return availPoints;
}

//submits form detalls when user clicks 'Submit Scores'
function saveForm() {

  userPoints = getUserPoints (); 
  
  //prints error message
  if ( Number(userPoints) == Number(totalPoints) ) {
    document.getElementById("groupEdit").submit();
  } else if (  Number(userPoints) > Number(totalPoints) ) {
    document.getElementById("errorMessage").innerHTML = "ERROR: You have used too many points!";
  } else {
    document.getElementById("errorMessage").innerHTML = "ERROR: You have not used all available points!";
  }

}

//gets total points to award group member
$(".editEval").click(function(){

  var elementID = $(this).attr("id");
  var member_id = elementID.split("editEval")[1];

  var groupModalBody = document.getElementById("groupModalBody");
  var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        // this.responseText is the response
        totalPoints = this.responseText;
      }
    };
  xhttp.open("GET", "AJAX/getAvailPoints.php?id="+member_id);
  xhttp.send(); 

});

//if input changes, it adjusts available points
$(document).on('change','.studInput',function(){
  
  $(this).siblings().val(this.value);
  var groupModalBody = document.getElementById("availPoints");
  var numStud = document.getElementById("numStud").value; 
  //console.log("here");
  
  //var studInput = document.getElementById("studInput").value;

  //gets the values from all inputs
  var numInput = $(".form-range").map(function() {
    return this.value;
  }).get();
  //console.log(numInput);

  //gets group names
  var names = $(".studName").map(function() {
      return this.innerHTML;
  }).get();

  availPoints = getAvailPoints (numInput, numStud);
  
  userPoints = getUserPoints ();

  //prints out new avail points for user on modal
  if (availPoints < 0 ) {
    groupModalBody.innerHTML = "<div class='negative'> You Are<h1> " + Math.abs(availPoints) + "</h1> Points Over! </div>" ;
  } else if (availPoints > 0) {
    groupModalBody.innerHTML = "You Have<h1> " + Math.abs(availPoints) + "</h1> Points Left" ;
  } else {
    groupModalBody.innerHTML = " NO POINTS REMANING 💯 😀 ";
  }
  

  //clears error after form submission
  if ( Number(userPoints) != Number(totalPoints) ) {
    document.getElementById("errorMessage").innerHTML = " ";
  }

  //set the html next to the range to the value
  
  names.push('Available Points\n');
  userInput = numInput;
  userInput.push(availPoints);


});

//if input changes, it adjusts available points
$(document).on('keyup','.boxStudInput',function(){
  if (this.value.length == 0) this.value = 0;
  $(this).siblings().val(this.value);
  var groupModalBody = document.getElementById("availPoints");
  var numStud = document.getElementById("numStud").value; 
  //console.log("here");
  
  //var studInput = document.getElementById("studInput").value;

  //gets the values from all inputs
  var numInput = $(".form-range").map(function() {
    return this.value;
  }).get();
  //console.log(numInput);

  //gets group names
  var names = $(".studName").map(function() {
      return this.innerHTML;
  }).get();

  availPoints = getAvailPoints (numInput, numStud);
  
  userPoints = getUserPoints ();

  //prints out new avail points for user on modal
  if (availPoints < 0 ) {
    groupModalBody.innerHTML = "<div class='negative'> You Are<h1> " + Math.abs(availPoints) + "</h1> Points Over! </div>" ;
  } else if (availPoints > 0) {
    groupModalBody.innerHTML = "You Have<h1> " + Math.abs(availPoints) + "</h1> Points Left" ;
  } else {
    groupModalBody.innerHTML = " NO POINTS REMANING 💯 😀 ";
  }
  

  //clears error after form submission
  if ( Number(userPoints) != Number(totalPoints) ) {
    document.getElementById("errorMessage").innerHTML = " ";
  }

  //set the html next to the range to the value
  
  names.push('Available Points\n');
  userInput = numInput;
  userInput.push(availPoints);


});


//opens high chart js
/*function openHighChart (userInput=[], studNames=[], numStud, availPoints) {
  
  console.log("hi" + numStud);
 
  //console.log(studNames);
  var chartsData = {
    chart: {
      plotBackgroundColor: 	null,
      plotBorderWidth: null,
      plotShadow: false,
      type: "pie",
      credits: null,
      animation: {
        animation: false
      }
    },
    exporting: {
      buttons: {
          contextButton: {
              enabled: false
          }
      }
    },
    title: {
      text: ""
    },
    tooltip: {
      pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>"
    },
    accessibility: {
      point: {
        valueSuffix: "%"
      }
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: "pointer",
        dataLabels: {
          //enabled: true,
          format: "<b>{point.name}</b>: {point.percentage:.1f} %"
        },
        series: {
          defer: 5,
          animation: true
        }
      }
    },
    series: [
      {
        name: "Individuals",
        colorByPoint: true,
        defer: 5,
        animation: false,
        //fade: false,
        data: []
      }
    ]
  };


  //chartsData['series'][0]['data'].push({name: "testing",y:100});
  //studNames.forEach(popArray);
  console.log("NUMSTUD: " + numStud);

  for ( i = 0; i < numStud + 1; i++ ) {
    chartsData['series'][0].data.push({name: studNames[i] , y: parseInt(userInput[i]) });
  }

  console.log(chartsData['series'][0]['data']);

  chart = Highcharts.chart("container", chartsData);
  chartIsOpen = true;
};

function updateHighChart () {
  chart = Highcharts.chart("container", chartsData);
}
/*
    $(document).ready( function () {
        openHighChart ();
    });
*/

/*js to make the notes interactive*/
$(document).ready(function () {
  all_notes = $("li a");

  all_notes.on("keyup", function () {
    note_title = $(this).find("h2").text();
    note_content = $(this).find("p").text();

    item_key = "list_" + $(this).parent().index();

    data = {
      title: note_title,
      content: note_content
    };

    window.localStorage.setItem(item_key, JSON.stringify(data));
  });

  all_notes.each(function (index) {
    data = JSON.parse(window.localStorage.getItem("list_" + index));

    if (data !== null) {
      note_title = data.title;
      note_content = data.content;

      $(this).find("h2").text(note_title);
      $(this).find("p").text(note_content);
    }
  });
});

var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl)
})

