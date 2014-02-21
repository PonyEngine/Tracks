function clearMarkings(){
    $("#formstatus").removeClass();
    $("#formstatus").html("");

    //Clear red markings
    $.each($("#formFields"), function(name, value){
        $(name).removeClass('red');
    });

}
function processingNotif(){
    $("#formstatus").addClass('alert');
    $("#formstatus").addClass('alert-info');
    $("#formstatus").html("<img src='/skin/images/smallSpinner.gif'/> Processing");
}

var ProfileViewModel = function() {
    var self=this;
    self.currentPassword = ko.observable();
    self.newPassword = ko.observable();
    self.confirmPassword = ko.observable();
    self.profileHasChanged=ko.observable(false);

    self.saveProfileClick = function() {
        processingNotif();
        self.profileTmpImagPath=$('#profile_profileTmpImagPath').val();
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/settings/changePassword",postData,
            function(data) {
                var jsonParsed = JSON.parse(data);
                if(jsonParsed.status.code=="200"){
                    clearMarkings();
                    $("#formstatus").addClass('alert');
                    $("#formstatus").addClass('alert-success');
                    $("#formstatus").html(jsonParsed.status.msg);
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
                }
            });
    };

    self.profileChanged = function() {
        //self.profileHasChanged($("#profileModel").attr("profilename")!=$("#profile_profileName").val());
        self.profileHasChanged(true);
    };

};


$(document).ready(function(){
    ko.applyBindings(new ProfileViewModel($("#profileForm")[0])); // This makes Knockout get to work

});
