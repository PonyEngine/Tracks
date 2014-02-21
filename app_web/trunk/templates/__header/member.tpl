<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/"><img src="/skin/images/logo.png" alt="LOGO_TRACKS" width="25" height="25" />&nbsp;{$config->title}</a>
            <div class="nav-collapse">
                <ul class="nav">
                {if ($identity->user_type=="Admin" || $identity->user_type=="developer")}
                    <li {if $menuSelect=="brandsandusers"}class="active"{/if}><a href="/dashboard/usermanagement">User Management</a></li>
                {/if}
                    {*
                    <li {if $menuSelect=="events"}class="active"{/if}><a href="/dashboard/events">Events</a></li>
                    <li class="dropdown" id="menu1">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#menu1" >
                            <span {if $menuSelect=="app_ui"|| $menuSelect=="app_social"}style="color:white"{/if}>App Settings</span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                                <li {if $menuSelect=="app_assetmanagement"}class="active"{/if}><a href="/app/ui"><i class="icon-wrench"></i>  User Interface</a></li>
                                <li {if $menuSelect=="app_effects"}class="active"{/if}><a href="/app/effects"><i class="icon-camera-retro"></i>  Photo Effects</a></li>
                                <li {if $menuSelect=="app_social"}class="active"{/if}><a href="/app/social"><i class="icon-group"></i>  Social Settings</a></li>

                                {if ($identity->user_type=="developer")}
                                     <li class="divider"></li>
                                    <li {if $menuSelect=="app_download"}class="active"{/if}><a href="/app/download">Download iPad App</a></li>
                                {/if}
                        </ul>
                    </li>
                    <li {if $menuSelect=="reports"}class="active"{/if}><a href="/dashboard/reports">Reports</a></li>*}
                 {if ($identity->user_type=="developer")}
                   <li class="dropdown" id="adminMenu">
                       <a class="dropdown-toggle" data-toggle="dropdown" href="/admin/management"" {if $menuSelect=="admin"}class="active"{/if}>
                           Admin Management
                       </a>
                   </li>
                    <li {if $menuSelect=="api"}class="active"{/if}><a href="/debug/apicalls">APIs</a></li>
                {/if}
                    {*<li {if $menuSelect=="contact"}class="active"{/if}><a href="/contact">Contact</a></li>*}
                </ul>
                <ul class="nav" style="float:right">
                    <li class="dropdown" id="userDetail">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#userDetail">
                            {$identity->usernameEmail}
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li {if $menuSelect=="home"}class="active"{/if}><a href="/"><i class="icon-home"></i>&nbsp;Home</a></li>
                            <li class="divider"></li>
                            <li {if $menuSelect=="settings"}class="active"{/if}><a href="/settings/"><i class="icon-cog"></i>&nbsp;Settings</a></li>
                            <li class="divider"></li>
                            <li {if $menuSelect=="help"}class="active"{/if}><a href="/help"><i class="icon-question-sign"></i>&nbsp;Help</a></li>
                            <li class="divider"></li>
                            <li><a id="appFbLogout" url="/account/requestlogout" href="#">Sign Out</a></li>
                        </ul>
                    </li>

                </ul>

            </div><!--/.nav-collapse -->
        </div>
    </div>
    <div id="fb-root"></div>
</div>
