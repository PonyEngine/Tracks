var imgemailbgs=[];
var updateEmail=false;


function clearMarkings(){
    $("#formstatus").removeClass();
    $("#formstatus").html("");
    //Clear red markings
    $.each($("#formFields"), function(name, value){
        $(name).removeClass('red');
    });
}

function optionsProcessingNotif(){
    var eventName=$('#eventSelect :selected').val();
    $("#socialoptions").html("<div style='text-align:center;'> Loading Options for "+eventName+" &nbsp;<img src='/skin/images/smallSpinner.gif'/></div>");
}

function processingNotif(){
    $("#formstatus").addClass('alert');
    $("#formstatus").addClass('alert-info');
    $("#formstatus").html("<img src='/skin/images/smallSpinner.gif'/> Processing");
}

function processCurrentSelection(){
    var eventId=$('#eventSelect :selected').attr('id');
    var eventData='eventId='+eventId;
    //Get the table for this event
    //Set the event Id for items that need it
    clearMarkings();
    optionsProcessingNotif();

    $.post("/app/socialoptions",eventData,
        function(data) {
            try
            {
                $('#socialoptions').html(data);

                $('.assetupload').on('change', function(){
                    var thisId=$(this).attr("id");
                    $("#"+thisId+"_prevLoader").html('<img src="/skin/images/ajax-loader.gif" alt="Uploading...."/>');
                    $("#"+thisId+"_form").hide();
                    //processingNotif();
                    var options={
                        success:
                            function(data, statusText, xhr, $form){
                                clearMarkings();
                                try
                                {
                                    var jsonParsed = JSON.parse(data);
                                    if(jsonParsed.status.code=="200"){
                                        var fileInfo=jsonParsed.data.fileInfo;
                                        var imgHTMLColumn="<td><ul class='thumbnails'><li class='span3'><div class='thumbnail'><img src='"+fileInfo.filePath+"' alt='"+fileInfo.fileName+"' height='200' width='200'/></div></li></ul></td>";
                                        $("#"+thisId+"_previewTable tr:first td:first").before(imgHTMLColumn);

                                        //Push on array to save asset
                                        var thisArrCount=0;
                                        switch(thisId){
                                            case 'emailbg':
                                                imgemailbgs.push(fileInfo.filePath);
                                                if(imgemailbgs.length<1)$("#"+thisId+"_form").show();
                                                updateEmail=true;
                                            break;
                                        }
                                        $("#"+thisId+"_prevLoader").html('');

                                    }else{
                                        $("#"+thisId+"_form").show();
                                        $("#"+thisId+"_prevLoader").html('');
                                        clearMarkings();
                                        var errors=jsonParsed.status.errors;
                                        var errorMsg='<ul>';
                                        $.each(errors, function(name, value) {
                                            //Highlight
                                            $("#"+name).addClass('red');
                                            //Error Msg
                                            errorMsg+="<li>"+value+"</li>";
                                        });
                                        errorMsg+='</ul>';

                                        //Print Errors
                                        $("#formstatus").addClass('alert');
                                        $("#formstatus").addClass('alert-error');
                                        $("#formstatus").html(errorMsg);
                                        //Highlight trouble areas
                                    }
                                }catch(err){
                                    //alert("Cannot preview image");
                                    $("#"+thisId+"_form").show();
                                    $("#"+thisId+"_prevLoader").html('');
                                    $("#formstatus").html(data);
                                }
                            }
                    };
                    $("#"+thisId+"_form").ajaxForm(options).submit();
                });

            }
            catch(err)
            {
                var txt="Error description: " + err.message + "\n\n";
                $("#formstatus").html(data);
            }

            var brandId=$('#eventSelect :selected').attr('brandId');
            var brandName=$('#eventSelect :selected').attr('brandName');
            var eventName=$('#eventSelect :selected').attr('eventName');
            var examplePathName=(brandName && eventName)?'/@'+brandName+'/'+eventName.replace(/\s/g, "")+'/example':'';

            $("#urlBrandEventName").html(examplePathName);
            $("#hrefBrandEventName").attr('href',examplePathName);
            $("#hrefBrandEventName_email").attr('href',examplePathName+"?email=1");

            var emailMessage=$("#emailMsg").attr('data');
             ko.applyBindings(new SettingsViewModel(emailMessage,$("#socialoptions")[0]));

            //Email Update
            $('#email_msg').on('change', function(){
                updateEmail=true;
            });
        });

};

var SettingsViewModel = function(emailMsg) {
    var self=this;
    self.eventId=ko.observable();
    self.updatePage=ko.observable();
    self.updateSocialNetworks=ko.observable();
    self.updateEmail=ko.observable();
    self.updateMMS=ko.observable();
    self.emailMsg=ko.observable(emailMsg);
    self.imgEmailBGArray = ko.observableArray();

    self.updateSettingsClick = function() {
        //Only process if one of the updates is true
        self.updateEmail=updateEmail;

        if(self.updateEmail){
            processingNotif();
            self.imgEmailBGArray=imgemailbgs;
            var eventId=$('#eventSelect :selected').attr('id');
            self.eventId=eventId;

            var thisModelJSON=ko.toJSON (self);
            var postData="json="+thisModelJSON;
            $.post("/app/updatesocialsettings",postData,
                function(data) {
                    clearMarkings();
                try
                {
                    // var jsonParsed = JSON.parse(data);
                    if(data.status.code=="200"){
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-success');
                        $("#formstatus").html(data.status.msg);

                        //reset
                        updateEmail=false;
                        imgemailbgs=[];



                    }
                    else{
                        var errors=data.status.errors;
                        var errorMsg='<ul>';
                        $.each(errors, function(name, value) {
                            //Highlight
                            $("#"+name).addClass('red');
                            //Error Msg
                            errorMsg+="<li>"+value+"</li>";
                        });
                        errorMsg+='</ul>';

                        //Print Errors
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-error');
                        $("#formstatus").html(errorMsg);
                    }

                }
                catch(err)
                {
                    var txt="Error description: " + err.message + "\n\n";
                    $("#formstatus").html(data);
                }
            });
        };
    }


    self.updateTplEmailClick = function() {
        var eventId=$('#eventSelect :selected').attr('id');
        var tplEmail= tinyMCE.get('emailcontent').getContent();
        var tplSubject= $('#emailSubject').val();
        var postData="eventId="+eventId+"&tplSubject="+tplSubject+"&tplEmail="+encodeURIComponent(tplEmail);
        processingNotif();
        $.post("/app/updatetplemail",postData,
            function(data) {
                clearMarkings();
                try
                {
                    // var jsonParsed = JSON.parse(data);
                    if(data.status.code=="200"){
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-success');
                        $("#formstatus").html(data.status.msg);

                        //reset
                       // updateEmail=false;
                       // imgemailbgs=[];
                        self.updateSettingsClick();
                    }
                    else{
                        var errors=data.status.errors;
                        var errorMsg='<ul>';
                        $.each(errors, function(name, value) {
                            //Highlight
                            $("#"+name).addClass('red');
                            //Error Msg
                            errorMsg+="<li>"+value+"</li>";
                        });
                        errorMsg+='</ul>';

                        //Print Errors
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-error');
                        $("#formstatus").html(errorMsg);
                    }

                }
                catch(err)
                {
                    var txt="Error description: " + err.message + "\n\n";
                    $("#formstatus").html(data);
                }
            });
        }

};




$(document).ready(function(){
        $('#eventSelect').on('change', function(){
            processCurrentSelection();
        });
        processCurrentSelection();

       // $('.tabbable').tabs();
    });


