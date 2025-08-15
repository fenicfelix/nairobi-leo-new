if (selectedMenu) load_menu();
$(".menu-item-form").on('submit', function (e) {
    e.preventDefault();
    var item = $(this).serializeObject();
    item.order = count;
    menu_array.push(item);
    console.log(menu_array);
    $("#add-menu-items").val(JSON.stringify(menu_array));
    prepare_html(item);
    count++;
});

function prepare_html(item) {
    var html = '<li class="list-group-item" id="item' + item.order + '">' +
        '<div class="row justify-content-between">' +
        '<div class="col-8" id="item-title-' + item.order + '">' + item.display_title + '</div>' +
        '<div class="col-4">' +
        '<div class="float-end text-capitalize"><span class="text-muted">' + item.type + '</span><a class="btn btn-link collapse-toggle" data-order="' + item.order + '" type="button" data-bs-toggle="collapse" data-bs-target="#newMenuItem' + item.order + '" aria-expanded="false" aria-controls="newMenuItem' + item.order + '"><i id="more-less-' + item.order + '" class="fas fa-chevron-down"></i></a></div>' +
        '</div>' +
        '</div>' +
        '<div class="collapse" id="newMenuItem' + item.order + '"><hr>' +
        '<div class="border-solid">' +
        '<form class="update-menu-item-inline" action="">' +
        '<label class="fst-italic">Navigation Headline</label>' +
        '<div class="col-12">' +
        '<input type="hidden" name="title" value="' + item.display_title + '">' +
        '<input type="text" class="form-control update-display-title" data-id="' + item.order + '" name="display_title" value="' + item.display_title + '">' +
        '</div>' +
        '<label class="mt-3 fst-italic">Original Title: <a href="#">' + item.title + '</a></label>' +
        '<div class="col-12 mt-3">' +
        '<input type="submit" class="btn btn-link text-danger px-0 btn-remove-menu-item" data-id=' + item.order + ' value="Remove">' +
        '</div>' +
        '</form>' +
        '</div>' +
        '</div>' +
        '</li>';
    $("#sortable").append(html);
    fetch_sort_results();
}

$('#sortable').sortable().bind('sortupdate', function (e) {
    fetch_sort_results();
});

function fetch_sort_results() {
    var menuItemsOrder = $('#sortable').sortable('toArray');
    $("#add-menu-items-order").val(menuItemsOrder);
}

$("#add-custom_title").on('keyup', function (e) {
    $("#add-custom-display-title").val($("#add-custom_title").val());
});

$("#select-menu").on('change', function (e) {
    selectedMenu = $("#select-menu").val();
    load_menu();
});

function load_menu() {
    $("#add-menu-id").val(selectedMenu);
    $.ajax({
        type: "GET",
        url: get_menu_items,
        data: { id: selectedMenu },
        success: function (result) {
            var result = JSON.parse(result);
            console.log(result.data);
            if (result.status == "000") {
                menu_array = result.data;
                count = menu_array.length;
                $("#sortable").html("");
                $.each(menu_array, function (index, row) {
                    $("#add-menu-items").val(JSON.stringify(menu_array));
                    prepare_html(row);
                });
            }
        },
        error: function (xhr, status, errorThrown) {
            $("#edit-loader").fadeOut(500);
            toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
        }
    });
}

$("#save-menu-items").on('click', function (e) {
    e.preventDefault();
    if ($("#add-menu-id").val() && $("#add-menu-items").val()) {
        $.ajax({
            type: "POST",
            url: add_menu_items_url,
            data: $("#add-menu-items-form").serialize(),
            success: function (result) {
                if (result.status == "000") {
                    window.location.reload();
                } else {
                    toastr["error"](result.message, "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
                }
            },
            error: function (xhr, status, errorThrown) {
                $("#edit-loader").fadeOut(500);
                toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
            }
        });
    } else {
        toastr["error"]("No changes have been made.", "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
    }
});

$(document).on("keyup", ".update-display-title", function (e) {
    e.preventDefault();
    var new_value = $(this).val();
    var id = $(this).data('id');
    $("#item-title-" + $(this).data('id')).html(new_value);
    $.each(menu_array, function (index, row) {
        console.log(row.order + " | " + id);
        if (row.order == id) {
            console.log(row.display_title);
            row.display_title = new_value;
        }
    });
    $("#add-menu-items").val(JSON.stringify(menu_array));
});

$(document).on("click", ".btn-remove-menu-item", function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    console.log('trash ' + id);
    var new_menu_array = [];
    $.each(menu_array, function (index, item) {
        if (item.order != id) {
            new_menu_array.push(item);
        }
    });

    menu_array = new_menu_array;
    count = menu_array.length;
    $("#add-menu-items").val(JSON.stringify(menu_array));
    $("#sortable").html("");
    $.each(menu_array, function (index, row) {
        $("#add-menu-items").val(JSON.stringify(menu_array));
        prepare_html(row);
    });
});

$(document).on("click", ".collapse-toggle", function () { $("#more-less-" + $(this).data('order')).toggleClass("fa-chevron-up fa-chevron-down") });