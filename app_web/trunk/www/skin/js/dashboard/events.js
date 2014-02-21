var eventModel;

function clearMarkings(){
    $("#formstatus").removeClass();
    $("#formstatus").html("");

    //Clear red markings
    $.each($("#formFields"), function(name, value){
        $(name).removeClass('red');
    });
}

var Button = function(name) {
    this.name = name;
    this.id=name;
    this.selected = ko.observable(false);
}

function processingNotif(){
    $("#formstatus").addClass('alert');
    $("#formstatus").addClass('alert-info');
    $("#formstatus").html("<img src='/skin/images/smallSpinner.gif'/> Processing");
}

function processCurrentSelection(){
    var eventId=$('#eventSelect :selected').attr('id');
    var eventData='eventId='+eventId;
    //Get the table for this event
    //Set the event Id for items that need it
    processingNotif();


    $.post("/dashboard/eventinfo",eventData,
        function(data) {
            try
            {
                //
                //Show in the box and then
                //var eventModel=ko.applyBindings(new EventUserViewModel($("#branduserForm")[0]));
                 $("#eventId").val(data.eventData.eventId).change();
                 $("#eventname").val(data.eventData.eventname).change();
                 $("#eventlocation").val(data.eventData.eventlocation).change();
                 $("#startdate").val(data.eventData.startdate).change();
                 $("#enddate").val(data.eventData.enddate).change();

                 var orientations=$("#orbtns button").get();
                 if(data.eventData.isPortrait){
                    orientations[0].click();
                 }else{
                        orientations[1].click();
                    }

                eventModel.allowprinting(data.eventData.allowPrinting);
                eventModel.social_facebook(data.eventData.hassocial_facebook);
                eventModel.social_twitter(data.eventData.hassocial_twitter);
                eventModel.social_googleplus(data.eventData.hassocial_googleplus);

                 $("#createEvent").html("Update Event");

                if(data.eventData.allowPrinting=='1'){
                    $('#allowprinting').attr('checked')
                }else{
                    $('#allowprinting').removeAttr('checked');
                }
                 $("#createEventPanel").show('slow');
                clearMarkings();
            }
            catch(err)
            {
                var txt="Error description: " + err.message + "\n\n";
                $("#formstatus").html(data);
            }


        });

}


function processCurrentBrandSelection(){
    //Hide or show default
    var hasDefault=$('#brandSelect :selected').attr('default');
    if(hasDefault>0){
        //Show Use Brand Default
        $('#usedefault').attr('disabled',false);
        $('#usedefaultdef').hide();
    }else{
        eventModel.usedefault(false);
        $('#usedefault').attr('disabled',true);
        $('#usedefaultdef').show();
    }


    //Can always make this the default
}
    var EventUserViewModel = function() {
    var self=this;
    self.brandId = ko.observable();
    self.eventId = ko.observable();
    self.eventname = ko.observable();
    self.eventlocation = ko.observable();
    self.startdate = ko.observable();
    self.enddate = ko.observable();
    self.allowprinting=ko.observable(false);
    self.usedefault=ko.observable(false);
    self.setasdefault=ko.observable(false);
    self.orientationButtons = ko.observableArray([ new Button('Portrait'),
                                       new Button('Landscape')]);
    self.selectedButton = ko.observable();
    self.selectButton = function(button) {
        if (self.selectedButton()) self.selectedButton().selected(false);
        self.selectedButton(button);
        self.selectedButton().selected(true);
    }
    self.profileHasChanged=ko.observable(false);

    //Social
    self.social_facebook=ko.observable(true);
    self.social_twitter=ko.observable(true);
    self.social_googleplus=ko.observable(true);


    self.createEventClick = function() {
        //Removal here
        clearMarkings();
        processingNotif();
        $("#formstatus").addClass('alert');
        $("#formstatus").addClass('alert-success');

        if ($("#selectBrand").length > 0){   //on selection
            self.brandId=$('#selectBrand :selected').attr('id');
        }else if (($("#brandId").length > 0)){
            self.brandId=$('#brandId').val();
        }
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/dashboard/createbrandevent",postData,
            function(data) {
                try{
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        clearMarkings();
                        //Status
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-success');
                        $("#formstatus").html(jsonParsed.status.msg);

                        if(!$("#eventId").val()){  //Meaning that it is a new creation
                            $("#eventname").val("");
                            $("#eventlocation").val("");
                            $("#startdate").val("");
                            $("#enddate").val("");
                        }else{
                            //Change the selection name of recently updated
                            var selectId=$("#eventId").val();
                            var newName=$("#eventname").val();
                            $('#eventSelect #'+selectId).html(newName);
                        }
                    }
                    else{
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
                    $("#formstatus").html('<button class="close" data-dismiss="alert">×</button>'+data);
                }
            });
    };



    self.endEventClick = function() {
        //Removal here
        clearMarkings();
        processingNotif();
        $("#formstatus").addClass('alert');
        $("#formstatus").addClass('alert-success');

        if ($("#eventSelect").length > 0){
            self.eventId=$('#eventSelect :selected').attr('id');
        }
        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/dashboard/requestendevent",postData,
            function(data) {
                try{
                    var jsonParsed = JSON.parse(data);
                    if(jsonParsed.status.code=="200"){
                        clearMarkings();
                        //Status
                        $("#formstatus").addClass('alert');
                        $("#formstatus").addClass('alert-success');
                        $("#formstatus").html(jsonParsed.status.msg);

                        $("#emailAddress").val("");
                        $("#username").val("");
                        $("#password").val("");
                        $("#confirmPassword").val("");

                        $("#brandName").val("");
                    }
                    else{
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
                    $("#formstatus").html('<button class="close" data-dismiss="alert">×</button>'+data);
                }
            });
    };


    self.profileChanged = function() {
        //self.profileHasChanged($("#profileModel").attr("profilename")!=$("#profile_profileName").val());
        self.profileHasChanged(true);
    };

};


$(document).ready(function(){
    eventModel=new EventUserViewModel($("#branduserForm")[0]);
    ko.applyBindings(eventModel); // This makes Knockout get to work

    processCurrentBrandSelection();

    $('#useSelectEvent').on('click', function(){
        $("#eventTypeTitle").html("Update Event");
        $("#brandSelection").hide();
        $("#eventSelection").show();
        $("#createEventPanel").hide('slow');
        $("#eventEndButton").show();
        processCurrentSelection();

    });

    $('#useBrandSelection').on('click', function(){
        $("#createEventPanel").hide('slow')
        $("#eventId").val("");
        $("#eventname").val("");
        $("#eventlocation").val("");
        $("#startdate").val("");
        $("#enddate").val("");
        $("#createEvent").html("Create Event");
        $("#brandSelection").show();
        $("#eventSelection").hide();
        $("#createEventPanel").show('slow');
        $("#eventEndButton").hide();
        $("#eventTypeTitle").html("New Event");
        $("#eventId").val('').change();
        $('#allowprinting').removeAttr('checked');
    });

    $('#eventSelect').on('change', function(){
        processCurrentSelection();
    });

    $('#brandSelect').on('change', function(){
        processCurrentBrandSelection();
    });

});
