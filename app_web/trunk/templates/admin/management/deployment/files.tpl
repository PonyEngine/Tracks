<div  style="width:850px;height:600px;overflow-y:scroll;">
<table border="3" style="padding: 5px;">
    <th colspan="3">From ({$fromFiles|@count} files)</th><th colspan="3">To ({$toFiles|@count} files)</th>
    <tr><td>Filename</td><td>Size</td><td>Date</td><td>Filename</td><td>Size</td><td>Date</td></tr>
    {foreach from=$fromToFiles item=aFromToFile name=count }
        <tr
            style="
            {if $aFromToFile.status==0} background-color: #f5f5f5;{/if}
            {if $aFromToFile.status==1} background-color: #fafad2;{/if}
            {if $aFromToFile.status==2} background-color: #add8e6;{/if}
            {if $aFromToFile.status==3} background-color: #f08080;{/if}
            ">
            <td>
                {section name=foo start=1 loop=$aFromToFile.from.level step=1}
                    &nbsp;&nbsp;
                {/section}
                <span title="{$aFromToFile.from.fullPath}" style="{if $aFromToFile.from.type==1}font-weight:bold;{/if}">{$aFromToFile.from.name}</span></td><td>{$aFromToFile.from.size}</td><td><span title="{$aFromToFile.from.time|date_format:"%H:%M:%S"}">{$aFromToFile.from.time|date_format:"%m/%d/%Y"}</span></td>
            <td>
                {section name=foo start=1 loop=$aFromToFile.to.level step=1}
                    &nbsp;&nbsp;
                {/section}
                <span title="{$aFromToFile.to.fullPath}" style="{if $aFromToFile.to.type==1}font-weight:bold;{/if}">{$aFromToFile.to.name}</span></td><td>{$aFromToFile.to.size}</td><td><span title="{$aFromToFile.to.time|date_format:"%H:%M:%S"}">{$aFromToFile.to.time|date_format:"%m/%d/%Y"}</span></td>

            {*<td  {if $fileInfo.name!= $fileInfoTo.name}bgcolor="#4169e1"{/if}><span title="{$fileInfo2.fullPath}">{$fileInfoTo.name}</span></td><td>{$fileInfoTo.size}</td><td><span title="{$fileInfoTo.time|date_format:"%H:%M:%S"}">{$fileInfoTo.time|date_format:"%m/%d/%Y"}</span></td>*}
        </tr>
    {/foreach}
</table>
</div>