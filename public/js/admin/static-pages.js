//------------------------------
//js for new static-page
//------------------------------
$('#static_page_publish_button').on('click', function () {
    var action = 'new';
    publishStaticPageFunction(action);
})

//------------------------------
//js for edit static-page
//------------------------------
$('#static_page_publish_button_edit').on('click', function () {
    var action = 'edit';
    publishStaticPageFunction(action);
})

function publishStaticPageFunction(action){ 
    var tabs_languages = $('li[id^="lang_li_id_"]');
    var language_id_array = [];
    $.each(tabs_languages, function(key,value) {
        explode = value.id.split("_");
        language_id_array.push(explode[3]);
    });
    
    var data_array = [];
    var post_title;
    var post_content;
    var trigger = true;
    $.each(language_id_array, function(key, value) {
        post_title = $('#post_title_'+value).val();
        post_content = CKEDITOR.instances.post_content_1.getData();
        //if the form is all empty, will skip to next language.
        if(post_title == "" && post_content == ""){
            return true;
        }
        if(post_title == ""){
            alertFlashMessenger('warning','You must enter static page title');
            $( "#post_title_"+value).focus(); 
            trigger = false;
            return; 
        }
        if(post_content == ""){
            alertFlashMessenger('warning','You must enter static page content');
            $( "#post_content_"+value).focus();  
            trigger = false;
            return; 
        }
        data_array[value] = [post_title, post_content];
    });

    //if there is not one valid form, break
    if(language_id_array.length > 0 && data_array.length == 0){
        alertFlashMessenger('warning','Error, you must fill at least one static page content.');
        return;
    }    

    var orig_language_id = $('li[id^="lang_li_id_"]:last').attr('id');
    exploaded = orig_language_id.split("_");
    orig_language_id = exploaded[3];

    //ajax call to insert static page 
    var data = {
                language_id:orig_language_id,
                data_array : data_array
            }
    if(trigger){
        if(action == 'new'){
            addNewStaticPage(data);
        }else if(action == 'edit'){
            editExistingStaticPage(data);
        }
    }
}

function addNewStaticPage(data){

    $.ajax({
        type: 'POST',
        url: BASE_URL + '/cms/static-pages/add-new-static-page',
        data: {language_id: data.language_id},
        dataType: "json",
        success: function(result) {
            if(result.success){
                var static_page_id = result.id;
                //ajax call to insert content for all languages for that static-page
                $.ajax({
                    type: 'POST',
                    url: BASE_URL + '/cms/static-pages/add-translate-for-static-page',
                    data: {static_page_id: static_page_id, data_array : data.data_array},
                    dataType: "json",
                    success: function(response) {
                        alertFlashMessenger('success','New post has been saved!');
                        window.location.href = BASE_URL + '/cms/static-pages/edit/id/'+static_page_id;
                    }
                });
            }else{
                alertFlashMessenger('error','Error!');
            }
        }
    });
}

function editExistingStaticPage(data){

    var existing_static_page_id = $('input[id="existing_static_page_id"]').val();

    $.ajax({
        type: 'POST',
        url: BASE_URL + '/cms/static-pages/edit-static-page',
        data: {existing: existing_static_page_id, language_id: data.language_id},
        dataType: "json",
        success: function(result) {
            if(result.success){
                //ajax call to insert content for all languages for that static page
                $.ajax({
                    type: 'POST',
                    url: BASE_URL + '/cms/static-pages/edit-translate-for-static-page',
                    data: {static_page_id: existing_static_page_id, data_array : data.data_array},
                    dataType: "json",
                    success: function(response) {
                        alertFlashMessenger('success','Changes have been saved!');
                    }
                });
            }else{
                alertFlashMessenger('error','Error!');
            }
        }
    });
}