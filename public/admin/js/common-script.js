function printErrorMsg (msg) {
    $(".print-error-msg").find("ul").html('');
    $.each( msg, function( key, value ) {
        $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
    });
    $(".print-error-msg").delay(1000).addClass("in").toggle(true).fadeOut(5000);
}

function showSuccessMsg (msg) {
    $(".print-success-msg").html(msg);
    $(".print-success-msg").delay(1000).addClass("in").toggle(true).fadeOut(3000);
}

function showSuccessToaster (msg) {
    toastr.success(msg)
}

function showErrorToaster (msg) {
    toastr.error(msg)      
}

$(".check_numeric").keydown(function (event) {
    if ((event.keyCode >= 48 && event.keyCode <= 57) || 
    (event.keyCode >= 96 && event.keyCode <= 105) || 
    event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
    event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) 
    {

    }
    else
    {
    event.preventDefault();
    }

});

function getChildElements(parent_id = null, selected = null, element = null, route = null){
    $.ajax({
        type: 'GET',
        url: route, data:{'parent_id': parent_id },
        dataType: 'json',
        delay: 250,
        success: function(data) {
            var selectTerms = '<option value="">Please select </option>';
            $.each(data.data, function(key, value) {
              selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
            });
            var select = $('#'+element);
            select.empty().append(selectTerms);
        }
    });
}

$(".print-success-msg").delay(1000).addClass("in").toggle(true).fadeOut(5000);
$(".print-error-msg").delay(1000).addClass("in").toggle(true).fadeOut(5000);

$(".card-alert .close").click(function () {
    $(this).closest(".card-alert").fadeOut("slow");
});