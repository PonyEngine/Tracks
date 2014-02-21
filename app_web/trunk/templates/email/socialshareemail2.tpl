Check out your photo from {$dataArr.eventName}.
Trouble viewing this email? <a href="{$dataArr.fullURL}">Click here</a> to see your picture.

<br /><br />
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Red Star Access</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
</head>
<body topmargin="0" marginheight="0">
<div id="container" style="width: 100%; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000000;">
</div>
<div id="content-container" style="font-family: Arial, Helvetica, sans-serif; letter-spacing: -0px; float: left; width: 729px; background-color: #000000;">
    <div id="content" style="clear: left; float: left; display: inline; margin-top: 0; margin-right: 20px; margin-bottom: 0; margin-left: 4%; padding-top: 20px; padding-right: 0; padding-bottom: 20px; padding-left: 0; background-color: #000000;">
    {if $dataArr.eventImage}
        <img style='border:10px solid #ffffff' src="{imagefilename fileObject=$dataArr.eventImage w=300 h=300}"/>
        {else}
        <img src="http://174.120.171.130/~dennis/heineken/rsa_image.png" style="border: 10px #fff solid" alt="rsa_image" width="264" height="382" />
    {/if}
    </div>
    <div id="aside" style="float: left; width: 301px; display: inline; color: #fff; margin-top: 0; margin-bottom: 0; margin-left: 0; padding-top: 10px; padding-right: 0; padding-bottom: 10px; padding-left: 0;">
        <h1>{$dataArr.eventName}</h1>
        <p style="font-family: 'futura_std_condensedbold', Arial, sans-serif;">
            {$dataArr.emailMsg}<br /><br />
            Your picture is ready to share!<br /><br />
        </p>
        <a href="{$dataArr.facebookLink}"><img src="http://{$config->webhost}/skin/images/facebook_100x100.png" alt="facebook" width="30" height="30" /></a>
        <a href="{$dataArr.twitterLink}"><img src="http://{$config->webhost}/skin/images/twitter_100x100.png" alt="twitter" width="29" height="29" /></a><br /><br />
        <a href="{$dataArr.facebookConnect}"><img src="http://{$config->webhost}/skin/images/flwus_hkn.png" alt="follow us on facebook" width="299" height="96" /></a>
    </div>
    <div style="float:left" width: "115px;"><img src="http://{$config->webhost}/skin/images/email/hkn-side.jpg" alt="rsa_image" width="114" height="500" /> </div>
</div>
</div>
</body>
</html>