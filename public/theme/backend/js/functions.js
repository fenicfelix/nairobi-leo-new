$(() => {
    // Run datatable
    let recover_url = "";
    let permanent_delete_url = "";
    let table = $('#dt-server-side').DataTable({
        "dom": 'lfBrtip',
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [tableDefaultFilter],
        "ajax": {
            "url": datatable_url,
            "dataType": "json",
            "type": "GET",
        }
    });

    $(document).on("click", ".seo-trigger", function (e) {
        calculate_seo_status("edit");
    });

    $("#edit-seo_title").on('click', function (e) {
        calculate_seo_status("edit");
    });

    $(".slugify").on('keyup', function (e) {
        let title = this.value;
        let slug = "";
        if (title != '') {
            slug = get_slug(title);
        }
        $("#" + $(this).data('task') + "-slug").val(slug);
        if ($(this).data('type') == "title") {
            $("#" + $(this).data('task') + "-hint-seo-permalink").html(slug);
            $("#" + $(this).data('task') + "-hint-slug").html(slug);
            $("#" + $(this).data('task') + "-hint-seo-title").html(title);
            $("#" + $(this).data('task') + "-seo_title").val(title);
        }
    });

    $(".show-hide-password").on('click', function (e) {
        $(this).toggleClass("active");
        if ($(this).hasClass("active")) {
            $(this).text("Hide").prev("input").attr("type", "text");
        } else {
            $(this).text("Show").prev("input").attr("type", "password");
        }
    });

    $(".seo").on('keyup', function (e) {
        let tag = $(this).data('task') + "-seo_" + $(this).data('type');
        var body = $("#" + tag).val();
        $("#" + $(this).data('task') + "-hint-seo-" + $(this).data('type')).html(body);
        if ($(this).data('type') == "title") {
            var slug = get_slug(body);
            $("#" + $(this).data('task') + "-slug").val(slug);
            $("#" + $(this).data('task') + "-hint-slug").html(slug);
            $("#" + $(this).data('task') + "-hint-seo-permalink").html(slug);

        }
        if ($(this).data('type') == "description") {
            var trimed_body = body;
            if (trimed_body.length > 155) trimed_body = trimed_body.substring(0, 155) + "...";

            $("#" + $(this).data('task') + "-hint-seo-description").html(trimed_body);
        }
        calculate_seo_status($(this).data('task'));
    });

    $('.slug').on('focusout', function (e) {
        let body = $('#' + $(this).data('task') + '-slug').val();
        var slug = get_slug(body);
        $('#' + $(this).data('task') + '-slug').val(slug);
        $("#" + $(this).data('task') + "-hint-seo-permalink").html(slug);
    });

    $(".btn-post-add").on('click', function (e) {
        e.preventDefault();
        $("#add-task").val($(this).data('type'));
        $("#ajax-form-add").submit();
    });



    $("#ajax-form-add").on('submit', function (e) {
        e.preventDefault();
        $("#add-loader").show();
        submit_ajax_data(submit_add_url, $("#ajax-form-add").serialize(), "add");
    });

    $("#ajax-form-edit").on('submit', function (e) {
        e.preventDefault();
        $("#edit-loader").show();
        submit_ajax_data(submit_edit_url, $("#ajax-form-edit").serialize(), "edit");
    });

    $("#ajax-form-autosave").on('submit', function (e) {
        e.preventDefault();
        $("#edit-loader").show();
        submit_ajax_data(submit_edit_url, $("#ajax-form-autosave").serialize(), "edit");
    });

    $("#ajax-form-change-password").on('submit', function (e) {
        e.preventDefault();
        $("#change-loader").show();
        submit_ajax_data(password_change_url, $("#ajax-form-change-password").serialize(), "change");
    });

    $("#ajax-form-reset-password").on('submit', function (e) {
        e.preventDefault();
        $("#reset-loader").show();
        submit_ajax_data(password_change_url, $("#ajax-form-reset-password").serialize(), "reset");
    });

    $("#logout").on('click', function (e) {
        e.preventDefault();
        $("#logout-form").submit();
    });

    $(document).on("click", ".btn-trash", function (e) {
        e.preventDefault();

        submit_delete_url = $(this).data('href');
        console.log(submit_delete_url);
        $("#delete-id").val($(this).data('id'));
        $("#delete-name").html("'" + $(this).data('name') + "'");
    });

    $("#ajax-form-delete").on('submit', function (e) {
        e.preventDefault();
        $("#delete-loader").show();
        submit_ajax_data(submit_delete_url, $("#ajax-form-delete").serialize(), "delete");
    });

    $("#ajax-lineup-clear").on('submit', function (e) {
        e.preventDefault();
        $("#delete-loader").show();
        submit_ajax_data(submit_delete_url, $("#ajax-lineup-clear").serialize(), "reload");
    });

    $(document).on("click", ".btn-recover", function (e) {
        e.preventDefault();

        recover_url = $(this).data('href');
        console.log(recover_url);
        $("#recover-id").val($(this).data('id'));
        $("#recover-name").html("'" + $(this).data('name') + "'");
    });

    $("#ajax-form-recover").on('submit', function (e) {
        e.preventDefault();
        $("#recover-loader").show();
        submit_ajax_data(recover_url, $("#ajax-form-recover").serialize(), "recover");
    });

    $(document).on("click", ".btn-delete-permanent", function (e) {
        e.preventDefault();

        permanent_delete_url = $(this).data('href');
        console.log(permanent_delete_url);
        $("#permanent-delete-id").val($(this).data('id'));
        $("#permanent-delete-name").html("'" + $(this).data('name') + "'");
    });

    $("#ajax-form-permanent-delete").on('submit', function (e) {
        e.preventDefault();
        $("#permanent-delete-loader").show();
        submit_ajax_data(permanent_delete_url, $("#ajax-form-permanent-delete").serialize(), "permanent-delete");
    });

    $("#ajax-form-permanent-delete-user").on('submit', function (e) {
        e.preventDefault();
        permanent_delete_url = $(this).attr("action");
        console.log(permanent_delete_url);
        $("#permanent-delete-user-loader").show();
        submit_ajax_data(permanent_delete_url, $("#ajax-form-permanent-delete-user").serialize(), "reload");
    });

    function get_slug(title) {
        var slug = slugify(title, {
            remove: /[*+~.()'"!:@,/]/g, // remove symbols
            lower: true // convert to lowercase
        });
        return slug;
    }

    function calculate_seo_status(task) {
        let status = 0;
        let seo_keyword = $("#" + task + "-seo_keywords").val();
        let seo_title = $("#" + task + "-seo_title").val();
        let seo_description = $("#" + task + "-seo_description").val();

        let status_keywords = '<div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>';
        let status_title = status_keywords;
        let status_description = status_keywords;

        if (seo_keyword != "") {
            status += 20;
            //Check if the keyword is unique and if so, add 10 points
            status_keywords = '<div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>';
        }
        if (seo_title != "") {
            status += 20;
            let length = seo_title.length;
            if (length > 0) {
                if (length >= 50 && length <= 60) {
                    status += 10;
                    status_title = '<div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>';
                } else {
                    status_title = '<div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>';
                }
            }
        }

        if (seo_description != "") {
            status += 20;
            let length = seo_description.length;
            if (length > 0) {
                if (length >= 155 && length <= 160) {
                    status += 10;
                    status_description = '<div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>';
                } else {
                    status_description = '<div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>';
                }
            }
        }

        $("#" + task + "-seo_status").val(status);
        $("#" + task + "-seo-status").html(update_seo_status(status));
        $("#" + task + "-seo_keywords_progress").html(status_keywords);
        $("#" + task + "-seo_title_progress").html(status_title);
        $("#" + task + "-seo_description_progress").html(status_description);

        $("#" + task + "-txt-seo-title").html($("#" + task + "-seo_title").val().length);
        $("#" + task + "-txt-seo-description").html($("#" + task + "-seo_description").val().length);
    }

    function submit_ajax_data(url, data, src) {
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (result) {
                $("#" + src + "-loader").fadeOut(500);
                if (result.status === '000') {
                    if (table) table.draw();
                    if (src == "reload") {
                        window.location.reload();
                    } else {
                        toastr["success"](result.message, "SUCCESS", { closeButton: true, progressBar: true, timeOut: 5000 });

                        try {
                            if (result.preview_url && src == "preview") window.location = result.preview_url;
                            if (result.edit_url) window.location = result.edit_url;
                        } catch (error) {
                            console.log(error);
                        }
                        if (src == "delete") {
                            $(".delete-modal").modal("hide");
                        } else if (src == "recover") {
                            $("#recoverConfirmationModal").modal("hide");
                        } else if (src == "permanent-delete") {
                            $("#permanentDeleteConfirmationModal").modal("hide");
                        } else {
                            if ($('.tags-input').length) {
                                $('.tags-input').tagsinput('removeAll');
                            }

                            //clear attributes edit
                            if ($("#edit-option_values").length || $("#edit-option_identifiers").length) {
                                $("#edit-option_values").html('');
                                $("#edit-option_identifiers").html('');
                            }
                        }

                        if (src == "add") $("#ajax-form-" + src)[0].reset();

                        if (src == "upload") {
                            $("#modalSetFeaturedImage").modal("hide");
                        }
                    }
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

    $('.upload-profile-pic').on("submit", function (e) {
        e.preventDefault();
        $("#img-upload-loader").show();
        var task = $(this).data('task');
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: upload_profile_image_url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                this.reset();
                $("#img-upload-loader").fadeOut(500);
                console.log(data);
                $('#' + task + '-thumbnail').val(data.path);
                $('#' + task + '-uploaded-image').attr('src', data.preview_url);
                toastr["success"](data.message, "SUCCESS", { closeButton: true, progressBar: true, timeOut: 5000 });
            },
            error: function (data) {
                toastr["error"]("Please try again.", "ERROR", { closeButton: true, progressBar: true, timeOut: 5000 });
            }
        });
    });

    //-------------------------- Settings

    $(".ajax-form-settings").on('submit', function (e) {
        e.preventDefault();
        let task = $(this).data('task');
        submit_settings(submit_add_url, $(this).serialize(), task);
    });

    function submit_settings(url, data, src) {
        $("#settings-loader").show();
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (result) {
                console.log(result);
                $("#settings-loader").fadeOut(500);
                if (result.status === '000') {
                    toastr["success"](result.message, "SUCCESS", { closeButton: true, progressBar: true, timeOut: 10000 });
                } else {
                    toastr["error"](result.message, "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
                }
            },
            error: function (xhr, status, errorThrown) {
                $("#settings-loader").fadeOut(500);
                toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
            }
        });
    }
});