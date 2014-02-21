var tokenFieldName="authToken";
$(document).ready(function(){
    var currentSession='';
    var currentJSON=null;
    $("#currentErrorMsg").html('No Errors');
    $("#currentErrorMsg").hide();
    $('#apiCallsSelect').on('change', function(){
        processCurrentSelection();

    });

    function processCurrentSelection(){
    var currentControllerAction='';
    var controlleractionParamsArr='';
    var method='';
    var params='';
    var paramsArr=[]
    var htmlForm='';
    var apiString=$('#apiCallsSelect :selected').attr('apiString');
    var callType=$('#apiCallsSelect :selected').attr('calltype');
    //$("#currentErrorMsg").html('');
    //Set Defaults
    $("#getGeo").hide();
    $("#currentErrorMsg").hide();
    $("#currentErrorMsg").html('');
    var latAvail=false;
    var lonAvail=false;
    //Break Up String for Method
    controlleractionParamsArr= apiString.split('?');
    currentControllerAction=controlleractionParamsArr[0];
   $("#apiSubmitForm").attr("action",currentControllerAction+'/');

    params=controlleractionParamsArr[1];

   switch(callType){
       case "0":
       default:
           $("#apiSubmitForm").attr("method","post");
           $("#apiSubmitForm").attr("enctype","");
            //REST CALL
            paramsArr=params.split('&');

            $.each(paramsArr, function(index, paramVal) {
                var paramValArr=paramVal.split('=');
                var theParam=paramValArr[0];
                var theVal=paramValArr[1];
                var htmlInputLine='';

                if(theParam!='method'){
                    if (theParam && theParam != undefined){
                        htmlInputLine+='<div class="control-group">';
                        htmlInputLine+='<label class="control-label" for="apiCalls">'+theParam+'</label>';
                        htmlInputLine+='<div class="controls">';

                       if(paramValArr[0]!=tokenFieldName){
                            var inputType=(paramValArr[0]!='password')?'text':'password';
                            htmlInputLine+='<input type="'+inputType+'" id="api_'+theParam+'" name="'+theParam+'" placeholder="'+theVal+'" />';
                        }else{
                            //Get Stored Session
                            htmlInputLine+='<input type="text" name="'+theParam+'" value="'+currentSession+'" placeholder="session not available" />';
                        }
                     if(paramValArr[0]=='lat')latAvail=true;
                     if(paramValArr[0]=='lon')lonAvail=true;

                     htmlInputLine+='</div>';
                     htmlInputLine+='</div>';
                    }
                }else{
                    method=paramValArr[1];
                    htmlInputLine+='<input type="hidden" id="api_'+theParam+'" name="method" value="'+method+'" />';
                }
                htmlForm+=htmlInputLine;
            });
           break;

           case "1":
               $("#apiSubmitForm").attr("method","post");
               $("#apiSubmitForm").attr("enctype","multipart/form-data");
               paramsArr=params.split('&');
               $.each(paramsArr, function(index, paramVal) {
                   var htmlInputLine='';
                   var paramValArr=paramVal.split('=');
                   var theParam=paramValArr[0];
                   var theVal=paramValArr[1];
                   htmlInputLine='';
                   htmlInputLine+='<div class="control-group">';
                   htmlInputLine+='<label class="control-label" for="apiCalls">'+theParam+'</label>';
                   htmlInputLine+='<div class="controls">';
                   theParamParts=theVal.split(','); //type and instructions

                   if(paramValArr[0]!=tokenFieldName){
                        htmlInputLine+="<input type='"+theParamParts[0]+"' name='"+theParam+"' placeholder='"+theParamParts[1]+"' />"
                   }else{
                        //Get Stored Session
                        htmlInputLine+='<input type="text" name="'+theParam+'" value="'+currentSession+'" placeholder="session not available" />';
                   }
                   htmlInputLine+='</div>';
                   htmlInputLine+='</div>';

                   htmlForm+=htmlInputLine;
               });
           break;
        }

    if(latAvail && lonAvail)$("#getGeo").show();
    $("#currentApiString").html(apiString); //Show Full Api Sting
    $("#currentControllerAction").html(currentControllerAction);
    $("#currentMethod").html(currentControllerAction.replace('/',''));
    $('#apiForm').html(htmlForm);
    $("#jsonResponse").html('');
    }

    processCurrentSelection();
    $('#currentSession').html(currentSession);


    $('#submitApiForm').on('click', function(){
        $("#currentErrorMsg").hide();
        $("#currentErrorMsg").html('');
        var options={
            type:      "post",
            success: function(status_data, statusText, xhr, $form){
                try
                {           //Redirect if stream- only run this on JSON
                    currentJSON=status_data;

                    if($("#prettyTableJSON").hasClass('active')){
                        $("#jsonResponse").html(prettyPrint(currentJSON));
                    }else if($("#prettyTextJSON").hasClass('active')){
                        $("#jsonResponse").html(formatJson(JSON.stringify(currentJSON)));
                    }else{
                        $("#prettyTextJSON").button('toggle');
                        $("#jsonResponse").html(formatJson(JSON.stringify(currentJSON)));
                    }

                    if(currentJSON.hasOwnProperty("data")){
                        if(currentJSON.data.hasOwnProperty("userAuthToken")){//May have to check sessionId as well
                            if(currentJSON.data.userAuthToken){
                                currentSession=currentJSON.data.userAuthToken;
                            $('#currentSession').html(currentSession);
                            }
                        }
                    }

                }catch(err){
                    $("#currentErrorMsg").html('Error:'+err.message);
                    $("#currentErrorMsg").show();
                }
            }
        };
        $("#apiSubmitForm").ajaxForm(options).submit();
        $("#jsonResponse").html('<div style="text-align: center;vertical-align:middle;"><img src="/skin/images/ajax-loader.gif" alt="Uploading...."/></div>');
    });

    $('#getGeoBtn').on('click', function(){
        getCurrentLocation();
    });

    //Enable buttons
    $('#prettyTableJSON').button();
    $("#prettyTableJSON").click(function() {
        $("#jsonResponse").html(currentJSON?prettyPrint(currentJSON):"No Data To Process");
    });

    $("#prettyTextJSON").click(function() {
        //$("#jsonResponse").html(formatJson(JSON.stringify(currentJSON)));
        $("#jsonResponse").html(currentJSON?formatJson(JSON.stringify(currentJSON)):"No Data To Process");
    });

});

function getCurrentLocation(){  //Move to separate js
if (navigator.geolocation)
{
    navigator.geolocation.getCurrentPosition(
        function (position) {
            // Did we get the position correctly?
             var latlonStatus= '('+position.coords.latitude+'/'+position.coords.longitude+")";
            $("#geoStatus").html(latlonStatus);
            $("#api_lat").val(position.coords.latitude);
            $("#api_lon").val(position.coords.longitude);
            // To see everything available in the position.coords array:
            // for (key in position.coords) {alert(key)}
    },        // next function is the error callback
        function (error)
        {
            switch(error.code)
            {
                case error.TIMEOUT:
                    alert ('Timeout');
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert ('Position unavailable');
                    break;
                case error.PERMISSION_DENIED:
                    alert ('Permission denied');
                    break;
                case error.UNKNOWN_ERROR:
                    alert ('Unknown error');
                    break;
            }
        }
    );
    }else{
     $("#geoStatus").html("Not Supported");
    }
}

