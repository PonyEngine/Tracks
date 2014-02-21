
var UserViewModel = function() {
    var self=this;
    self.fullname = ko.observable("");
    self.email = ko.observable("");
    self.password= ko.observable("");
    self.username = ko.observable("");

    self.signup = function() {
        $("#notifcation").html("&nbsp; <span style='color:blue'>Authenticating ...<img src='/skin/images/smallSpinner.gif'/></span>");
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/account/requestregister",postData,
            function(data) {
                $("#notifcation").html("");
                if(data.status.code=="200"){
                    $("#notifcation").html("&nbsp; <span style='color:blue'>Logging In ...<img src='/skin/images/smallSpinner.gif'/></span>");
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
    ko.applyBindings(new UserViewModel($("#signupForm")[0])); // This makes Knockout get to work

});
