@extends('backend.layouts.backend')

@section('styles')

    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/summernote/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/dselect.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/bootstrap-tagsinput.css') }}">
        
@endsection

@section( 'main_body')

    <!-- Main body -->
    <div id="main-body">
        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Widgets</li>
          </ol>
        </nav>

        <div class="row">
            <div class="col-12 col-md-5">
            <div class="row g-5">
                <div class="col-12">
                    <h3 class="fw-black">{{ ($widget) ? 'Update' : 'Create' }} Widget</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                </div>
            </div>

            <form class="ajax-form" id="ajax-form-autosave" method="POST">
            @csrf
                <input type="hidden" id="edit-id" name="id" value="{{ $widget->id ?? '' }}">
                <input type="hidden" id="edit-task" name="task" value="autosave">
                <div class="row g-5">
                    <div class="">
                        <div class="vstack gap-4">
                            <div>
                                <label class="form-label" for="blogTitle">Widget Title</label>
                                <input type="text" class="form-control slugify title" data-task="edit" data-type="title" id="edit-title" name="title" placeholder="Widget title" value="{{ $widget->title ?? '' }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="blogTitle">Slug</label>
                                <input type="text" class="form-control slug" data-task="edit" data-type="title" id="edit-slug" name="slug" placeholder="Post title" value="{{ $widget->slug ?? '' }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="edit-body">Body</label>
                                <textarea class="form-control summernote word-countable" data-counter_result='edit-body-counter' id="edit-body" name="body">{!! $widget->body ?? '' !!}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                    <input type="reset" class="btn btn-light" value="Reset">
                                    <span id="edit-loader" class="form-text ms-2 submit-add hidden">
                                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-12 col-md-7">
            <h3 class="fw-black">All Widgets</h3>
            <div class="vstack gap-2">
                <div class="table-responsive">
                    <table id="dt-server-side" class="table table-bordered table-striped dt-custom-table w-100">
                        <!-- Filter columns -->
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <!-- /Filter columns -->

                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
    <!-- /Main body -->

@endsection

@section('scripts')
  <script>
    datatable_url = "{{ route('datatable.get_widgets') }}";
    let start_autosave = false;
    let submit_edit_url = '{{ route("update_widget") }}';
    var file_upload_url = '{{ route("upload_file") }}';
    var intext_image_upload_url = '{{ route("upload_intext_file") }}';
    var post_task = "edit";
    var can_enable_delete = false;

    startAutosaveTimer();
    function startAutosaveTimer() {
        setInterval(function () {
            if (start_autosave) autoSave();
        }, 5000);
    }

    function autoSave(){
        if ($("#edit-title").val() != "") {
        $("#edit-loader").show();
        $.ajax({
            type: "POST",
            url: submit_edit_url,
            data: $("#ajax-form-autosave").serialize(),
            success: function (result) {
                console.log(result);
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
                }
            },
            error: function (xhr, status, errorThrown) {
                $("#edit-loader").fadeOut(500);
                toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
            }
        });
    }
    }
    function calculate_word_count() {}
  </script>
    
  <script src="{{ asset('theme/backend/vendor/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('theme/backend/vendor/slugify/slugify.js') }}"></script>
  <script src="{{ asset('theme/backend/vendor/summernote/summernote-lite.js?v=1.0.5') }}"></script>
  <script src="{{ asset('theme/backend/vendor/summernote/summernote-image-captionit.js') }}"></script>
  <script src="{{ asset('theme/backend/js/summernote.js?v1.0.5') }}"></script>
@endsection