{include file='header.tpl'}
<div class="container" xmlns="http://www.w3.org/1999/html">
    <br />
    <div class="row" style="text-align: center;">
        <div class="span12">
            <h2 style="font-size: 33px;color: rgb(63, 63, 63);"><i class="icon-magic icon-1x"></i>   Event Creation & Management</h2> <br/>
            <label style=" font-size: 16px;color: rgb(129, 129, 129);text-align: center;" class="control-label" for="selectBrand">Select Brand <br/>{if  $events|@count >0}<smaller>or <a id="useSelectEvent" href="#">Manage Events</a></smaller>{/if}</label>
            <div class="form-horizontal" id="branduserForm">
                <fieldset id="formFields">
                    <div id="formstatus" class="">
                        &nbsp;
                    </div>
                    <div class="control-group span7" style="margin-left: 234px;">
                    {if  $brands|@count >0}
                        {if $identity->user_type=='Admin' || $brands|@count >1}
                            <div id="brandSelection" class="control-group" style="display:block;">

                            <div id="selectBrand" class="controls">
                                <select id="brandSelect" style="font-size: 16px;margin-left: -230px;">
                                    {foreach from=$brands item=aBrand }
                                        <option id="{$aBrand->getId()}" default="{if $aBrand->default_event_id}1{else}0{/if}">{$aBrand->brand_name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                         <div id="eventSelection" class="control-group" style="display:none;">
                             {if  $events|@count >0}
                                <label style="font-size: 17px;" class="control-label" for="selectEvent">Select Event <br/>or <a id="useBrandSelection" href="#">New Event</a></label>
                                <div id="selectEvent" class="controls">
                                    <select id="eventSelect" >
                                        {foreach from=$events item=anEvent }
                                            <option id="{$anEvent->getId()}">{$anEvent->event_name}</option>
                                        {/foreach}
                                    </select>
                                 </div>
                                 {else}
                                <h2>No events to select from.</h2>
                             {/if}
                         </div>
                         {else}
                              <div id="brandSelection" class="control-group" style="display:block;">
                                    <label style="font-size: 17px;" class="control-label" for="brandName"><span style="font-weight: bolder;">New Event</span><br/>{if  $events|@count >0}<smaller>Or <a id="useSelectEvent" href="#">Manage Events</a></smaller>{/if}</label>
                                    <div id="brandName" class="controls">
                                        {foreach from=$brands item=aBrand}{*Will only need to loop once *}
                                           <h2>{$aBrand->brand_name}</h2>
                                            <input type="hidden" id="brandId" value="{$aBrand->getId()}" />
                                        {/foreach}
                                     </div>
                                </div>
                                <div id="eventSelection" class="control-group" style="display:none;">
                                    {if  $events|@count >0}
                                        <label style="font-size: 17px;" class="control-label" for="selectEvent">Edit Event <br/>or <a id="useBrandSelection" href="#">New Event</a></label>
                                        <div id="selectEvent" class="controls">
                                            <select id="eventSelect" >
                                                {foreach from=$events item=anEvent }
                                                    <option id="{$anEvent->getId()}">{$anEvent->event_name}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        {else}
                                        <h2>No events to select from.</h2>
                                    {/if}
                                </div>
                        {/if}
                        <div id="createEventPanel" class="control-group">
                            <div class="control-group" >
                                <label style="font-size: 17px;" class="control-label" for="eventname">&nbsp;</label>
                                <div class="controls">
                                    <input id="eventId" data-bind="value:eventId" type="hidden"/>
                                </div>
                            </div>
                            <div class="control-group">
                                <label style="font-size: 17px;" class="control-label" for="eventname">Use Brand Default</label>
                                <div class="controls">
                                    <input id="usedefault" type="checkbox" data-bind="checked:usedefault">
                                    <span id='usedefaultdef'>Default Not Available</span>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label style="font-size: 17px;" class="control-label" for="eventname">Event Name</label>
                                <div class="controls">
                                    <input id="eventname" data-bind="value:eventname" placeholder="Event Name" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label style="font-size: 17px;" class="control-label" for="eventlocation">Location</label>
                                <div class="controls">
                                    <input id="eventlocation" data-bind="value:eventlocation" placeholder="City, State" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label style="font-size: 17px;" class="control-label" for="startdate">Start Date</label>
                                <div class="controls">
                                    <input id="startdate"  data-bind="value:startdate" placeholder="03/11/2012" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label style="font-size: 17px;" class="control-label" for="enddate">End Date</label>
                                <div class="controls">
                                    <input id="enddate"  data-bind="value:enddate"   placeholder="03/11/2012" class="input-large"/>
                                </div>
                            </div>
                            <div  class="control-group" >
                                <label style="font-size: 17px;" class="control-label" for="orientationButtons">Photo Orientation</label>
                                <div id="orientationButtons" class="controls">
                                    <div id="orbtns" class="btn-group" data-toggle="buttons-radio" data-bind="foreach: orientationButtons">
                                    {literal}<button class="btn" data-bind="text: name, css: {active: selected}, click: $parent.selectButton"></button>{/literal}
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label style="font-size: 17px;" class="control-label" for="allowprinting">Allow Printing?</label>
                                <div class="controls">
                                    <input id="allowprinting" type="checkbox" data-bind="checked:allowprinting">
                                </div>
                            </div>
                            <div class="control-group">
                                <label style="font-size: 17px;" class="control-label" for="allowprinting">Social Integration?</label>
                                <div class="controls">
                                    <input id="social_facebook" type="checkbox" data-bind="checked:social_facebook" /> Facebook &nbsp;
                                    <input id="social_twitter" type="checkbox" data-bind="checked:social_twitter" /> Twitter &nbsp;
                                    <input id="social_googleplus" type="checkbox" data-bind="checked:social_googleplus" /> Google + &nbsp;
                                </div>
                            </div>
                           <div class="control-group" >
                                <label style="font-size: 17px;
"class="control-label" for="eventname">Set As Default Event?</label>
                                <div class="controls">
                                    <input id="setasdefault" type="checkbox" data-bind="checked:setasdefault">
                                </div>
                            </div>
                            <div class="control-group">
                                <label style="
    font-size: 17px;
"class="control-label" for="createEvent">&nbsp;</label>
                                <div class="controls">
                                    <button style="
    float: right;
    padding: 15px 15px 15px 15px;
    margin-right: 22px;
    margin-top: 32px;
    background-image: -webkit-linear-gradient(top, rgb(79, 177, 13), rgb(26, 107, 55)); background-color: rgb(26, 107, 55);
"id="createEvent" data-bind='click: createEventClick' class="btn btn-primary">&nbsp;&nbsp;&nbsp;Create Event &nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                        </div>
                        {if  $events|@count >0}
                            <div id="eventEndButton" class="control-group" style="display:none;">
                                <label style="
    font-size: 17px;
"class="control-label" for="endEvent">&nbsp;</label>
                                <div class="controls">
                                    <button id="endEvent" data-bind='click: endEventClick' class="btn btn-primary">&nbsp;&nbsp;&nbsp;End Event &nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                        {/if}
                    {*<div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="selectUserCheckBox"><input id="selectUserCheckBox" type="checkbox" data-bind="checked:selectUser">&nbsp;Select User</label>
                            <div class="controls"></div>
                        </div>
                    </div>*}
                    {else}
                        <h2>You can not create an event because you are not associated with any brands. Please see administrator.</h2>
                    {/if}
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{$config->url->js}dashboard/events.js"></script>
{include file='footer.tpl'}
