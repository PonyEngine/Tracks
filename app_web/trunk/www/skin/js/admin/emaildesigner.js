function testButton(obj, event, key)
{
    alert("Insert either a user, space,track");
}

$(document).ready(
    function()
    {
        $('#redactor_content').redactor({
            focus: true,
            buttonsAdd: ['|', 'button1'],
            buttonsCustom: {
                button1: {
                    title: 'Objects',
                    callback: testButton
                }
            }
        });

        $('#redactor_content').redactor({ css: 'docstyle.css', autoresize: true, fixed: true });


        $("#saveEmail").on('click', function(){
            var textToUpload=$('#redactor_content').getCode();
            alert(textToUpload);
        })
    }



);