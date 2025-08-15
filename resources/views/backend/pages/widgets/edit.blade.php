@extends('backend.layouts.backend')

@section('styles')

    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/summernote/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/summernote-lite.css') }}">
    
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


        <div class="row g-5">
          <div class="col-12 col-md-5">
            <div id="add-div">
                <form class="ajax-form" id="ajax-form-edit" method="POST">
                    @csrf
                    <input type="text" data-task="edit" data-type="title" id="edit-id" name="id" value="<?=$widget->identifier?>" required>
                    <h3 class="fw-black">Update Widget</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                    <div class="vstack gap-3">
                        <div class="form-floating">
                            <input type="text" class="form-control slugify title" data-task="add" data-type="title" id="add-name" name="title" placeholder="Widget Title" required>
                            <label for="add-name" class="form-label">Widget Title</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control slug" data-task="add" id="add-slug" name="slug" placeholder="Category slug" required>
                            <label for="add-slug" class="form-label">Slug</label>
                        </div>
                        <div>
                            <label class="form-label" for="add-body">Body</label>
                            <textarea class="form-control summernote " id="add-body" name="body"></textarea>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="add-loader" class="form-text ms-2 submit-add hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
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

    <script src="{{ asset('theme/backend/js/posts.js?v=1.0.3') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-lite.js?v=1.0.3') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-image-captionit.js') }}"></script>
    <script src="{{ asset('theme/backend/js/summernote.js?v1.0.4') }}"></script>

    <script>
        let submit_add_url = '{{ route("widgets.store") }}';
        datatable_url = "{{ route('datatable.get_widgets') }}";
        tableDefaultFilter = [0, "DESC"];
    </script>
@endsection