function validerUnicite()
{
    var nomChomeur = $("#chomeur_nom").val();
    //alert("On blur " + nomChomeur);

    $.get("validerUnicite?nomChomeur=" + nomChomeur, function(data, status)
{
    if (data =="doublon")
        alert("Ce nom existe déjà en BD");
});
}