$(function () {

    $("#loadMedia").on('click', function (e) {
        e.preventDefault();
        $("div.display-media").removeClass('d-none');
        $("#loadMedia").addClass('d-none');
        $("#hideMedia").removeClass('d-none');
    });

    $("#hideMedia").on('click', function (e) {
        e.preventDefault();
        $("div.display-media").addClass('d-none');
        $("#loadMedia").removeClass('d-none');
        $("#hideMedia").addClass('d-none');
    });

});
