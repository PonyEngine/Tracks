{include file='header.tpl'}
<br>
<h2 style="text-align: center; font-size: 33px;color: rgb(63, 63, 63);"><i class="icon-trophy icon-1x"></i> User Management {*&nbsp;<a  href="#modalnewbrand" class="btn btn-primary"  data-toggle="modal"> Add Brand</a>*}</h2>

<div  id="socialoptions" style="width: 94%;"class="tabbable span12"> <!-- Only required for left/right tabs -->
    <ul class="nav nav-tabs"  id="myTab" style="

    margin-left: 25px;

">

      {*  <li class="active"><a href="#create" data-toggle="tab">Create User</a></li>*}
        <li><a href="#manage" data-toggle="tab">Manage Users</a></li>

    </ul>

    <div class="tab-content span6">

        <div class="tab-pane active" id="create">

        {*{include file='dashboard/usermanagement/create.tpl'}*}

        </div>

        <div class="tab-pane active" id="manage">

        {include file='dashboard/usermanagement/manage.tpl'}

{literal}

    <script>

        $(function () {

            $('#myTab a:last').tab('show');

        })

    </script>

{/literal}

            <!-- Modals -->
        {include file='dashboard/usermanagement/modalnewbrand.tpl'}




</div>


<script type="text/javascript" src="{$config->url->js}dashboard/usermanagement.js"></script>

</div>{include file='footer.tpl'}</div>
