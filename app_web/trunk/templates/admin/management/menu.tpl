<div class="span2">
    <ul class="nav nav-list">
            <li class="nav-header">
                Users ({$userCount} Total)
            </li>
            <li {if $selected=="users_create_u"}class="active"{/if}>
                <a href="/admin/management?area=users&func=create_u">User Management</a>
            </li>
            <br /><br />
            <li class="nav-header">
                Maintenance
            </li>
             <li {if $selected=="maintenance_snapshot"}class="active"{/if}>
                    <a href="/admin/management?area=maintenance&func=snapshot">Snapshot (Zips)</a>
            </li>
            <li {if $selected=="tools_s"}class="active"{/if}>
                <a {if $selected=="tools_s"} class="selected"{/if} href="/admin/management/option/compression_m">Compression</a>
            </li>
            <li {if $selected=="maintenance_s"}class="active"{/if}>
                <a {if $selected=="maintenance_s"} class="selected"{/if} href="/admin/management/option/maintenance_s">Database Tools</a>
            </li>
            <br /><br />
            <li class="nav-header">
                Deployment
            </li>
            <li {if $selected=="deployment_files"}class="active"{/if}>
                <a  href="/admin/management?area=deployment&func=files">Files</a>
            </li>
            {if $config->servertype=='dev'}
                <li {if $selected=="edit_s"}class="active"{/if}>
                    <a {if $selected=="edit_s"} class="selected"{/if} href="/admin/management?area=maintenance&func=deployfromto">Deploy From=? To=?</a>
                </li>
            {/if}
            <br /><br />
            <li class="nav-header">
                Debugging
            </li>
            <li {if $area_func=="debugging_viewlog"} class="active"{/if}>
                <a href="/admin/management?area=debugging&func=viewlog">View Logs</a>
            </li>
            <li {if $area_func=="debugging_viewdata"} class="active"{/if}>
                <a href="/admin/management?area=debugging&func=viewdata">View Data</a>
            </li>
            <li {if $area_func=="debugging_viewtmp"} class="active"{/if}>
                <a href="/admin/management?area=debugging&func=viewtmp">View Tmp</a>
            </li>
      </ul>
</div>
