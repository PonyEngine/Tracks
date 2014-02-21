{include file='header.tpl'}
<!-- Section Name -->
<div class="page-header">
    <h1>New Campaign <small>Create and send new campaign</small></h1>
</div>
<div class="row">
    <div class="span12">
        <div class="form-horizontal" id="campaignForm">
            <fieldset>
                <div id="legend" class="">
                </div>
                <div class="control-group">
                    <label class="control-label" for="campaign_intro">Campaign Introduction</label>
                    <div class="controls">
                        <input id="campaign_intro" data-bind="value: campaignIntro" class="input-xlarge"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="campaign_title">Title</label>
                    <div class="controls">
                        <input id="campaign_title" data-bind="value:campaignTitle" class="input-xlarge"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="campaign_descr">Description</label>
                    <div class="controls">
                        <textarea id="campaign_descr" data-bind="value: campaignDescription" class="input-xlarge" rows="3" columns="100" ></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="campaign_url">URL</label>
                    <div class="controls">
                        <input id="campaign_url" data-bind="value: campaignURL" class="input-xlarge" placeholder="http://tracks.ponyengine.com"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="imageformb">Image</label>
                    <div class="controls">
                        <form id="imageformb" method="post" enctype="multipart/form-data" action='/dashboard/imageupload'>
                            <input type="hidden" name="width" value="150" />
                            <input type="hidden" name="height" value="150"  />
                            <input type="hidden" name="postname" value="photoimgb"/>
                            <input type="hidden" name="fileType" value="1"/>
							<span class="btn btn-success fileinput-button">
        						<span>Add Image</span>
								<input type="file" name="photoimgb" id="photoimgb" />
							</span>
                        </form>
                        <div id='previewb'>
                        </div>
                        <input type="hidden" id="campaign_tmpImagePath" value="" class="input-xlarge"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="previewOnWall">See what friends will receive</label>
                    <div class="controls">
                        <button id="previewOnWall" data-bind='click: previewOnWallClick' class="btn btn-default">Preview on your Facebook Wall</button>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="sendCampaign">Send Campaign</label>
                    <div class="controls">
                        <button id="sendCampaign" data-bind='click: sendCampaignClick' class="btn btn-primary">Send</button>
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