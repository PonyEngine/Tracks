var effectsModel;
var imgaccents=[];
var imgoverlays=[];
var screenbgs=[];
var colorTintReq=false;

function clearMarkings(){
    $("#formstatus").removeClass();
    $("#formstatus").html("");
    //Clear red markings
    $.each($("#formFields"), function(name, value){
        $(name).removeClass('red');
    });
}

function processingNotif(){
    $("#formstatus").show();
    $("#formstatus").addClass('alert');
    $("#formstatus").addClass('alert-info');
    $("#formstatus").html("<img src='/skin/images/smallSpinner.gif'/> Processing");

}

var EffectsViewModel = function() {
    var self=this;
    self.eventId=ko.observable();
    self.imgoverlayArray = ko.observableArray();
    self.imgaccentArray=ko.observableArray();
    self.hasvignette= ko.observable(false);
    self.filterId=ko.observable();
    self.fontId=ko.observable();
    self.handleYPos=ko.observable();
    self.colorTintHex=ko.observable();
    self.accentOnLeft=ko.observable();
    self.clearimgAccent=ko.observable(false);
    self.clearimgOverlay=ko.observable(false);


    self.saveEffectsClick = function() {
        processingNotif();
        //Always get th handle position will. Will always be a handle position
        self.handleYPos=$( "#slider-vertical" ).slider( "values",0);

        if(colorTintReq){
            self.colorTintHex=$.farbtastic('#colorpicker').color;// $.farbtastic('#colorpicker').hsl.join();

        }else{
            self.colorTintHex=null;
        }

        if(1){
            self.accentOnLeft=$('input[name=accentPos]:checked', '#radioBtns').val();
        }else{

        }
        self.imgoverlayArray=imgoverlays;
        self.imgaccentArray=imgaccents;
        self.eventId=$('#eventSelect :selected').attr('id').replace('ev_','');

        //Get
        var theFilter=$('#filterSelect').val();
        self.filterId=theFilter.replace('fi_','');

        //Get
        var theFont=$('#fontSelect').val();
        self.fontId=theFont.replace('fo_','');

        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/app/updateffectssettings",postData,
            function(data) {
                clearMarkings();
                try
                {
                    imgaccents=[];
                    imgoverlays=[];
                    screenbgs=[];

                    if(data.status.code=="200"){
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-success');
                        $("#formstatus").html(data.status.msg); //.delay(1500).fadeOut(800);
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
                    $("#formstatus").addClass('alert');
                    $("#formstatus").addClass('alert-error');
                    $("#formstatus").html(txt);
                }
            });
    };
};


function optionsProcessingNotif(){
    var eventName=$('#eventSelect :selected').val();
    $("#effectsoptions").html("<div style='text-align:center;'>Loading Options for "+eventName+"... &nbsp;<img src='/skin/images/smallSpinner.gif'/></div><br /><br /><br />");
}


function processCurrentSelection(){
    optionsProcessingNotif();
    var eventId=$('#eventSelect :selected').attr('id').replace('ev_','');
    var eventData='eventId='+eventId;
    //Get the table for this event
    //Set the event Id for items that need it
    //$('#exceleventid').val(eventId);

    $.post("/app/effectsoptions",eventData,
        function(data) {
            try
            {
                imgaccents=[];
                imgoverlays=[];
                screenbgs=[];

                $('#effectsoptions').html(data);
                effectsModel=new EffectsViewModel($("#effectsoptions")[0]);
                ko.applyBindings(effectsModel);
                var vignetteStatus=$('#hasvignette').val();
                var theVignetteStatus=vignetteStatus=="1"?true:false;
                effectsModel.hasvignette(theVignetteStatus);

                //Set the handle Position
                //Set the Color Picker
                var filterOptions=$('#filterSelect :selected').attr('options')
                if(filterOptions){
                    var options=$('#filterSelect :selected').attr('options').split(";");
                    $(options).each(function(name,value) {
                        var optionVal=value.split(":");
                        switch (optionVal[0]){
                            case "color":
                                $("#colorpickertoggle").show();
                                if(optionVal[1]){
                                    $("#colorstring").attr('string',optionVal[1]);
                                    var hsl=optionVal[1].split(',');
                                    $.farbtastic('#colorpicker').setColor(hsl);

                                }
                                colorTintReq=true;
                                break;
                            default:
                                $("#colorpickertoggle").hide();
                                colorTintReq=false;
                        }

                    });
                }else{
                    $("#colorpickertoggle").hide();
                }


                //now attach to the id of color picker
                $('#filterSelect').on('change', function(){
                    // var filterId=$('#filterSelect :selected').attr('id').replace('fi_','');

                   var filterOptions=$('#filterSelect :selected').attr('options')
                  if(filterOptions){
                        var options=$('#filterSelect :selected').attr('options').split(",");
                        $(options).each(function(name,value) {
                            var optionVal=value.split(":");
                            switch (optionVal[0]){
                                case "color":
                                  $("#colorpickertoggle").show();
                                    if(optionVal[1])$("#color").val(optionVal[1]);
                                  colorTintReq=true;
                                break;
                            default:
                                $("#colorpickertoggle").hide();
                                colorTintReq=false;
                        }

                        });
                    }else{
                       $("#colorpickertoggle").hide();
                   }

                });
            }
            catch(err)
            {
                var txt="Error description: " + err.message + "\n\n";
                $("#formstatus").addClass('alert');
                $("#formstatus").addClass('alert-error');
                $("#formstatus").html(txt)
            }

            $('.clearasset').on('click', function(){
               //Find which one it is
               //remove the item
                switch($(this).attr("id")){
                    case 'clearaccent':
                        $('#imgaccent_prevLoader').html('');
                        effectsModel.clearimgAccent(true);
                        imgaccents=[];
                    break;
                    case 'clearoverlay':
                       $('#imgoverlay_prevLoader').html('');
                        effectsModel.clearimgOverlay(true);
                       imgoverlays=[];
                    break;
                    default:
                    break;
                }
            });


            $('.assetupload').on('change', function(){
                var thisId=$(this).attr("id");
                $("#"+thisId+"_prevLoader").html('<img src="/skin/images/ajax-loader.gif" alt="Uploading...."/>');
               // $("#"+thisId+"_form").hide();
               // processingNotif();
                var options={
                    success: function(data, statusText, xhr, $form){
                        clearMarkings();
                        try
                        {
                            var jsonParsed = JSON.parse(data);
                            if(jsonParsed.status.code=="200"){
                                var fileInfo=jsonParsed.data.fileInfo;
                                var imgHTMLColumn="<td><div class='thumbnail'><img src='"+fileInfo.filePath+"' alt='"+fileInfo.fileName+"' height='100' width='100'/></div>";
                                $("#"+thisId+"_previewTable tr:first td:first").before(imgHTMLColumn);

                                //Push on array to save asset
                                var thisArrCount=0;
                                switch(thisId){
                                    case 'imgoverlay':
                                        imgoverlays.push(fileInfo.filePath);
                                        if(imgoverlays.length<1)$("#"+thisId+"_form").show();

                                    break;
                                    case 'imgaccent':
                                        imgaccents.push(fileInfo.filePath);
                                        if(imgaccents.length<1)$("#"+thisId+"_form").show();

                                    break;
                                    case 'screenbg':
                                        imgoverlays.push(fileInfo.filePath);
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
        });
}

$(document).ready(function(){
    processCurrentSelection();
    $('#eventSelect').on('change', function(){
        processCurrentSelection();
    });

});
