var ProfileViewModel = function(fullName,aProfileName) {
    var self=this;
    self.fullName = fullName;
    self.profileName = ko.observable(aProfileName);
    self.profileHasChanged=ko.observable(false);
    self.profileTmpImagPath= ko.observable("");

    self.saveProfileClick = function() {
        self.profileTmpImagPath=$('#profile_profileTmpImagPath').val();
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

var CampaignViewModel = function() {
    var self=this;
    self.campaignIntro = ko.observable();
    self.campaignTitle = ko.observable();
    self.campaignDescription = ko.observable();
    self.campaignURL = ko.observable();
    self.campaignTmpImagPath= ko.observable("");

    self.previewOnWallClick = function() {
        if(self.validateCampaignForm()){
            self.campaignTmpImagPath=$('#campaign_tmpImagePath').val();
            var thisModelJSON=ko.toJSON (self);
            var campaignData="campaign="+thisModelJSON;
            $.post("/dashboard/prepostcampaigntowall",campaignData,
                function(data) {
                    //alert(data);
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        window.open("https://www.facebook.com/profile.php");
                    }else{
                        alert ("Could Not Add Campaign TO Wall");
                    }
                });
        }

    };

    self.sendCampaignClick = function() {
        if(self.validateCampaignForm()){
            self.campaignTmpImagPath=$('#campaign_tmpImagePath').val();
            var thisModelJSON=ko.toJSON (self);
            //alert(thisModelJSON);
            var campaignData="campaign="+thisModelJSON;
            $.post("/dashboard/savecampaign",campaignData,
                function(data) {
                    //alert(data);
                    //If success then delete the old data
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        alert ("Your Campaign has been sent");
                        $("#campaign_intro").val("");
                        $("#campaign_title").val("");
                        $("#campaign_descr").val("");
                        $("#campaign_url").val("");
                        $("#imageformb").show();
                        $("#previewb").html('');

                        var theCampId=$("#campList tr:last").attr("campId");
                        self.getNewCampaignsAfterCampaignId(theCampId,"#campList");
                    }else{
                        alert ("Could Not Save this Campaign");
                    }

                });
        }
    };

    self.validateCampaignForm = function() {
        if($("#campaign_intro").val().length <1){
            alert("Campaign Intro information is required.");
            return false;
        }
        if ($("#campaign_title").val().length <1){
            alert("Campaign Title information is required.");
            return false;
        }
        if ($("#campaign_descr").val().length <1){
            alert("Campaign Description information is required.");
            return false;
        }
        if ($("#campaign_url").val().length <1){
            alert("Campaign Url is required.");
            return false;
        }else{
            var urlregex = new RegExp(
                "^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)");
            if(!urlregex.test($("#campaign_url").val())){
                alert("Please enter a valid url");
                return false;
            }

        }
        return true;
    };

    self.getNewCampaignsAfterCampaignId = function(campaignId,tableId) {
        var lastId=campaignId;
        var campaignData="campaignid="+lastId;
        $.post("/dashboard/getcampaignsaftercampaignid",campaignData,
            function(data) {
                var jsonParsed = JSON.parse(data);
                if(jsonParsed.status.code=="200"){
                    var newCampaigns='';
                    var theCampaigns=jsonParsed.data.campaigns;

                    $.each(theCampaigns, function(index, aCampaign) {
                        newCampaigns=newCampaigns+
                            "<tr campId='"+aCampaign.campId+"'>" +
                            "<td><img src='"+aCampaign.img_link+"' height='25' width='25' alt='"+aCampaign.img_altname+"'></a></td>" +
                            "<td><a href='"+aCampaign.campaignProfileName+"'>"+aCampaign.name+"</a></td>" +
                            "<td>"+aCampaign.supporters+"</td>"+
                            "<th>"+"<span "+(aCampaign.yes>0?"class='badge badge-success'>":'>')+
                            aCampaign.yes+"</span></th>"+
                            "<th>"+"<span "+(aCampaign.no>0?"class='badge badge-error'>":'>')+
                            aCampaign.no+"</span></th>"+
                            "<th>"+aCampaign.sent+"</th><th>"+
                            (aCampaign.published.length>0?aCampaign.published:
                                "<button id='camp"+aCampaign.campId+" campId='"+aCampaign.campId+"' class='publishToFB btn btn-success'>Publish to Facebook</button>")+
                            "</th></tr>";
                    });

                    $(tableId+" tr:last").after(newCampaigns);
                }else{
                    alert ("Could Not Save this Campaign");
                }
                return null;
            });
    };


};


$(document).ready(function(){
    ko.applyBindings(new ProfileViewModel($("#profileModel").attr("fullname"),$("#profileModel").attr("profilename")),$("#profileForm")[0]); // This makes Knockout get to work
    ko.applyBindings(new CampaignViewModel(),$("#campaignForm")[0]);

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

    //Pagination of table
    var pageNavId='pageNavPosition';
    var tableListId='campList';
    var pager = new Pager(tableListId, 10);
    pager.init();
    pager.showPageNav('pager', 'pageNavPosition');
    if(pager.pages >0){
        pager.showPage(pager.pages);
    }else{
        $("#"+pageNavId).hide();
        $("#"+tableListId).hide();
    }
    $('.pagenum').on("click",function() {
        var pageNum=$(this).attr("pagenum");
        pager.showPage(pageNum);
    });

    $('.pagenumprev').click(function() {
        pager.prev();
    });
    $('.pagenumnext').click(function() {
        pager.next();
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
