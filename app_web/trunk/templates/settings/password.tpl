{include file='header.tpl'}
<!-- Section Name -->
<div class="container-fluid">
    <br/>
    <div class="row-fluid">
        <div class="span2">
        {include file='settings/_menu.tpl'}
        </div>
        <div class="span9">
            <div class="row">
                <div class="span9">
                    <div class="form-horizontal" id="profileForm">
                        <div id="formstatus" class="">
                            &nbsp;
                        </div>
                        <fieldset class="well">
                            <div class="span9">
                                <h3>Password <small>Change your password</small></h3>
                                <br />
                                <br />
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="profile_currentPassword">Current Password</label>
                                <div class="controls">
                                    <input type="password"  id="profile_currentPassword" data-bind="value: currentPassword,event:{literal}{keydown: profileChanged()}{/literal}" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="profile_newPassword">New Password</label>
                                <div class="controls">
                                    <input type="password"  id="profile_newPassword" data-bind="value: newPassword,event:{literal}{keydown: profileChanged()}{/literal}" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="profile_confirmPassword">Confirm Password</label>
                                <div class="controls">
                                    <input type="password"  id="profile_confirmPassword" data-bind="value: confirmPassword,event:{literal}{keydown: profileChanged()}{/literal}" class="input-large"/>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="saveProfile"></label>
                                <div class="controls">
                                    <button id="saveProfile" data-bind='click: saveProfileClick,enable:profileHasChanged' class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{$config->url->js}settings/password.js"></script>
{include file='footer.tpl'}