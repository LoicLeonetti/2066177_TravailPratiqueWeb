$(document).ready(function () {
    $(".boutonModal").on('click', function (event) {
        //ATTENTION: le event.preventDefault() est nécessaire sinon la modale ne s'affiche pas
        event.preventDefault();
        $.get($(this).attr('href'), function (data) {
            console.log(data)
            $("#modaleProduit").html(data).dialog({
                height: "auto",
                width: 700,
                modal: true
            });
        });
    });
});


