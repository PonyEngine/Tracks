<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
    <title>{$config->title} {if $config->version->num}({$config->version->num}){/if}{if $header.title!=''}-{/if}{$header.title|escape}</title>
    <meta name="description" content="Tracks app">
    <meta name="author" content="Tracks team">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="/favicon.ico" />
    <script type="text/javascript" src="{$config->url->js}_jquery/jquery1.7.2.js"></script>
    <link href="{$config->url->css}_bootstrap/bootstrap.css" rel="stylesheet">
    <link href="{$config->url->css}_bootstrap/bootstrap-responsive.css" rel="stylesheet">
    <meta charset="utf-8">
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!--<script type="text/javascript" src="{$config->url->js}_social/fbLoginBtn.js"></script>-->
    <script type="text/javascript" src="{$config->url->js}fbconnect.js"></script>
    <script type="text/javascript" src="{$config->url->js}_knockout/knockout-2.0.0.js"></script>
    <script type="text/javascript" src="{$config->url->js}_knockout/knockout.mapping.js"></script>
    <script type="text/javascript" src="{$config->url->js}json2.js"></script>
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
</head>
<body>
<div class="container">
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"><a class="brand" href="/">
                        <img src="/skin/images/logo.png" alt="LOGO_BETONIT" width="40" height="40" style="padding:0px;" />&nbsp;</a>
                     </span>
                </a>
            </div>
        </div>
    </div>
    <br />
    <br />
    <br />
    <br />
    <br />
    <div class="span3 well offset1">
        <div class="form-horizontal" id="loginForm">
            <input id="usernameEmail" data-bind="value:usernameEmail" placeholder="Username or email" class="input-large"/>
            <br /><br />
            <input style="float: left;" id="password" type="password" data-bind="value:password" class="input-medium" placeholder="Password"/>
            <button style="float:left; margin-left:5px; " id="submitLogin" data-bind='click: submitLogin' class="btn btn-primary">Sign in</button><span id="loginNotif"></span>
        </div>
        <div id="fb-root"></div>
        <div class="control-group">
            <label class="control-label" for="status"></label>
            <div class="controls">
                <div id="status" style="display:none;" class="alert alert-error input-medium"">
            </div>
        </div>
        <p>
            <br /> <br /> <br />
            <a id="fbLogin" href="{literal}javascript:void(0);{/literal}" url="/account/fblogin"><img src="/skin/images/facebook-connect.png" alt="Facebook Connect" style="height:85%;width:85%" /></a>
        </p>
        <p>
            or <a href="/account/register">Sign Up</a> with Your Email
        </p>
    </div>
    <footer>
        <script type="text/javascript" src="{$config->url->js}/_analytics/google.js"></script>
        <script type="text/javascript" src="{$config->url->js}account/login.js"></script>
        <script type="text/javascript" src="{$config->url->js}_bootstrap/bootstrap-button.js"></script>
        <script type="text/javascript" src="{$config->url->js}_bootstrap/bootstrap-dropdown.js"></script>
    </footer>
    <span id="js" fbappid="{$config->facebook->appid}"/>
</div>
</body>
</html>