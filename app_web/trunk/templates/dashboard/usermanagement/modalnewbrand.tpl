<div id="modalnewbrand" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="editBrandModalLabel">Add Brand</h3>
    </div>
    <div class="modal-body">
        <div id="brandedit_formstatus" class="">
            &nbsp;
        </div>
        <p>
        <div class="span4">
            <div class="form-horizontal" id="editBrandForm">
                <fieldset id="editFormFields">
                    <div id="editFormstatus" class="">
                        &nbsp;
                    </div>
                    <div class="control-group span4">
                        <div id="editCreateUserPanel" class="control-group">
                            <input type="hidden" id="editUserId" data-bind="value:editUserId" />
                            <div class="control-group" >
                                <label class="control-label" for="editBrandName">Brand Name</label>
                                <div class="controls">
                                    <input id="editBrandName" data-bind="value:brandName" placeholder="Enter a Brand Name" class="input-large"/>
                                    &nbsp;
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="editBrandSubmission">&nbsp;</label>
                                <div class="controls">
                                    <button id="editBrandSubmission" data-bind='click: createBrandClick' class="btn btn-primary">&nbsp;&nbsp;&nbsp;Create Brand &nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    </p>
</div>