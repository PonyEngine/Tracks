{include file='header.tpl'}
<div class="container-fluid span7 offset2">
    <br/><br/>
    <div class="span6 well">
        <div class="form-horizontal" id="signupForm">
            <div class="control-group">
                <label class="control-label" for="fullname">Full Name</label>
                <div class="controls">
                    <input id="fullname" data-bind="value:fullname" placeholder="Full Name" class="input-large"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Email Address</label>
                <div class="controls">
                    <input id="email" data-bind="value:email" placeholder="Email Address" class="input-large"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Create a Password</label>
                <div class="controls">
                    <input style="float: left;" id="password" type="password" data-bind="value:password" class="input-medium" placeholder="Password"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Choose Username</label>
                <div class="controls">
                    <input style="float: left;" id="username" type="input" data-bind="value:username" class="input-medium" placeholder=""/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">&nbsp;</label>
                <div class="controls">
                    <button style="float:left; margin-left:5px; " id="signup" data-bind='click: signup' class="btn btn-primary">Sign Up</button><span id="notification"></span>
                </div>
            </div>
            <div class="control-group">
            </div>
            <div id="fb-root"></div>
            <div class="control-group">
                <label class="control-label" for="status"></label>
                <div class="controls">
                    <div id="status" style="display:none;" class="alert alert-error input-medium"">
                </div>
            </div>
            <p>
                <a id="fbLogin" href="#" url="/account/fblogin"><img src="/skin/images/facebook-connect.png" alt="Facebook Connect" style=";width:200px;" /></a>
            </p>
            <p>
                Already have an account?&nbsp;<a href="/account/login">Log In</a>
            </p>
        </div>
        <footer>
            <script type="text/javascript" src="{$config->url->js}account/register.js"></script>
        </footer>
        <span id="js" fbappid="{$config->facebook->appid}"/>
    </div>
</div>
