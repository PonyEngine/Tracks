{include file='header.tpl'}
<br />
<br />
<div class="page-header">
    <h1>API Calls <small>Select an API<br/></small><span style="font-size:12px;" id="currentApiString"></span></h1>
    <div style="font-size:12px;display:none;" id="currentErrorMsg"class="alert alert-error">
    </div>
</div>
<div class="row">
    <div class="span12">
    <div class="span5">
        <div class="form-horizontal" id="formForApi">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="currentSession">Current Session</label>
                    <div id="currentSession" class="controls">
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="apiCalls">Select an API Call</label>
                    <div id="apiCalls" class="controls">
                           <select id="apiCallsSelect" >
                           {foreach from=$restCalls key=apiIdName item=apiObject }
                               <option id="{$apiObject->getId()}" apiString="{$apiObject->apicall_string}" calltype="{$apiObject->apicall_type}" responsetype="{$apiObject->apiresponse_type}">{$apiObject->apicall_name}</option>
                            {foreachelse}
                               No items were found in the search
                           {/foreach}
                        </select>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                ACTION/CONTROLLER:<span id="currentControllerAction"></span>
            </fieldset>
            <fieldset>
                METHOD:<span id="currentMethod"></span>
            </fieldset>
            <fieldset>
                <div id="getGeo" class="control-group" style="display: none;">
                    <label class="control-label" for="getGeoBtn">&nbsp;</label>
                    <div  class="controls">
                        <button id="getGeoBtn" class="btn">Get Current Location</button>
                        <span id="geoStatus"></span>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <div class="control-group">
                    <div class="row">
                        <div class="span12">
                                <form id="apiSubmitForm" method="">
                                     <div id="apiForm">
                                    </div>
                                </form>
                                 <div class="control-group">
                                     <label class="control-label" for="submitApiForm">&nbsp;</label>
                                     <div class="controls">
                                         <button id="submitApiForm"  class="btn btn-success">Submit API</button>
                                     </div>
                                 </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
     </div>
     <div id="jsonSection" class="span6">
        <div class="row">
            <div class="span2">&nbsp;</div>
            <div id="JSONViews" class="btn-group span3" data-toggle="buttons-radio">
                <button id="prettyTableJSON" class="btn btn-primary viewToggles">TableView</button>
                <button id="prettyTextJSON" class="btn btn-primary viewToggles">TextView</button>
            </div>
        </div>
        <div class="row">
            <br />
            <div id="jsonResponse" class="span6" style="background:#d3d3d3;height:600px;">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{$config->url->js}_tools/prettyprint.js"></script>
<script type="text/javascript" src="{$config->url->js}_tools/jsonFormatter.js"></script>
<script type="text/javascript" src="{$config->url->js}debug/apicalls.js"></script>
{include file='footer.tpl'}

