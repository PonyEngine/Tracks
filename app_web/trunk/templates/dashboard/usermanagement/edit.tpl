<div id="editUserModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="editModalLabel">Edit User</h3>
    </div>
    <div class="modal-body">
        <p>
        <div class="span4">
            <div class="form-horizontal" id="editUserForm">
                <fieldset id="editFormFields">
                    <div id="editFormstatus" class="">
                        &nbsp;
                    </div>
                    <div class="control-group span4">
                        <div id="editCreateUserPanel" class="control-group">
                            <input type="hidden" id="editUserId" data-bind="value:editUserId" />
                            <div class="control-group" >
                                <label class="control-label" for="editEmail">Email Address</label>
                                <div class="controls">
                                    <input id="editEmail" data-bind="value:editEmail"  placeholder="user@corso.com" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label class="control-label" for="editProfilename">Profile Name</label>
                                <div class="controls">
                                    <input id="editProfilename"  data-bind="value:editProfilename" placeholder="A Username (opt)" class="input-large"/>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label class="control-label" for="editBrandNum">Brand</label>
                                <div class="controls">

                                    <select id="editBrandNum" >
                                    {foreach from=$brands item=aBrand }
                                        <option value="{$aBrand->getId()}">{$aBrand->brand_name}</option>
                                    {/foreach}
                                    </select>
                                    <div id="brandfullaccess">
                                        Full Brand Access
                                    </div>
                                </div>
                            </div>
                            <div class="control-group" >
                                <label class="control-label" for="editExpiry">Membership Expiration</label>
                                <div class="controls">
                                {*TODO:calculate a time 30 days in the future as the start date for both  the placeholder and datepicker*}
                                {* <div class="input-append datepicker" id="dp1" data-date="03/02/2015" data-date-format="mm/dd/yyyy">*}
                                    <input id="editExpiry"  data-bind="value:editExpiry"  placeholder="03/02/2015" class="input-large"/>
                                {*   <span class="add-on"><i class="icon-th"></i></span>
                                </div>*}
                                </div>
                            </div>
                            {*<div class="control-group">
                                <label class="control-label" for="userSubmission">&nbsp;</label>
                                <div class="controls">
                                    <a href="#">Reset Password</a>
                                </div>
                            </div>*}
                            <div class="control-group">
                                <label class="control-label" for="saveUserClick">&nbsp;</label>
                                <div class="controls">
                                    <button id="saveUserClick" data-bind='click: saveUserClick' class="btn btn-primary">&nbsp;&nbsp;&nbsp;Save &nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                        </div>
                    {*
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
                        *}
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
    </div>
    </p>
{*<div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary">Save changes</button>
</div>*}
</div>