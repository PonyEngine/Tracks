Event Reports for {$dataArr.brandName}:{$dataArr.event->event_name}
<h4>{$dataArr.event->event_name}</h4><br />
{if $dataArr.event->getId()>0}
<table style="border-width: 1px; border-spacing: 2px; border-style: groove; border-color: {$dataArr.borderColor}; border-collapse: collapse;background-color: rgb(250, 240, 230);">
    <tr style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor};">
        <td><h4>Taken</h4></td>
        {foreach from=$dataArr.eventStats item=anEventStat}
            <td style="border-width: 2px; padding: 2px; border-style: ridge; border-color:{$dataArr.borderColor};{if !$anEventStat.dayOfEvent} color:graytext;{/if}">{$anEventStat.taken}</td>
        {/foreach}
    </tr>
    <tr style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor};">
        <td><h4>Emailed</h4></td>
        {foreach from=$dataArr.eventStats item=anEventStat}
            <td style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor}; {if !$anEventStat.dayOfEvent} color:graytext;{/if}">{$anEventStat.emailed}</td>
        {/foreach}
    </tr>
    <tr style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor};">
        <td><h4>MMS</h4></td>
        {foreach from=$dataArr.eventStats item=anEventStat}
            <td style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor}; {if !$anEventStat.dayOfEvent} color:graytext;{/if}">{$anEventStat.mmsed}</td>
        {/foreach}
    </tr>
    <tr style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor};">
        <td><h4>&nbsp;</h4></td>
        {foreach from=$dataArr.eventStats  item=anEventStat}
            <td style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor};{if !$anEventStat.dayOfEvent} color:graytext;{/if}"><h4>{$anEventStat.date}</h4></td>
        {/foreach}
        <td style="border-width: 2px; padding: 2px; border-style: ridge; border-color: {$dataArr.borderColor};"><h4>Total: {$dataArr.event->imageIds()|@count}</h4></td>
    </tr>
</table>
{else}
        Empty Report
{/if}