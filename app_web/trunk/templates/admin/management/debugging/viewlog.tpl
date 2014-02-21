<div class="container" xmlns="http://www.w3.org/1999/html">
    <div class="row">
        <h3>SELECT LOG</h3>
            <form>
                <select class="small">
                    debug.logfiles
                {assign var=logfilesArr value=","|explode:$config->debug->logfiles}
                {foreach from=$logfilesArr key=fileIndex item=fileName}
                    <option value="fileName">{$fileName}</option>
                {/foreach}
                </select>
                From <input type="text"/>&nbsp;&nbsp;
                <input type="checkbox" value="live"/>&nbsp;ACTIVE
                <textarea rows="20" cols="800" style="width:800px;text-align: left;">{foreach from=$logRows key=rowPos item=rowTxt}{$rowTxt}{/foreach}</textarea>
                <button id="saveProfile" data-bind='click: saveProfileClick,enable:profileHasChanged' class="btn btn-primary">Flush Data</button>
            </form>
    </div>