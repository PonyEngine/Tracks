var LoginViewModel = function() {
    var self=this;
    self.usernameEmail = ko.observable("");
    self.password= ko.observable("");

    self.submitLogin = function() {
        $("#loginNotif").html("&nbsp; <span style='color:blue'>Authenticating ...<img src='/skin/images/smallSpinner.gif'/></span>");
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/account/requestlogin",postData,
            function(data) {
                $("#loginNotif").html("");
                if(data.status.code=="200"){
                    $("#loginNotif").html("&nbsp; <span style='color:blue'>Logging In ...<img src='/skin/images/smallSpinner.gif'/></span>");
                    if(window.top==window) {

                        // you're not in a frame so you reload the site
                        location.reload(); //reloads after 3 seconds
                    } else {
                        //you're inside a frame, so you stop reloading
                    }

                }
                else{
                    var errorMsg='';
                    var errors=data.status.errors;
                    $.each( errors, function(i, n){
                        errorMsg+=n+'<br />';
                    });


                    if(errorMsg.length >0){
                        $("#status").html(errorMsg);
                        $("#status").show();
                    }
                }
            });
    };


};

$(document).ready(function(){
    $("#status").hide();
    ko.applyBindings(new LoginViewModel($("#loginForm")[0])); // This makes Knockout get to work

});
