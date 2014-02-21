{include file='header.tpl'}
<h1> A  Basic Demo of Prototype at work</h1>
<div id="result"></div>
	<div class="iquestionMark"></div>
	<div class="icheckMark"></div>
 	<div class="iXMark"></div>
	<div class="iedit"></div>
	<div class="iThumbUp"></div>
	<div class="iThumbDown"></div>
	<div class="iSatellite"></div>
	
	<div class="iSearch"></div>
	<div class="iSlice"></div>
	<div class="iPen"></div>
	<div class="iBlocker"></div>
	<div class="iLatcher"></div>
	<div class="iLatcher2"></div>
	<div class="iCompass"></div>
	New
	<br />
	
	<div class="iCamera"></div>
	<div class="iFilm"></div>
	<div class="iProfile"></div>
	<div class="iTag"></div>
	<div class="iSpontt"></div>
	<div class="iCamcorder"></div>
	NEW SHEET 
	<div class="i2ArrowD"></div>
	<div class="i2ArrowU"></div> 
	
	<div class="i2People"></div> 
	 <div class="loading"></div>
	{* {foreach from=$topOverallSpontters item=topOverallSpontter}
	<div class="span-17">
		<div class="span-5">
			{$topOverallSpontter->profile->up1} {$topOverallSpontter->profile->up3}
		</div>
		<div class="span-12 last">
			{$topOverallSpontter->profile->up27} 
			{$topOverallSpontter->getSponttCount()}
		</div>	
	</div>	
	{/foreach}
	*} 
	
	<div class="i2ArrowDs"></div>
	<div class="i2ArrowUs"></div>
   {include file='account/subhome/tagSelection.tpl'}
   
   <br />
	

	<ul id="navmenu-v" >
		<li>
	 {foreach from=$selectionArr key=catName item=selection}
		<ul> 
    		<li><a href="#">{$catName}</a> 
				<ul>
      			{foreach from=$selection key=subCatName item=tagInfo}
      			<li><a href="#">{$subCatName}</a>
				<ul>
				{foreach from=$tagInfo key=tagId item=tag}
					<li><a href="#{$tag.tag_id}">{$tag.tag_name}</a></li>
				{/foreach}
				</ul>
				</li>		
      			{/foreach}	
				</ul>
			</li>
		</ul>
	 {/foreach}
	 </li>
	 </ul>
{include file='footer.tpl'}