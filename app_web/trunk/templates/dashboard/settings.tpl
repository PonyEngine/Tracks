{include file='header.tpl'}
<!-- Section Name -->
<div class="page-header">
    <h1>Profile Detail <small>Setup your profile details</small></h1>
</div>
<div class="row">
    <div class="span12">
        <div class="form-horizontal" id="profileForm">
            <fieldset>
                <div id="profileLegend" class="">
                </div>
                <div class="control-group">
                    <label class="control-label" for="profile_fullName">&nbsp;</label>
                    <div class="controls">
                        <span id="profile_fullName" style="float:left" data-bind="text: fullName" class="input-xlarge"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="profile_profileName">Profile Name</label>
                    <div class="controls">
                        <input id="profile_profileName" data-bind="value: profileName,event:{literal}{keydown: profileChanged()}{/literal}" class="input-xlarge"/>
                        <p>http://tracks.ponyengine.com/@<span data-bind="text: profileName,event:{literal}{blur: profileChanged()}{/literal}"></span>
                        </p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="imageform">Profile Picture</label>
                    <div class="controls">
                    {if $user->logoImage}
                        <img id="imageform" src="{imagefilename fileObject=$user->logoImage w=150 h=150}" alt="{$user->logoImage->filename}" />
                        {else}
                        <form id="imageform" method="post" enctype="multipart/form-data" action='/dashboard/imageupload'>
                            <input type="hidden" name="width" value="150" />
                            <input type="hidden" name="height" value="150"  />
                            <input type="hidden" name="postname" value="photoimg"/>
                            <input type="hidden" name="fileType" value="0"/>
							<span class="btn btn-success fileinput-button">
        						<span>Add Your Logo</span>
								<input type="file" name="photoimg" id="photoimg" />
							</span>
                        </form>
                        <div id='preview'>
                        </div>
                    {/if}
                        <input type="hidden" id="profile_profileTmpImagPath" data-bind="value: profileTmpImagPath" class="input-xlarge"/>
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
<span id="profileModel" fullname="{$user->profile->up1} {$user->profile->up3}" profilename="{$user->profileName}" />
<script type="text/javascript" src="{$config->url->js}dashboard/index.js"></script>
{include file='footer.tpl'}