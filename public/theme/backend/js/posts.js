var start_autosave = false;

$('.seo-keywords').tagsinput({});

$('#post-tags').tagsinput({
    typeahead: {
        source: tags.map(function (item) {
            return item.name;
        }),
        afterSelect: function () {
            this.$element[0].value = '';
        }
    }
});

dselect(document.querySelector('.post-authors'), {
    search: true,
    creatable: true,
});

flatpickr('.datetimepicker-inline', {
    enableTime: true,
    inline: true
});

$(".btn-post-edit").on('click', function (e) {
    e.preventDefault();
    $("#edit-task").val($(this).data('type'));
    if ($(this).data('type') == "publish") {
        $("#action-publish").addClass('d-none');
        $("#post-edit-go-back").removeClass('d-none');
    }
    $("#ajax-form-autosave").submit();
});

$("#a-preview").on('click', function (e) {
    e.preventDefault();
    if ($(this).attr("href") == "#") toastr["error"]("Nothing to preview.", "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
    else {
        window.open($(this).attr("href"), '_blank');
    }
});

function autoSave() {
    if ($("#edit-title").val() != "") {
        $("#edit-loader").show();
        $.ajax({
            type: "POST",
            url: submit_edit_url,
            data: $("#ajax-form-autosave").serialize(),
            success: function (result) {
                $("#edit-loader").fadeOut(500);
                if (result.status === '000') {
                    if (result.id) {
                        $("#edit-id").val(result.id);
                        $("#a-preview").attr("href", result.preview_url);
                    }
                    if (result.can_publish === true) {
                        $("#action-publish").prop('disabled', false);
                    }
                } else if (result.status === '097') {
                    window.location = result.message;
                } else {
                    toastr["error"](result.message, "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
                }
            },
            error: function (xhr, status, errorThrown) {
                $("#edit-loader").fadeOut(500);
                toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
            }
        });
    }
}

$(document).ready(function () {
    $(".a-input").on('change', function () {
        start_autosave = true;
    });
    calculate_word_count();
});

startAutosaveTimer();

function startAutosaveTimer() {
    setInterval(function () {
        console.log("Autosaving");
        if (start_autosave) autoSave();
    }, 10000);

}

function calculate_word_count(view) {
    var text = $(".word-countable").val();
    var counter = text.length;
    if (counter > 0) {
        var regex = /\s+/gi;
        var counter = text.trim().replace(regex, ' ').split(' ').length;
    }

    var str_counter = (counter == 1) ? " word" : " words";

    $("#" + view).html(counter);
    $("#" + view + "-text").html(str_counter);
    return;
}