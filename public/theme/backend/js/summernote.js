$(function () {

    summernoteStyle()

    $('.summernote').summernote({
        height: 300,
        callbacks: {
            onChange: function (contents, $editable) {
                if ($(this).summernote('isEmpty') && contents != '') {
                    $(this).summernote('code', '')
                } else {
                    start_autosave = true;
                }
                calculate_word_count($(this).data('counter_result'));
            },
            onImageUpload: function (image) {
                uploadImage(image[0]);
            },
            onPaste: function (e) {
                e.preventDefault();
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                if (bufferText.includes('iframe') || bufferText.includes('twitter-tweet') || bufferText.includes('instagram-media')) {
                    $(this).summernote('pasteHTML', bufferText);
                } else {
                    document.execCommand('insertText', false, bufferText);
                }
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

    $('.long-description').summernote({
        height: 300,
        callbacks: {
            onImageUpload: function (image) {
                uploadImage(image[0]);
            },
            onPaste: function (e) {
                e.preventDefault();
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                $(this).summernote('pasteHTML', bufferText);
                // if (bufferText.includes('iframe') || bufferText.includes('twitter-tweet')) {
                //     e.preventDefault();
                //     $(this).summernote('pasteHTML', bufferText);
                // } else {
                //     return true;
                // }
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

    $('.in-summary').summernote({
        height: 150
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
                if (result.status == "000") {
                    var image = $('<img>').attr('src', result.preview_url);
                    $("#img-url").html(data.preview_url);
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

});