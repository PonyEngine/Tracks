var theEditUserForm=null;
var aUserSearchVM=null;
function clearMarkings(){
    $("#formstatus").removeClass();
    $("#formstatus").html("");

    //Clear red markings
    $.each($("#formFields"), function(name, value){
        $(name).removeClass('red');
    });
}

function clearSearchStatusMarkings(){
    $("#searchstatus").removeClass();
    $("#searchstatus").html("");

    //Clear red markings
   $(".red").removeClass('red');
}

function processingNotif(){
    $("#formstatus").addClass('alert');
    $("#formstatus").addClass('alert-info');
    $("#formstatus").html("<img src='/skin/images/smallSpinner.gif'/> Processing");
}

var UserViewModel = function() {
    var self=this;
    self.editUserId = ko.observable();
    self.editEmail = ko.observable();
    self.editProfilename = ko.observable();
    self.edit_password = ko.observable();
    self.edit_confirmPassword = ko.observable();
    self.editExpiry = ko.observable();

    self.saveUserClick = function() {
            //clearMarkings();
            //processingNotif();
        $(".alert").removeClass('alert');
        $(".alert-success").removeClass('alert-success');

        if ($("#editBrandNum").length > 0){
            self.editBrandNum=$('#editBrandNum :selected').attr('value');
        }

            var thisModelJSON=ko.toJSON (self);
            var postData="json="+thisModelJSON;

        $("#editFormstatus").addClass('alert');
        $("#editFormstatus").addClass('alert-primary');
        $("#editFormstatus").html("Processing ....");
            $.post("/dashboard/updateuser",postData,
                function(response) {
                    try{

                        if(response.status.code=="200"){
                           // clearMarkings();
                            //Status
                            $("#editFormstatus").addClass('alert');
                            $("#editFormstatus").addClass('alert-success');
                            $("#editFormstatus").html(response.status.msg);
                            if(aUserSearchVM)aUserSearchVM.searchUsersClick();
                        }
                        else{
                            //clearMarkings();
                            var errors=response.status.errors;
                            var errorMsg='<ul>';
                            $.each(errors, function(name, value) {
                                //Highlight
                                $("#"+name).addClass('red');
                                //Error Msg
                                errorMsg+="<li>"+value+"</li>";
                            });
                            errorMsg+='</ul>';

                            //Print Errors
                            $("#editFormstatus").addClass('alert');
                            $("#editFormstatus").addClass('alert-error');
                            $("#editFormstatus").html(errorMsg);
                            //Highlight trouble areas
                        }
                    }catch(err){
                        $("#editFormstatus").html('<button class="close" data-dismiss="alert">×</button>'+response);
                    }
                });
        };
}


var BrandUserViewModel = function(fullName,aProfileName) {
    var self=this;
    self.brandId = ko.observable();
    self.brandName = ko.observable();
    self.emailAddress = ko.observable();
    self.username = ko.observable();
    self.password = ko.observable();
    self.confirmPassword = ko.observable();
    self.userExpiry = ko.observable();
    self.profileHasChanged=ko.observable(false);
    self.createUser= ko.observable(true);
    self.selectUser= ko.observable(false);

    self.createBrandOrUserClick = function() {
        //Removal here
        clearMarkings();
        processingNotif();
        $("#formstatus").addClass('alert');
        $("#formstatus").addClass('alert-success');

        if ($("#selectBrand").length > 0){
            self.brandId=$('#selectBrand :selected').attr('id');
        }
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/dashboard/createbrandandoruser",postData,
            function(data) {
                try{
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        clearMarkings();
                        //Status
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-success');
                        $("#formstatus").html(jsonParsed.status.msg);

                        $("#emailAddress").val("");
                        $("#username").val("");
                        $("#password").val("");
                        $("#confirmPassword").val("");

                        $("#brandName").val("");
                        $("#userExpiry").val("");
                    }
                    else{
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
                    $("#formstatus").html('<button class="close" data-dismiss="alert">×</button>'+data);
                }
            });
    };

    self.profileChanged = function() {
        //self.profileHasChanged($("#profileModel").attr("profilename")!=$("#profile_profileName").val());
        self.profileHasChanged(true);
    };

};

var BrandEditViewModel = function(fullName,aProfileName) {
    var self=this;
    self.brandId = null;
    self.brandName = ko.observable();
    self.emailAddress = null;
    self.username =null;
    self.password =null;
    self.confirmPassword =null;
    self.userExpiry = null;
    self.profileHasChanged=null;
    self.createUser=false;
    self.selectUser= false;

    self.createBrandClick = function() {
        //Removal here
        $("#brandedit_formstatus").addClass('alert');
        $("#brandedit_formstatus").addClass('alert-info');
        $("#brandedit_formstatus").html("<img src='/skin/images/smallSpinner.gif'/> Processing");

        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/dashboard/createbrandandoruser",postData,
            function(data) {
                try{
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        clearMarkings();
                        //Status- Place status in edit box
                        $("#brandedit_formstatus").addClass('alert');
                        $("#brandedit_formstatus").removeClass('alert-info');
                        $("#brandedit_formstatus").addClass('alert-success');
                        $("#brandedit_formstatus").html(jsonParsed.status.msg);

                        $("#editBrandName").val("");

                        //Add  this brand name and brandId to the selection list

                    }
                    else{
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
                        $("#brandedit_formstatus").addClass('alert');
                        $("#brandedit_formstatus").addClass('alert-error');
                        $("#brandedit_formstatus").html(errorMsg);
                        //Highlight trouble areas
                    }
                }catch(err){
                    $("#brandedit_formstatus").html('<button class="close" data-dismiss="alert">×</button>'+data);
                }
            });
    };
};


var UserSearchViewModel = function(fullName,aProfileName) {
    var self=this;
    self.userId = ko.observable();
    self.userName = ko.observable();
    self.email = ko.observable();
    self.mLevel = ko.observable();
    self.profileName = ko.observable();
    self.createdFrom = ko.observable();
    self.createdTo = ko.observable();
    self.lastFrom=ko.observable();
    self.lastTo=ko.observable();
    self.expireFrom=ko.observable();
    self.expireTo=ko.observable();

    self.searchUsersClick = function() {
        //Removal here
        clearSearchStatusMarkings();
        $('#recordsCount').html('0');
        $("#listofusers").html("<td colspan='9'><img src='/skin/images/smallSpinner.gif'/> Searching ...</td>");
        $("tr.founduser").remove();

        if ($("#mLevel").length > 0){
            if($('#mLevel :selected').attr('id')!=0){
                var theId=$('#mLevel :selected').attr('id');
                self.mLevel=theId;
            }else{
                self.mLevel=null;
            }
        }

        if ($("#userBrand").length > 0){
            if($('#userBrand :selected').attr('id')!=0){
                var theId=$('#userBrand :selected').attr('id');
                self.brandId=theId;
            }else{
                self.brandId=null;
            }
        }

        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;


        $.post("/dashboard/searchusers",postData,
            function(response) {
                $("#listofusers").html("");
                try{
                    if(response.status.code=="200"){
                        var searchtr=$('#listofusers');
                        var recordsCount=response.data.founduserCount;
                        $('#recordsCount').html(recordsCount);
                        $.each(response.data.foundusers, function(name, value) {
                            //userEdit
                            var expiry=(typeof value.tsExpire != 'undefined')?value.tsExpire:'0';

                            var uEdit='editemail="'+value.usernameEmail+'" ';
                                uEdit=uEdit+'editexpiry="'+expiry+'" ';
                                uEdit=uEdit+'editprofilename="'+value.profileName+'" ';
                                uEdit=uEdit+'role="'+value.user_type+'" ';
                                uEdit=uEdit+'edituserId="'+value.userId+'" ';
                                    //'+expiry+'" ';

                           var trHTML='<tr class="founduser">'+
                               //'<td><input type="checkbox" /></td>'+
                               //'<td>&nbsp;</td>'+
                               '<td>'+value.userId+'</td>'+          //ID
                               '<td><img src="'+value.picURL+'" height=40px; width=40px; /></td>'+  //Pic
                                '<td>'+value.usernameEmail+'</td>'+     //Email
                                '<td>'+value.profileName+'</td>'+       //Profile Name
                                '<td>'+value.user_type+'</td>'+         //Member Level
                               '<td>'+value.tsCreated+'</td>'+         //Created
                               '<td>'+value.tsLastLogin+'</td>'+         //Last Sign In
                               '<td><b>'+value.tsExpire+'</b></td>'+           //Expiration
                               '<td>'+'<a href="#editUserModal" class="edituser"  data-toggle="modal" '+uEdit+'>Edit</a></td>'+
                               '</tr>';
                            //tableHTML=tableHTML+trHTML;
                            $(trHTML).insertAfter(searchtr);

                        });

                        $('.edituser').on('click', function(){
                            theEditUserForm.editEmail($(this).attr('editemail'));
                            theEditUserForm.editProfilename($(this).attr('editprofilename'));
                            theEditUserForm.editUserId($(this).attr('edituserId'));


                            //theEditUserForm.editBrandNum
                            var expiry=$(this).attr('editexpiry');
                            theEditUserForm.editExpiry(expiry);
                       });
                    }
                    else{
                       // clearMarkings();
                        var errors=response.status.errors;
                        var errorMsg='<ul>';
                        $.each(errors, function(name, value) {
                            //Highlight
                            $("#"+name).addClass('red');
                            //Error Msg
                            errorMsg+="<li>"+value+"</li>";
                        });
                        errorMsg+='</ul>';

                        //Print Errors
                        $("#searchstatus").addClass('alert');
                        $("#searchstatus").addClass('alert-error');
                        $("#searchstatus").html(errorMsg);
                    }
                }catch(err){
                    $("#searchstatus").html('<button class="close" data-dismiss="alert">×</button>'+response);
                }
            });
    };

    self.profileChanged = function() {
        //self.profileHasChanged($("#profileModel").attr("profilename")!=$("#profile_profileName").val());
        self.profileHasChanged(true);
    };

};




$(document).ready(function(){
   //  ko.applyBindings( new BrandUserViewModel(), document.getElementById("branduserForm"));
     //ko.applyBindings( new BrandEditViewModel(), document.getElementById("editBrandForm"));


     aUserSearchVM=new UserSearchViewModel();
     ko.applyBindings(aUserSearchVM, document.getElementById("usersearchForm"));

    var formHTML=document.getElementById("editUserForm");
    theEditUserForm=new UserViewModel();
    ko.applyBindings(theEditUserForm ,formHTML );

    aUserSearchVM.searchUsersClick();


  /*  $('#createUserCheckBox').on('click', function(){
        if($('#createUserCheckBox').is(':checked')){
            $("#createUserPanel").show('slow');
            $("#brandSubmission").hide();
        }else{
            $("#createUserPanel").hide('slow');
            $("#brandSubmission").show();
        }
    });

    $('#useNewBrand').on('click', function(){
        $("#brandSelection").hide();
        $("#brandCreation").show();
    });

    $('#useBrandSelection').on('click', function(){
        $("#brandSelection").show();
        $("#brandCreation").hide();
        $("#brandCreation").val("");

        //brandName
        $("#brandName").val("");
    });

    /* $('#dp1').datepicker({
        format: 'mm/dd/yyyy'
    });*/

   /* $('#dp1').datepicker()
    .on('changeDate', function(ev){
        if (ev.date.valueOf() < 1){

        }else{
            $('#userExpiry').val(ev.date.valueOf());
        }
    });*/

});
