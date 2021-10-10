$(document).ready(function () {
    var ladate = new Date();
    var annee = ladate.getFullYear()
    $('.date').empty();
    $('.date').append("<option value=''>-</option>")
    for (let i = annee; i > 1936; i--){
        $('.date').append(
            "<option value="+i+">"+i+"</option>"
        )
    };

    $("input[data-preview]").change(function() {
        var $input = $(this);
        var fileReader = new FileReader();
        fileReader.readAsDataURL(this.files[0]);
        fileReader.onload = function(fileEvent) {
            $($input.data('preview')).attr('src', fileEvent.target.result);
        };
    });

    AOS.init();
})