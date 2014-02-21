<div class="container" xmlns="http://www.w3.org/1999/html">
    <div class="row">
        <h3>Tmp Files ({$tmpFiles|@count} files)</h3>
        <div  style="width:710px;height:400px;overflow-y:scroll;">
            <table border="0" style="padding: 5px;">
                <span style="position:absolute;"><tr><th>Filename</th><th>Size</th><th>Date</th></tr></span>
            {foreach from=$tmpFiles item=aTmpFile name=count }
                <tr
                        style="background-color: #f5f5f5;">
                    <td>
                        {section name=foo start=1 loop=$aTmpFile.level step=1}
                            &nbsp;&nbsp;
                        {/section}
                        <span title="{$aTmpFile.fullPath}" style="{if $aTmpFile.type==1}font-weight:bold;{/if}">{$aTmpFile.name}</span></td><td>{$aTmpFile.size}</td><td><span title="{$aTmpFile.time|date_format:"%H:%M:%S"}">{$aTmpFile.time|date_format:"%m/%d/%Y"}</span>
                </td>
                </tr>
            {/foreach}
            </table>
        </div>