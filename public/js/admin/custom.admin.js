//upload file element style
jQuery(function($) {
    $(".form input[type=file]").filestyle({
        buttonText: "Izaberi...",
        classButton: "btn-primary"
    });
    Dropzone.options.myAwesomeDropzone = {
        acceptedFiles: "image/*",
        maxFilesize: 20, // MB 
        parallelUploads: 1,
        maxFiles: 100,
        thumbnailWidth: 280,
        thumbnailHeight: 280,
        addRemoveLinks: true,
        dictDefaultMessage: "<center>Drop files here<br> to upload <br><span> (or click)<span></center>",
        previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-details\">\n<img data-dz-thumbnail />\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-success-mark\"><span>✔</span></div>\n </div>",
        init: function() {
            this.on("addedfile", function() {
                $("input[id='youtube_url']").attr('disabled', true);
                $("button[id='youtube_url_submit']").attr('disabled', true);
            });
            if(file_name){
                var  file = {name: file_name, accepted: 'true'};
                this.files.push(file);
                this.options.addedfile.call(this, file);
                this.options.thumbnail.call(this, file, file.name);
            }
        },
        complete: function(response, res) {
            $("input[id='youtube_url']").attr('disabled', false);
            $("button[id='youtube_url_submit']").attr('disabled', false);

            var parts = response.name.split(".");
            title = response.name.replace('.'+parts[parts.length-1], '');
            $.ajax({
                url: BASE_URL + '/cms/medias/get-last-uploaded-image',
                data: {title: title},
                type: "POST",
                dataType: "json",
                success: function(result) {
                    if (result.success) {
                        $(".dz-preview:last-child").attr('data-id', result.file_name.id);
                    } else {
                        alertFlashMessenger('warning','Error, image id not found and linked.');
                    }
                },
                error: function() {
                    alertFlashMessenger('warning','Error, image not linked');
                }
            });
            
            if(response.xhr.responseText === 'false'){
                alertFlashMessenger('error','Error uploading file '+response.name);
            }
            jQuery('.content-reload').load($(".content-reload").attr("data-reloadUrl") + ' .content-reload');
            if (response._removeLink) {
                return response._removeLink.textContent = this.options.dictRemoveFile;
            }
        },  
        removedfile: function(file) {
            var media_id = file.previewElement.attributes['data-id'].value;
            
            $.ajax({
                url: BASE_URL + '/cms/medias/delete-removed-file',
                data: {media_id: media_id},
                type: "POST",
                dataType: "json",
                success: function(result) {
                    if (result.success) {
                        alertFlashMessenger('success','File succesfully deleted from database');
                        var _ref;
                        if ((_ref = file.previewElement) != null) {
                          _ref.parentNode.removeChild(file.previewElement);
                        }
                        $('.dz-message').css('display','block');
                        jQuery('.content-reload').load($(".content-reload").attr("data-reloadUrl") + ' .content-reload');
                        return this._updateMaxFilesReachedClass();
                    } else {
                        alertFlashMessenger('warning','Error deleting file from database.');
                    }
                },
                error: function() {
                    alertFlashMessenger('warning','Error deleting file');
                }
            });
        },
        error: function(file, message) {
            alertFlashMessenger('warning',message);
            return this.removeFile(file);
      },
    };

    $('#myTab a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
    preventPaginator();
})


//triger delete href if result is true
function deleteHref(result, href) {
    if (result) {
        this.location = href;
    }
}


function addIcon(icon_id, icon_class) {
    $("input[name='icon_id']").val(icon_id);
    $('.preview i').removeClass();
    $('.preview i').addClass('icon-large');
    $('.preview i').addClass(icon_class);
    $('.preview').css('display','block');

    $('#icons-modal').modal('hide');
}

function removeIcon() {
    $("input[name='icon_id']").val('');
    $('.preview i').removeClass();
    $('.preview i').addClass('icon-large');
    $('.preview').css('display','none');
}


//select transform 
$(document).ready(function() {
    $(".select").chosen({
        no_results_text: "No results for ",
        allow_single_deselect: true
    });
    
    $('.datepicker').datepicker({
        "format": "dd.mm.yyyy", 
        "weekStart": 1, 
        "autoclose": true
    });

    $('.multiselect').multiselect({
      includeSelectAllOption: true,
      buttonWidth: '150px',
      numberDisplayed: 1,
      maxHeight: 430
    });
 
});

// image preview on edit pages
function showImagePreview(id, path) {
    jQuery(id).parent().append('<div style="margin-bottom:5px;" class="thumb"><img style="max-width:200px;" src="' + path + '"" /></div>');
}

function showSwfPreview(id, path) {
    jQuery(id).parent().append('<div style="margin-bottom:5px;" class="thumb"><object width="200" height="70" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"><param name="SRC" value="' + path + '"><embed src = "' + path + '"></embed></object></div>');
}

function preventPaginator() {
    $("div[id^='modal-content'] .pagination a").click(function(e) {
        var div_id = $(this).parent().closest('div').attr('id');
        var parts = div_id.split("_");
        
        e.preventDefault();
        jQuery('.content-reload-'+parts[1]).load($(this).attr("href") + ' .content-reload-'+parts[1], function() {
            preventPaginator();
        });
    })
}


$("div[id^='show_on_hover']").hide();

var timeoutId;
$('.comments-list li').hover(function() {
    var _this = $(this);
    if (!timeoutId) {
        timeoutId = window.setTimeout(function() {
            timeoutId = null;
            $('#show_on_hover_'+ _this.attr('id')).slideDown();
       }, 100);
    }
}, function() {
    var _this = $(this);
    if (timeoutId) {
        window.clearTimeout(timeoutId);
        timeoutId = null;
    }
    else {
       $('#show_on_hover_'+ _this.attr('id')).slideUp();
    }
});



function alertFlashMessenger(type, message){
    var opts = {
        "closeButton": false,
        "debug": false,
        "positionClass": "toast-top-full-width",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
        };
    if(type == 'success'){
        toastr.success(message, opts);
    }else if(type == 'info'){
        toastr.info(message, opts);
    }else if(type == 'warning'){
        toastr.warning(message, opts);
    }else if(type == 'error'){
        toastr.error(message, opts);
    }
}