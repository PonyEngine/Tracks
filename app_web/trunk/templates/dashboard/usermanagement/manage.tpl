<div  id="searchUser"> <!-- Only required for left/right tabs -->
    <div id="usersearchForm">
        <table id="userList" class="table table-striped table-bordered">
            <div class="span19">
                <div class="span6">
                {*Page <input style="width:20px;" type="text" class="input-mini"> of 2 Pages &nbsp;|&nbsp;View <select class="input-mini"><option>10</option><option>20</option></select> &nbsp; per page |*}
                    Total <span id="recordsCount">0</span> records found
                </div>
                <div class="span2 offset6" style="float: right;">
                {*<div class="btn-group">
                <button class="btn">Export To</button>
                <button class="btn dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>CSV</li>
                    <li>Excel</li>
                </ul>
                </div>*}
                {*  <button class="btn btn-small btn-primary" type="button">Reset Filter</button>*}
                </div>
            </div>
            <tr>
            {*  <td colspan="12">
                <a href='#'>Select All</a> &nbsp;|&nbsp; <a href='#'>Unselect All</a> &nbsp;|&nbsp; <a href='#'>Select Visible</a> &nbsp;|&nbsp; <span style="font-weight:bolder;">0</span> Items selected
            </td>*}
            </tr>
            <tr>
               {* <th>&nbsp;</th>*}
                <th>ID &nbsp;</th>
                <th>ProfilePic</th>
                <th>Email</th>
                <th>Profile Name</th>
                <th>Member Level</th>
                <th>Created</th>
                <th>Last Sign In</th>
                <th>Expiration</th>
                <th>&nbsp;</th>
            </tr>
            <tr id="searchtr">
                {*<th></th>*}
                <th><input id="userId" data-bind="value:userId" type="text" class="input-mini"/></th>
                <th>&nbsp;</th>
                <th><input type="text" class="input-medium" data-bind="value:email"/></th>
                <th><input type="text" class="input-medium" data-bind="value:profileName"/></th>
                <th>
                    <select id="mLevel" class="input-small" >
                        <option id="0">Any</option>
                    {foreach from=$memberLevels item=aMemberLevel }
                        <option id="{$aMemberLevel}">{$aMemberLevel}</option>
                    {/foreach}
                    </select>
                </th>
                <th>
                    <select id="userBrand" class="input-medium" >
                        <option id="0">Any</option>
                    {foreach from=$brands item=aBrand }
                        <option id="{$aBrand->getId()}">{$aBrand->brand_name}</option>
                    {/foreach}
                    </select>
                </th>
                <th>From:&nbsp;<input id="createdFrom" type="text" class="input-mini datepicker" data-bind="value:createdFrom" placeholder="9/14/2012" data-date="12-02-2012" data-date-format="m/d/yyyy"/><br/>&nbsp;&nbsp;&nbsp;&nbsp;To:&nbsp;<input id="createdTo" type="text" class="input-mini datepicker" data-bind="value:createdTo" placeholder="9/14/2012"  data-date="12-02-2012" data-date-format="m/d/yyyy"/></th>
                <th>From:&nbsp;<input id="lastFrom" type="text" class="input-mini" data-bind="value:lastFrom" placeholder="9/14/2012" /><br/>&nbsp;&nbsp;&nbsp;&nbsp;To:&nbsp;<input id="lastTo" type="text" class="input-mini" data-bind="value:lastTo" placeholder="9/14/2012"/></th>
                <th>From:&nbsp;<input id="expireFrom" type="text" class="input-mini" data-bind="value:expireFrom" placeholder="9/14/2012" /><br/>&nbsp;&nbsp;&nbsp;&nbsp;To:&nbsp;<input id="expireTo" type="text" class="input-mini" data-bind="value:expireTo" placeholder="9/14/2012"/></th>
                <th> <button id="userSubmission" data-bind='click: searchUsersClick' class="btn btn-primary">&nbsp;&nbsp;&nbsp;Search &nbsp;&nbsp;&nbsp;</button>
                </th>
            </tr>
            <tr id="listofusers">
            </tr>
        </table>
    </div>
    <!-- Modals -->
{include file='dashboard/usermanagement/edit.tpl'}
</div>