{include file='header.tpl'}
<!-- Section Name -->
<div class="container-fluid">
    <br />
    <div class="row-fluid">
        <div class="span2">
        {include file='settings/_menu.tpl'}
        </div>
        <div class="span10">
            <div class="row">
                <div class="span9">
                    <div class="form-horizontal" id="profileForm">
                        <div id="formstatus" class="">
                            &nbsp;
                        </div>
                        <fieldset class="well">
                            <div class="span9">
                                <h3>Profile Detail <small>Upload your information.</small></h3>
                                <br />
                                <br />
                            </div>
                            {*<div class="control-group">
                                <label class="control-label" for="imageform">Picture</label>
                                <div class="controls">
                                {if $user->logoImage}
                                    <img id="imageform" src="{imagefilename fileObject=$user->logoImage w=150 h=150}" alt="{$user->logoImage->filename}" />
                                    {else}
                                    <form id="imageform" method="post" enctype="multipart/form-data" action='/utility/fileupload'>
                                        <input type="hidden" name="width" value="150" />
                                        <input type="hidden" name="height" value="150"  />
                                        <input type="hidden" name="postname" value="photoimg"/>
                                        <input type="hidden" name="fileType" value="image_user"/>
							<span class="btn btn-success fileinput-button">
        						<span>Choose File</span>
								<input type="file" name="photoimg" id="photoimg" />
							</span>
                                    </form>
                                    <div id='preview'>
                                    </div>
                                {/if}
                                    <input type="hidden" id="profile_profileTmpImagPath" data-bind="value: profileTmpImagPath" class="input-xlarge"/>
                                </div>
                            </div>*}
                            <div class="control-group">
                                <label class="control-label" for="profile_fullname">Contact Name</label>
                                <div class="controls">
                                    <input id="profile_fullname" data-bind="value: fullName,event:{literal}{keydown: profileChanged()}{/literal}" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="usernameEmail">Email Address</label>
                                <div class="controls">
                                    <input id="usernameEmail" data-bind="value: usernameEmail,event:{literal}{keydown: profileChanged()}{/literal}" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="phone10">Contact Phone Number</label>
                                <div class="controls">
                                    <input id="phone10" data-bind="value: phone10,event:{literal}{keydown: profileChanged()}{/literal}" placeholder="(310) 555-1234" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="saveProfile"></label>
                                <div class="controls">
                                    <button id="saveProfile" data-bind='click: saveProfileClick,enable:profileHasChanged' class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="errorMsg">

</div>
<span id="profileModel" fullname="{$fullName}" usernameEmail="{$usernameEmail}" phone10="{$phone10}" />
<script type="text/javascript" src="{$config->url->js}settings/profile.js"></script>

{include file='footer.tpl'}