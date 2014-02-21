Check out your photo from {$dataArr.eventName}.

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
Trouble viewing this email? <a href="{$dataArr.fullURL}">Click here</a> to see your picture.
<br />
<br />
<table width="750" border="0" bgcolor="#000">
    <tr>
        <td width="30" rowspan="3">&nbsp;</td>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td width="270" valign="top" align="left">
        {if $dataArr.eventImage}
            <img style='border:10px solid #ffffff' src="{imagefilename fileObject=$dataArr.eventImage w=300 h=300}"/>
            {else}
            <img src="http://174.120.171.130/~dennis/heineken/rsa_image.png" style="border: 10px #fff solid" alt="rsa_image" width="264" height="382" />
        {/if}
        </td>
        <td align="left" valign="top"><div style="color:#fff;font-family:'Arial';"><h1 style="margin-top:0;">{$dataArr.eventName}</h1><p style="">{$dataArr.emailMsg}<br /><br />Your picture is ready to share!<br />
            <br /><a href="{$dataArr.facebookLink}"><img src="http://{$config->webhost}/skin/images/facebook_100x100.png" width="30" height="30" /></a>
            <a href="{$dataArr.twitterLink}"><img style="margin-left:5px;" src="http://{$config->webhost}/skin/images/twitter_100x100.png" width="30" height="30" /></a><br /><br /><a href="{$dataArr.facebookConnect}"><img src="http://{$config->webhost}/skin/images/flwus_hkn_225X72.png" /></a>
        </p></div></td>
        <td align="right" valign="bottom" style="margin:0;padding:0;"><img src="http://{$config->webhost}/skin/images/email/hkn-side.jpg" width="96" height="379" /></td>
    </tr>
</table>
</body>
</html>