$(document).ready(function(){
    window.fbAsyncInit = function() {
        FB.init({appId: '182794351917104', status: true, cookie: true, xfbml: true});
    };
    (function() {
        var e = document.createElement('script');
        e.type = 'text/javascript';
        e.src = document.location.protocol +
            '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
    }());

    function login(){
        var urlRef=$("#fbLogin").attr("url");
        document.location.href=urlRef;
    }
    function logout(){
        document.location.href = "/account/fblogout";
    }
    $("#appFbLogout").click(function() {
        FB.getLoginStatus(function(response) {
            if (response.status !== 'connected') {
                logout();
            }else{
                FB.logout(function(response) {
                    logout();
                });
            }
        });


    });
    $("#fbLogin").click(function() {
        FB.login(function(response) {
            login();
        }, {scope: 'email,publish_actions,offline_access,publish_stream'});
    });

});
