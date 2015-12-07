$(document).ready(function() {
    $(".fancybox").fancybox({
      padding    : 10,
      helpers: { 
        title: {
          type: 'inside',
          position : 'top'
        }
      },
      beforeShow: function() {
        var id =  $(this.element).data('id').split("_");
        var description =  $(this.element).data('description');
        id = id.pop();

        if(autoshow_image){
          var url = window.location.href.replace('/'+getLastUrlParamValue(), '/'+id);
        }else{
          var url = window.location.href+'/'+id;
        }

        this.title += 'Image ' + (this.index + 1) + ' of ' + this.group.length;
        this.title += '<div class="fb-like gallery" data-href="'+url+'" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>';
        this.title += '<br>'+description;
      },
      afterShow : function () {
        FB.XFBML.parse();
      },
   }); 

   $("#menu-menu").click(function(){
      $(".menu_gallery").eq(0).trigger("click");
   });

   $("#restoran-slike").click(function(){
      $(".restorant_gallery").eq(0).trigger("click");
   });

   $("#kreacije").click(function(){
      $(".creations_gallery").eq(0).trigger("click");
   });

   $("#milenija").click(function(){
      $(".tradition_gallery").eq(0).trigger("click");
   });

  $("#basta").click(function(){
      $(".garden_gallery").eq(0).trigger("click");
   });

    $("#recepti").click(function(){
      $(".recipes_gallery").eq(0).trigger("click");
   });

  $(".rooms_gallery_triger").click(function(){
      $(".rooms_gallery").eq(0).trigger("click");
   }); 

  setTimeout(function(){ fancyboxAutoClick();}, 500);             
});

function fancyboxAutoClick(){
    if(autoshow_image){
      var img_id = "fancy-box_"+parseInt(getLastUrlParamValue(),10);
      $(".fancybox[data-id="+img_id+"]").trigger('click');       
    }
}

function getLastUrlParamValue(){
    var url_path_arr = window.location.href.split("/");
    return url_path_arr[url_path_arr.length -1];
}