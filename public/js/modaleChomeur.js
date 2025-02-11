
$(document).ready( function()
{
    $(".boutonModale").on('click', function(event)
    {
        //alert("Clic sur détails chome");
        event.preventDefault();
        $.get($(this).attr('href'), function(data) 
        {
            $("#modaleChomeur").html(data).dialog(
                {
                    height: "auto",
                    width:700,
                    modal:false
                });
        });
    });
});

