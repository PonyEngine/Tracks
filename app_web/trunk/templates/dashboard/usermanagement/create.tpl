{*<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Create Brand & User</h3>
    </div>
    <div class="modal-body">*}
        <div class="span4">
            <div class="form-horizontal" id="branduserForm">
                <fieldset id="formFields">
                    <div id="formstatus" class="">
                        &nbsp;
                    </div>
                    <div class="control-group span4">
                    {if  $brands|@count >0}
                        <div id="brandSelection" class="control-group" style="display:block;">
                            <label class="control-label" for="selectBrand">Select A Brand {*<br/> <smaller>Or  <a id="useNewBrand" href="#">New Brand</a></smaller>*}</label>
                            <div id="selectBrand" class="controls">
                                <select id="artistSelect" >
                                    {foreach from=$brands item=aBrand }
                                        <option id="{$aBrand->getId()}">{$aBrand->brand_name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div id="brandCreation" class="control-group" style="display:none;">
                            <label class="control-label" for="brandName">Brand Name <br/>Or <a id="useBrandSelection" href="#">Select a Brand</a></label>
                            <div class="controls">
                                <input id="brandName" data-bind="value:brandName" placeholder="Enter a Brand Name" class="input-large"/>
                                &nbsp;  <button id="brandSubmission" data-bind='click: createBrandOrUserClick' class="btn btn-primary" style="display:none;">&nbsp;&nbsp;&nbsp;Create Brand &nbsp;&nbsp;&nbsp;</button>
                            </div>
                        </div>
                        {else}
                        <div class="control-group">{*Not an error because brandName/brandSubmission of if/then condition- only 1 id shows up on the page*}
                            <label class="control-label" for="brandName">Brand Name</label>
                            <div class="controls">
                                <input id="brandName" data-bind="value:brandName" placeholder="Enter a Brand Name" class="input-large"/>
                                &nbsp;  <button id="brandSubmission" data-bind='click: createBrandOrUserClick' class="btn btn-primary" style="display:none;">&nbsp;&nbsp;&nbsp;Create Brand &nbsp;&nbsp;&nbsp;</button>
                            </div>
                        </div>
                    {/if}
                    {*<hr />*}
                        <div id="createUserPanel" class="control-group">
                            {*<i class="icon-user"></i> User*}
                            <div class="control-group" >
                                <label class="control-label" for="emailAddress">Email Address</label>
                                <div class="controls">
                                    <input id="emailAddress" data-bind="value:emailAddress" placeholder="user@corso.com" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label class="control-label" for="username">Username</label>
                                <div class="controls">
                                    <input id="username" data-bind="value:username" placeholder="A Username (opt)" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label class="control-label" for="password">Password</label>
                                <div class="controls">
                                    <input id="password" type="password" data-bind="value:password" placeholder="" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label class="control-label" for="confirmPassword">Confirm Password</label>
                                <div class="controls">
                                    <input id="confirmPassword"  type="password" data-bind="value:confirmPassword"  class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label class="control-label" for="userExpiry">Membership Expiration</label>
                                <div class="controls">
                                {*TODO:calculate a time 30 days in the future as the start date for both  the placeholder and datepicker*}
                                {* <div class="input-append datepicker" id="dp1" data-date="03/02/2015" data-date-format="mm/dd/yyyy">*}
                                    <input id="userExpiry"  data-bind="value:userExpiry" placeholder="03/02/2015" class="input-large"/>
                                {*   <span class="add-on"><i class="icon-th"></i></span>
                                </div>*}
                                </div>

                            </div>
                            <div class="control-group">
                                <label class="control-label" for="userSubmission">&nbsp;</label>
                                 <div class="controls">
                                    <button id="userSubmission" data-bind='click: createBrandOrUserClick' class="btn btn-primary">&nbsp;&nbsp;&nbsp;Create User &nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                        </div>
                        <div class="span4">
                           {*Client thought the current process was too complicated
                              <div class="control-group">
                                <label class="control-label" for="createUserCheckBox"><input id="createUserCheckBox" type="checkbox" data-bind="checked:createUser">&nbsp;Create User</label>
                                <div class="controls"></div>
                            </div>*}
                        </div>
                    {*<div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="selectUserCheckBox"><input id="selectUserCheckBox" type="checkbox" data-bind="checked:selectUser">&nbsp;Select User</label>
                            <div class="controls"></div>
                        </div>
                    </div>*}
                    </div>
                </fieldset>
            </div>
        </div>

{* <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary">Save changes</button>
</div>
</div>*}