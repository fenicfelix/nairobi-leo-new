$(function () {

    summernoteStyle()

    $('.summernote').summernote({
        height: 300,
        callbacks: {
            onChange: function (contents, $editable) {
                if ($(this).summernote('isEmpty') && contents != '') {
                    $(this).summernote('code', '')
                }
            },
            onImageUpload: function (image) {
                uploadImage(image[0]);
            }
        },
        popover: {
            image: [
                ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']],
                ['custom', ['captionIt']],
            ],
        },
        captionIt: {
            figureClass: 'wwt-intext-image',
            figcaptionClass: 'wwt-intext-image-caption',
            captionText: 'PHOTO | COURTESY'
        }
    });

    function uploadImage(image) {
        var data = new FormData();
        data.append("file", image);
        $.ajax({
            url: intext_image_upload_url,
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            type: "POST",
            success: function (result) {
                console.log(result);
                if (result.status == "000") {
                    var image = $('<img>').attr('src', result.preview_url);
                    console.log(image);
                    $('.summernote').summernote("insertNode", image[0]);
                } else {
                    toastr["error"](result["message"], "Sorry!", {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 5000
                    });
                }
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

})

$('.seo-keywords').tagsinput({});

flatpickr('.datetimepicker-inline', {
    enableTime: true,
    inline: true
})

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