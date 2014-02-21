var uploadPhotoBtnImages=[];
var backgroundScreenImages=[];
var shareEmailImages=[];
var sharePrintImages=[];
var shareMMSImages=[];
var sharefbImages=[];
var sharetwitImages=[];
var sharegpImages=[];

function clearMarkings(){
    $("#formstatus").removeClass();
    $("#formstatus").html("");
    //Clear red markings
    $.each($("#formFields"), function(name, value){
        $(name).removeClass('red');
    });
}

function processingNotif(){
    $("#formstatus").addClass('alert');
    $("#formstatus").addClass('alert-info');
    $("#formstatus").html("<img src='/skin/images/smallSpinner.gif'/> Processing");
}

function resetSliderWithId(idName){
    var currentPosition = 0;
    var slideWidth = 170;
    var slides = $('.slide');
    var numberOfSlides = slides.length;

    // Remove scrollbar in JS
    $('#slidesContainer').css('overflow', 'hidden');

    // Wrap all .slides with #slideInner div
    slides
        .wrapAll('<div id="slideInner"></div>')
        // Float left to display horizontally, readjust .slides width
        .css({
            'float' : 'left',
            'width' : slideWidth
        });

    // Set #slideInner width equal to total width of all slides
    $('#slideInner').css('width', slideWidth * numberOfSlides);

    // Insert controls in the DOM
    $('#slideshow')
        .prepend('<span class="control" id="leftControl">Clicking moves left</span>')
        .append('<span class="control" id="rightControl">Clicking moves right</span>');

    // Hide left arrow control on first load
    manageControls(currentPosition);

    // Create event listeners for .controls clicks
    $('.control')
        .bind('click', function(){
            // Determine new position
            currentPosition = ($(this).attr('id')=='rightControl') ? currentPosition+1 : currentPosition-1;

            // Hide / show controls
            manageControls(currentPosition);
            // Move slideInner using margin-left
            $('#slideInner').animate({
                'marginLeft' : slideWidth*(-currentPosition)
            });
        });

    // manageControls: Hides and Shows controls depending on currentPosition
    function manageControls(position){
        // Hide left arrow if position is first slide
        if(position==0){ $('#leftControl').hide() } else{ $('#leftControl').show() }
        // Hide right arrow if position is last slide
        if(position==numberOfSlides-1){ $('#rightControl').hide() } else{ $('#rightControl').show() }
    }

    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });
}

var AssetsViewModel = function() {
    var self=this;
    self.eventId=ko.observable();
    self.imgbackgroundscreenArray = ko.observableArray();
    self.imguploadphotobtnArray = ko.observableArray();
    self.imgshareemailbtnArray = ko.observableArray();
    self.imgshareprintbtnArray = ko.observableArray();
    self.imgsharemmsbtnArray = ko.observableArray();
    self.imgsharefbbtnArray = ko.observableArray();
    self.imgsharetwitbtnArray = ko.observableArray();
    self.imgsharegpbtnArray = ko.observableArray();


    self.saveAssetsClick = function() {
        processingNotif();
        self.imguploadphotobtnArray=uploadPhotoBtnImages;
        self.imgbackgroundscreenArray=backgroundScreenImages;
        self.imgshareemailbtnArray=shareEmailImages;
        self.imgshareprintbtnArray = sharePrintImages;
        self.imgsharemmsbtnArray = shareMMSImages;
        self.imgsharefbbtnArray = sharefbImages;
        self.imgsharetwitbtnArray = sharetwitImages;
        self.imgsharegpbtnArray = sharegpImages;
        self.eventId=$('#eventSelect :selected').attr('id');

        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/app/uploadassets",postData,
            function(data) {
                clearMarkings();
                try
                {
                    uploadPhotoBtnImages=[];
                    backgroundScreenImages=[];
                    shareEmailImages=[];
                    sharePrintImages=[];
                    shareMMSImages=[];
                    sharefbImages=[];
                    sharetwitImages=[];
                    sharegpImages=[];
                   // var jsonParsed = JSON.parse(data);
                    if(data.status.code=="200"){
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-success');
                        $("#formstatus").html(data.status.msg);
                    }
                    else{
                        var errors=data.status.errors;
                        var errorMsg='<ul>';
                        $.each(errors, function(name, value) {
                            //Highlight
                            $("#"+name).addClass('red');
                            //Error Msg
                            errorMsg+="<li>"+value+"</li>";
                        });
                        errorMsg+='</ul>';

                        //Print Errors
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-error');
                        $("#formstatus").html(errorMsg);
                    }

                }
                catch(err)
                {
                    var txt="Error description: " + err.message + "\n\n";
                    $("#formstatus").html(data);
                }


            });
    };
};


function optionsProcessingNotif(){
    var eventName=$('#eventSelect :selected').val();
    $("#uioptions").html("<div style='text-align:center;'>Loading Options for "+eventName+"... &nbsp;<img src='/skin/images/smallSpinner.gif'/></div>");
}


function processCurrentSelection(){
    optionsProcessingNotif();
    var eventId=$('#eventSelect :selected').attr('id');
    var eventData='eventId='+eventId;
    //Get the table for this event
    //Set the event Id for items that need it
    //$('#exceleventid').val(eventId);

    $.post("/app/uioptions",eventData,
        function(data) {
            try
            {
                uploadPhotoBtnImages=[];
                backgroundScreenImages=[];
                shareEmailImages=[];
                sharePrintImages=[];
                shareMMSImages=[];
                sharefbImages=[];
                sharetwitImages=[];
                sharegpImages=[];

                $('#uioptions').html(data);
                ko.applyBindings(new AssetsViewModel($("#uioptions")[0]));

                $('#restoreuidefaults').on('click', function(){
                    alert("User will be able to reset the UI to the default UI");
                });

            }
            catch(err)
            {
                var txt="Error description: " + err.message + "\n\n";
                $("#formstatus").html(data);
            }

            $('.assetupload').on('change', function(){
                var thisId=$(this).attr("id");
                $("#"+thisId+"_prevLoader").html('<img src="/skin/images/ajax-loader.gif" alt="Uploading...."/>');
               // $("#"+thisId+"_form").hide();
                //processingNotif();
                var options={
                    success: function(data, statusText, xhr, $form){
                        clearMarkings();
                        try
                        {
                            var jsonParsed = JSON.parse(data);
                            if(jsonParsed.status.code=="200"){
                                var fileInfo=jsonParsed.data.fileInfo;
                                var imgHTMLColumn="<td><ul class='thumbnails'><li class='span3'><img src='"+fileInfo.filePath+"' alt='"+fileInfo.fileName+"' height='100' width='100'/></li></ul></td>";
                                $("#"+thisId+"_previewTable tr:first td:first").before(imgHTMLColumn);

                                //Push on array to save asset
                                var thisArrCount=0;
                                switch(thisId){
                                    case 'imgbgimg':
                                        backgroundScreenImages.push(fileInfo.filePath);
                                        //if(backgroundScreenImages.length<1)$("#"+thisId+"_form").show();
                                        break;
                                    case 'imguploadphotobtn':
                                        uploadPhotoBtnImages.push(fileInfo.filePath);
                                    break;
                                    case 'imgshareemailbtn':
                                        shareEmailImages.push(fileInfo.filePath);
                                     break;
                                    case 'imgshareprintbtn':
                                        sharePrintImages.push(fileInfo.filePath);
                                    break;
                                    case 'imgsharemmsbtn':
                                        shareMMSImages.push(fileInfo.filePath);
                                    break;
                                    case 'imgsharefbbtn':
                                        sharefbImages.push(fileInfo.filePath);
                                    break;
                                    case 'imgsharetwitbtn':
                                        sharetwitImages.push(fileInfo.filePath);
                                     break;
                                    case 'imgsharegpbtn':
                                        sharegpImages.push(fileInfo.filePath);
                                     break;

                                }
                                $("#"+thisId+"_prevLoader").html('');

                            }else{
                                $("#"+thisId+"_form").show();
                                $("#"+thisId+"_prevLoader").html('');
                                clearMarkings();
                                var errors=jsonParsed.status.errors;
                                var errorMsg='<ul>';
                                $.each(errors, function(name, value) {
                                    //Highlight
                                    $("#"+name).addClass('red');
                                    //Error Msg
                                    errorMsg+="<li>"+value+"</li>";
                                });
                                errorMsg+='</ul>';

                                //Print Errors
                                $("#formstatus").addClass('alert');
                                $("#formstatus").addClass('alert-error');
                                $("#formstatus").html(errorMsg);
                                //Highlight trouble areas
                            }
                        }catch(err){
                            //alert("Cannot preview image");
                            $("#"+thisId+"_form").show();
                            $("#"+thisId+"_prevLoader").html('');
                            $("#formstatus").html(data);
                        }
                    }
                };
                $("#"+thisId+"_form").ajaxForm(options).submit();
            });

    $('.assetlib').on('click', function(){
        var thisId=$(this).attr("id");
        $("#slidesContainer_"+thisId).html('<center><img src="/skin/images/ajax-loader.gif" alt="Searching...."/></center>');
        var options={
            success: function(response, statusText, xhr, $form){
                $("#slidesContainer_"+thisId).html('');
                try
                {
                    var libOptions='';
                    if(response.status.code=="200"){
                        var assets=response.data.assets;
                        //LoopAround Options
                        $.each(assets, function(index, assetVal) {
                            libOptions=libOptions+'<div imageId="'+assetVal.imgId+'" class="liboption slide" ><img src="'+assetVal.imgURL+'"/></div>';
                        });
                        var thisArrCount=0;
                        switch(thisId){
                            case 'libbgimg':
                                  if(libOptions.length>0)$("#slidesContainer_"+thisId).html(libOptions);
                                resetSliderWithId('libbgimg');
                            break;
                            case 'imguploadphotobtn':
                                //list options
                                //uploadPhotoBtnImages.push(fileInfo.filePath);
                                break;
                            case 'imgshareemailbtn':
                                //list options
                                //shareEmailImages.push(fileInfo.filePath);
                                break;
                            case 'imgshareprintbtn':
                                //list options
                               // sharePrintImages.push(fileInfo.filePath);
                                break;
                            case 'imgsharemmsbtn':
                                //list options
                                //shareMMSImages.push(fileInfo.filePath);
                                break;
                            case 'imgsharefbbtn':
                                //list options
                                //sharefbImages.push(fileInfo.filePath);
                                break;
                            case 'imgsharetwitbtn':
                               //list options
                               // sharetwitImages.push(fileInfo.filePath);
                                break;
                            case 'imgsharegpbtn':
                                //list options
                               // sharegpImages.push(fileInfo.filePath);
                                break;
                        }

                    }else{
                        $("#"+thisId+"_form").show();
                        $("#"+thisId+"_prevLoader").html('');
                        clearMarkings();
                        var errors=jsonParsed.status.errors;
                        var errorMsg='<ul>';
                        $.each(errors, function(name, value) {
                            //Highlight
                            $("#"+name).addClass('red');
                            //Error Msg
                            errorMsg+="<li>"+value+"</li>";
                        });
                        errorMsg+='</ul>';

                        //Print Errors
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-error');
                        $("#formstatus").html(errorMsg);
                        //Highlight trouble areas
                    }
                }catch(err){
                    //alert("Cannot preview image");
                    $("#"+thisId+"_form").show();
                    $("#"+thisId+"_prevLoader").html('');
                    $("#formstatus").html(data);
                }
            }
        };
        $("#form_"+thisId).ajaxForm(options).submit();
    });
});


}






$(document).ready(function(){
    processCurrentSelection();
    $('#eventSelect').on('change', function(){
        processCurrentSelection();
    });

});
