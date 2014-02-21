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

var SendReportViewModel = function() {
    var self=this;
    self.eventId=ko.observable();
    self.emailAddress = ko.observable();

    self.sendUsageReportClick = function() {
        //Removal here
        clearMarkings();
        processingNotif();
        $("#formstatus").addClass('alert');
        $("#formstatus").addClass('alert-success');

        self.eventId=$('#formEventId').val();

        var thisModelJSON=ko.toJSON (self);
        var postData="json="+thisModelJSON;
        $.post("/dashboard/sendusagereport",postData,
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
    ko.applyBindings(new SendReportViewModel($("#sendreportform")[0])); // This makes Knockout get to work
    var tableId=$('#tableId').attr("theid");
    if(tableId=="eventList")$("#"+tableId).tablesorter();
});
