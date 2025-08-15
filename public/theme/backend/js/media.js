$(function () {

    let imgDestination = "";
    let page = 1;

    fetch_images();

    $("#search-media-form").on('submit', function (e) {
        e.preventDefault();
        page = 1;
        fetch_images();
    });

    $(".page-link").on('click', function (e) {
        e.preventDefault();
        if ($(this).data('page') == "next") page++;
        else page--;

        if (page < 0) page = 0;
        fetch_images()
    });

    function fetch_images() {
        $("#ajax-load-more").removeClass("hidden");
        var data = { page: page, s: $("#search-input").val() };
        $.ajax({
            url: fetch_thumbnails_url,
            type: 'GET',
            data: data,
            success: function (result) {
                $("#ajax-load-more").addClass("hidden");
                if (result["status"] == "000") {
                    $("#add-media-gallery").html(result["items"]);
                    if (result["previous_page"] == 0) $("#pagination-prev").addClass("disabled");
                    else {
                        $("#library_page-prev").val(result["previous_page"]);
                        $("#pagination-prev").removeClass("disabled");
                    }
                    if (result["next_page"] == 0) $("#pagination-next").addClass("disabled");
                    else {
                        $("#library_page-next").val(result["next_page"]);
                        $("#pagination-next").removeClass("disabled");
                    }
                }
            }
        });
    }

    $("#update-image-tags-form").on('submit', function (e) {
        e.preventDefault();
        $("#reset-loader").show();
        submit_ajax_upload_data(image_tags_update_url, $("#update-image-tags-form").serialize(), "upload");
    });

    $(".btn-upload-img").on('click', function (e) {
        imgDestination = $(this).data('dest');
        console.log(imgDestination);
    });

    $("#update-attachment-details").on("submit", function (e) {
        e.preventDefault();
        $("#media-loader").show();
        submit_ajax_upload_data(image_tags_update_url, $("#update-attachment-details").serialize(), "media");

    });

    function submit_ajax_upload_data(url, data, src) {
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (result) {
                $("#" + src + "-loader").fadeOut(500);
                if (result.status === '000') {
                    toastr["success"](result.message, "SUCCESS", { closeButton: true, progressBar: true, timeOut: 5000 });
                    //Reset submit fields
                    $("#update-attachment-details")[0].reset();
                    $("#img-url").html("");
                    $('#uploaded-image').attr('src', "");
                    //Reset submit fields

                    $("#modalSetFeaturedImage").modal("hide");
                    $("#upload-" + imgDestination).val($("#" + src + "-id").val());
                    $('#img-' + imgDestination).attr('src', result.preview_url);

                    //$(".selected-image").attr('src', result.preview_url);
                } else {
                    toastr["error"](result.message, "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
                }
            },
            error: function (xhr, status, errorThrown) {
                $("#" + src + "-loader").fadeOut(500);
                toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
            }
        });
    }

    $(document).on("click", '.a-media-tab', function (e) {
        $("#img-url").html("");
        $("#update-attachment-details")[0].reset();
    });

    $(document).on("click", ".media-select", function (e) {
        $("#media-" + $(this).data("id")).prop('checked', 'checked');
        $("#media-id").val($(this).data("id"));
        $("#media-alt_text").val($(this).data("alt"));
        $("#media-title").val($(this).data("title"));
        $("#media-caption").val($(this).data("caption"));
        $("#media-description").val($(this).data("description"));
        $("#img-url").html($(this).attr("src"));
    });

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
                $("#img-" + imgDestination).attr('src', data.preview_url);
                $("#img-url").html(data.preview_url);
                toastr["success"](data.message, "SUCCESS", { closeButton: true, progressBar: true, timeOut: 5000 });
            },
            error: function (data) {
                toastr["error"]("Please try again.", "ERROR", { closeButton: true, progressBar: true, timeOut: 5000 });
            }
        });
    });

    $("#ajax-form-delete-img").on('submit', function (e) {
        e.preventDefault();
        let id = $("#media-id").val();
        if (id != "") {
            $("#delete-img-id").val(id);
            $.ajax({
                type: "POST",
                url: delete_image_url,
                data: $("#ajax-form-delete-img").serialize(),
                success: function (result) {
                    $("#delete-loader").fadeOut(500);
                    if (result.status === '000') {
                        location.reload();
                    } else {
                        toastr["error"](result.message, "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
                    }
                },
                error: function (xhr, status, errorThrown) {
                    $("#delete-loader").fadeOut(500);
                    toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
                }
            });
        } else {
            toastr["error"]("Please select an image to delete", "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
        }
    });
});