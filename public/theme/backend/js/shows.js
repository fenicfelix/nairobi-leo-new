$("#file-upload-form").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: file_upload_url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: (data) => {
            this.reset();
            $("#media-id").val(data.file_id);
            $('#uploaded-image').attr('src', data.preview_url);
            $(".selected-image").attr('src', data.preview_url);
            toastr["success"](data.message, "SUCCESS", { closeButton: true, progressBar: true, timeOut: 5000 });
        },
        error: function (data) {
            toastr["error"]("Please try again.", "ERROR", { closeButton: true, progressBar: true, timeOut: 5000 });
        }
    });
});

dselect(document.querySelector('.show-hosts'), {
    search: true,
    creatable: true,
});