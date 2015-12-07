$(document).ready(function(){
  width = $( window ).width()
   z = 481
   if (width < z ) {
     $('meta[name=viewport]').attr("content","width=480,user-scalable=no");
   }
    $("#dropdown").click(function(){
         $(".full").toggle();
         $(".nav").animate({width:"toggle"},500);
       	 contentPosition = $("#content").css("position");
       	if ( contentPosition == "relative"){
       		$("#content").css("position","fixed");
       		$("#footer").css({"position":"fixed","bottom":"-500px"});

       	} else {
       			$("#content").css("position","relative");
            $("#footer").css({"position":"","bottom":""});
       	  }
    });
        y = 750;
        window.addEventListener("orientationchange", function(){
            width = $( document ).width();
            if (width > y) {
              $(".nav").css("display","none");
              $(".full").css("display","none");
              $("#content").css("position","relative");
              $("#footer").css({"position":"","bottom":""});
          }
          
          
            content = $('meta[name=viewport]').attr("content");
            if (content == "width=480,user-scalable=no"){
              $('meta[name=viewport]').attr("content","width=device-width,initial-scale=1.0,user-scalable=no");
            } else {
              $('meta[name=viewport]').attr("content","width=480,user-scalable=no");
            }
        },false);
});