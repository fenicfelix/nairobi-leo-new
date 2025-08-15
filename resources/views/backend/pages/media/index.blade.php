@extends('backend.layouts.backend')

@section('styles')

    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/photoswipe/photoswipe.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/photoswipe/default-skin/default-skin.css') }}">
    
@endsection

@section( 'main_body')

      <!-- Main body -->
      <div id="main-body">

        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Media</li>
          </ol>
        </nav>

        <input type="hidden" id="library_page" value="">
        <input type="hidden" id="library_page-prev" value="">
        <input type="hidden" id="library_page-next" value="">

        <div class="row">
            <div class="col-12 col-md-8">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-link active" id="nav-media_library-tab" data-bs-toggle="tab" href="#nav-media_library" role="tab" aria-controls="nav-media_library" aria-selected="false"><h4><b>Media Library</b></h4></a>
                        <a class="nav-link" id="nav-upload_a_file-tab" data-bs-toggle="tab" href="#nav-upload_a_file" role="tab" aria-controls="nav-upload_a_file" aria-selected="true"><h4><b>Upload a File</b></h4></a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-media_library" role="tabpanel" aria-labelledby="nav-media_library-tab">
                        <div class="row mt-4">
                            <div class="col-md-8 col-sm-12">
                                <p class="text-secondary font-size-sm">
                                    List of all uploaded media files from recently uploaded to oldest.
                                    <br>
                                    <span id="ajax-load-more" class="form-text ms-2 mt-2 hidden">
                                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <form action="" id="search-media-form">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2" id="search-input" name="s" value="">
                                        <button type="submit" class="input-group-text btn-primary" id="basic-addon2">Go</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr>
                        <div id="add-media-gallery" class="row row-cols-3 row-cols-sm-4 row-cols-xl-5 g-1 file-manager-grid photos-grid">
                        </div>
                        <hr>
                        <div id="section1" class="mt-4">
                            <nav aria-label="Page navigation example">
                            <ul class="pagination mb-0">
                                <li class="page-item" id="pagination-prev"><a class="page-link has-icon a-paginate" data-page="prev" href="#"><i class="fas fa-chevron-left"></i>&nbsp;&nbsp;Previous</a></li>
                                <li class="page-item" id="pagination-next"><a class="page-link has-icon a-paginate" data-page="next" href="#">Next&nbsp;&nbsp;<i class="fas fa-chevron-right"></i></a></li>
                            </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-upload_a_file" role="tabpanel" aria-labelledby="nav-upload_a_file-tab">
                        <section id="section6" class="mt-3">
                            <p class="text-secondary font-size-sm">
                                Select a file to set as a featured image. Max size 1MB
                            </p>
                            <form action="#" id="file-upload-form" enctype="multipart/form-data" >
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <input type="file" class="form-control" name="file" id="upload-file" required>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary" id="btn-upload">Upload</button>
                                    <span id="upload-loader" class="form-text ms-2 submit-add hidden">
                                            <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                        </span>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="row">
                                <div class="col-8">
                                    <div class="mt-2 w-100">
                                        <div class="image-area  mb-2"><img id="uploaded-image" class="selected-image post-thumbnail w-100" src="{{ $image = fetch_image(NULL, "lg") }}" class="img-fluid" alt="Responsive image"></div>
                                    </div>
                                </div>
                                <div class="col-4 mt-5 text-muted" id="upload-image-details">
                                    <span id="upload-results"></span>
                                    <a class="upload-trash hidden" href="#">Delete Permanently</a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 border-left-gray">
                <h3 class="fw-black">Image details</h3>
                @include('backend.pages.media.seo')
            </div>
        </div>
          
      </div>
      <!-- /Main body -->

      <div class="modal fade delete-modal" id="deleteImageConfirmationModal" tabindex="-1" aria-labelledby="deleteImageConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <form class="form" method="post" id="ajax-form-delete-img" data-form_type="modal">
                    @csrf
                    <input type="hidden" id="delete-img-id" name="identifier"required>
                    <div class="modal-body">
                        <p class="font-30">Are you sure you want to delete the selected image?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-success">Yes... Delete Now.</button>
                        <span id="delete-loader" class="form-text ms-2 submit-edit hidden">
                            <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var file_upload_url = '{{ route("upload_file") }}';
        var fetch_thumbnails_url = "{{ route('media.fetch_images') }}";
        var image_tags_update_url = '{{ route("update_image_tags") }}';
        var delete_image_url = '{{ route("delete_image") }}';
        $("#delete-image").removeClass("d-none");
    </script>
  <script src="{{ asset('theme/backend/js/media.js?v=1.0.3') }}"></script>

    
@endsection