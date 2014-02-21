$(document).ready(function(){
    // for The Search Bar
 /*   swapValues = [];
    $(".swap").each(function(i){
        swapValues[i] = $(this).val();
        $(this).focus(function(){
            if ($(this).val() == swapValues[i]) {
                $(this).val("");
            }
            $(".shaker").effect("shake", {times:2 }, 50);

        }).blur(function(){
                if ($.trim($(this).val()) == "") {
                    $(this).val(swapValues[i]);
                }
            });
    });*/

    // icon shake

    $(".shaker").hover(function () {
        $(this).effect("shake", { times:2 }, 100);
    });

});