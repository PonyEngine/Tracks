var ProfileViewModel = function(profileName,profileEmail) {
    var self=this;
    self.profileName = ko.observable(profileName);
    self.profileEmail = ko.observable(profileEmail);
    self.profileHasChanged=ko.observable(false);
    self.saveProfileClick = function() {
        var thisModelJSON=ko.toJSON (self);
        //alert(thisModelJSON);
        var postData="profile="+thisModelJSON;
        $.post("/dashboard/saveprofile",postData,
            function(data) {
                var jsonParsed = JSON.parse(data);
                if(jsonParsed.status.code=="200"){
                    var newProfileName=jsonParsed.data.profileName;
                    $("#profileModel").attr('profilename',newProfileName);
                    alert(jsonParsed.status.msg);
                }
                else{
                    if(jsonParsed.data.profileName.length>0){
                        $("#profile_profileName").val(jsonParsed.data.profileName);
                    }
                }
            });
    };

    self.profileChanged = function() {
        //self.profileHasChanged($("#profileModel").attr("profilename")!=$("#profile_profileName").val());
        self.profileHasChanged(true);
    };

};

$(document).ready(function(){
    //alert($("#profileModel").attr("profilename"));
    ko.applyBindings(new ProfileViewModel($("#profileModel").attr("profilename"),$("#profileModel").attr("profileemail")),$("#profileForm")[0]); // This makes Knockout get to work

    $('#profile_profileName').keydown(function(event) {
        //alert('You pressed '+event.keyCode);
        thisProfileViewModel.profileHasChanged($("#profileModel").attr("profileName")!=$("#profile_profileName").val());
    });

    $('.publishToFB').on('click', function() {
        var campaignData="campaignId="+$(this).attr("campId");
        var campId=$(this).attr("id");
        $("#"+campId).hide();
        $("#th"+campId).addClass("buttonLoader");
        $.post("/dashboard/publishtofacebook",campaignData,
            function(data) {
                try
                {
                    var jsonParsed = JSON.parse(data);
                    $("#th"+campId).removeClass("buttonLoader");
                    if(jsonParsed.status.code=="200"){
                        $("#"+campId).replaceWith(jsonParsed.data.publishDate);
                        $(this).hide();
                    }else{
                        $("#"+campId).show();
                        alert ("Could Not Publish and will state reason");
                    }

                }
                catch(err)
                {
                    $("#"+campId).show();
                    $("#th"+campId).removeClass("buttonLoader");
                    var txt="Error description: " + err.message + "\n\n";
                }
            });
    });


    $('#photoimg').on('change', function(){
        $("#preview").html('<img src="/skin/images/ajax-loader.gif" alt="Uploading...."/>');
        $("#imageform").hide();
        var options={
            success: function(data, statusText, xhr, $form){
                try
                {
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        var imgHTML="<img src='"+jsonParsed.data.imageName+"' alt=''/>";
                        $("#preview").html(imgHTML);
                        $('#profile_profileTmpImagPath').val(jsonParsed.data.imageName);
                    }else{
                        $("#imageform").show();
                    }
                }catch(err){
                    alert("Cannot preview image");
                    $("#imageform").show();
                    $("#preview").html('');
                }
            }

        };
        $("#imageform").ajaxForm(options).submit();
    });

    $('#photoimgb').on('change', function(){
        $("#imageformb").hide();
        $("#previewb").html('<img src="/skin/images/ajax-loader.gif" alt="Uploading...."/>');
        var options={
            success: function(data, statusText, xhr, $form){
                //alert(data);
                try
                {
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        var imgHTML="<img src='"+jsonParsed.data.imageName+"' alt='uploadedImage'/>";
                        $("#previewb").html(imgHTML);
                        $('#campaign_tmpImagePath').val(jsonParsed.data.imageName);
                    }else{
                        $("#imageformb").show();
                    }
                }catch(err){
                    alert("Cannot preview image");
                    $("#imageformb").show();
                    $("#previewb").html('');
                }
            }
        };
        $("#imageformb").ajaxForm(options).submit();
    });



});
