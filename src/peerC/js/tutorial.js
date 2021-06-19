$(document).ready(function(){
    if (seenTutorial == 0) $('#tutorialModal').modal('toggle');

    $('#carouselExampleDark').carousel({
        interval: false,
        wrap: false
    });
});

$(".secondLastButton").click(function(){
    $("#tutorialDone").css("display","inline-block");
});

var $div = $(".finalSlide");
var observer = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    if (mutation.attributeName === "class") {
      var attributeValue = $(mutation.target).prop(mutation.attributeName);
      //console.log("Class attribute changed to:", attributeValue);
      if ($div.hasClass( "carousel-item-next" ) == true || $div.hasClass( "carousel-item-prev" ) == true) $("#tutorialDone").css("display","inline-block");
      else if ($div.hasClass( "carousel-item-start" ) == true || $div.hasClass( "carousel-item-end" ) == true) $("#tutorialDone").css("display","none");
    }
  });
});
observer.observe($div[0], {
  attributes: true
});

/*$('.carousel').carousel('pause');*/

$(document).on("click", "#tutorialDone", function (e) {

    //change datalist innerHTML
    var xhttp = new XMLHttpRequest(); //Making a new request to another page

    xhttp.open("GET", "./AJAX/seenTutorial.php?newValue=1"); //Declaring the method and the file name of which we want to go to
    xhttp.send(); //Sending to file
  });


