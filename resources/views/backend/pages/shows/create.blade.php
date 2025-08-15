@extends('backend.layouts.backend')

@section('styles')
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/summernote/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/dselect.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/bootstrap-tagsinput.css') }}">
        
@endsection

@section( 'main_body')

    <!-- Main body -->
    <div id="main-body">
        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
             <li class="breadcrumb-item"><a href="{{ route('posts.index', 'all') }}">Shows</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
          </ol>
        </nav>

        <div class="row g-5">
          <div class="col-12">
            <h3 class="fw-black">New Show</h3>
            <p>Fill in the form below and submit. All fields are important.</p>
          </div>
        </div>

        <form class="ajax-form" id="ajax-form-add" method="POST">
          @csrf
          
            <input type="hidden" id="add-seo_status" name="seo_status" value="0">
            <input type="hidden" class="form-control" id="upload-banner_img" name="banner_img" placeholder="Banner image">
            <input type="hidden" class="form-control" id="upload-mobile_img" name="mobile_img" placeholder="Mobile image">
            <input type="hidden" class="form-control slug" id="add-slug" name="slug" placeholder="Post slug" required>
          <div class="row g-5">
            <div class="col-lg-8">
              <div class="">
                <div class="vstack gap-4">
                  <div>
                    <label class="form-label" for="blogTitle">Title</label>
                    <input type="text" class="form-control slugify title" data-task="add" data-type="title" id="add-title" name="title" placeholder="Post title" required>
                    <p class="pt-2 text-muted"><i class="page-permalink">{{ get_option('ak_app_url') }}/<span id="add-hint-slug"></span></i></p>
                  </div>

                  <div>
                    <label class="form-label" for="add-synopsis">Synopsis</label>
                    <textarea class="form-control in-summary" id="add-synopsis" name="synopsis" rows="5" maxlength="255"></textarea>
                  </div>

                  <div>
                    <label class="form-label" for="add-description">Long Description</label>
                    <textarea class="form-control long-description" id="add-description" name="description"></textarea>
                  </div>

                  <fieldset class="form-fieldset">
                    <legend>SEO Details <span id="add-seo-status" class="seo-status"><i class="fas fa-circle text-danger"></i></span></legend>
                    <div class="card seo-preview" id="add-seo-preview">
                        <div class="card-body">
                            <p><i> <span id="hint-seo-logo"><img src="{{ config('cms.app_icon') }}" alt=""></span>{{ get_option('ak_app_url') }}/<span id="add-hint-seo-permalink"></span></i></p>
                            <p class="hint-seo-title text-primary"><span id="add-hint-seo-title"></span><span id="add-hint-seo-domain"> - {{ get_option('ak_app_title') }}</span></p>
                            <p class="hint-seo-description" id="add-hint-seo-description">Please provide an SEO description by editing the snippet below. If you don’t, search engines will take part of your post body to show in the search results.</p>
                        </div>
                    </div>
                    <div class=" mt-3">
                        <label for="add-seo_keywords" class="form-label">Keywords <small>(Comma separated)</small></label>
                        <input type="text" class="form-control seo seo-keywords" data-task="add" data-type="keywords" id="add-seo_keywords" name="seo_keywords" maxlength="255" placeholder="Focus keywords">
                        <div class="progress" id="add-seo_keywords_progress" style="height: 0.1rem;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="form-floating mt-3">
                        <input type="text" class="form-control seo" data-task="add" data-type="title" id="add-seo_title" name="seo_title" placeholder="SEO title">
                        <label for="add-seo_title" class="form-label">Title</label>
                        <div class="progress" id="add-seo_title_progress" style="height: 0.1rem;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="form-floating mt-3">
                        <textarea class="form-control h-120 seo" data-task="add" data-type="description" id="add-seo_description" name="seo_description" maxlength="200" rows="5" placeholder="SEO description"></textarea>
                        <label for="add-seo_description" class="form-label">Description</label>
                        <div class="progress" id="add-seo_description_progress" style="height: 0.1rem;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </fieldset>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body vstack gap-4">
                  <div>
                    <label class="form-label" for="blogTitle">Show hosts</label>
                    <input type="text" class="form-control" data-task="add" id="add-hosts" name="hosts" placeholder="Show hosts" required>
                  </div>
                  {{-- <div>
                    <label class="form-label" for="add-hosts">Hosts</label>
                    <select class="form-select dselect show-hosts" id="add-hosts" name="hosts[]" multiple required>
                      <option value="">Hosts</option>
                      @forelse ($hosts as $host)
                            <option value="{{ $host->id }}">{{ $host->display_name }}</option>
                        @empty
                            
                        @endforelse
                    </select>
                    <div class="invalid-feedback">
                      Please provide at least one host.
                    </div>
                  </div> --}}
                  <div>
                    <label class="form-label" for="blogTitle">Banner Image</label>
                    <div class="post-thumbnail w-100"><img id="img-banner_img" class="selected-image post-thumbnail w-100 img-fluid" src="{{ $image = fetch_image(NULL, "md") }}" alt="Responsive image"></div>
                    <br>
                    <a href="#" data-dest="banner_img" class="btn btn-primary btn-upload-img" data-bs-toggle="modal" data-bs-target="#modalSetFeaturedImage">Select Image</a>
                  </div>
                  <div>
                    <label class="form-label" for="blogTitle">Mobile Image</label>
                    <div class="post-thumbnail w-100"><img id="img-mobile_img" class="selected-image post-thumbnail w-100 img-fluid" src="{{ $image = fetch_image(NULL, "md") }}" alt="Responsive image"></div>
                    <br>
                    <a href="#" data-dest="mobile_img" class="btn btn-primary btn-upload-img" data-bs-toggle="modal" data-bs-target="#modalSetFeaturedImage">Select Image</a>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <span id="add-loader" class="form-text ms-2 submit-edit hidden">
                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                      </span>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-12">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-light btn-edit-reset" value="Cancel">
                        <span id="add-loader" class="form-text ms-2 submit-add hidden">
                            <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </div>
                </div>
              </div>
            </div>
          </div>
        </form>
    </div>
    <!-- /Main body -->

    @include('backend.pages.media.media_modal')

@endsection
{{-- upload_intext_file --}}
@section('scripts')
  <script>
    let submit_add_url = '{{ route("tv.shows.store") }}';
    var file_upload_url = '{{ route("upload_file") }}';
    var intext_image_upload_url = '{{ route("upload_intext_file") }}';
    var image_tags_update_url = '{{ route("update_image_tags") }}';
    var fetch_thumbnails_url = "{{ route('media.fetch_images', '1') }}";
    var post_task = "add";
  </script>
    <script src="{{ asset('theme/backend/vendor/slugify/slugify.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/dselect/dselect.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-lite.js?v=1.0.3') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-image-captionit.js') }}"></script>
    <script src="{{ asset('theme/backend/js/summernote.js?v=1.0.1') }}"></script>
    <script src="{{ asset('theme/backend/js/media.js?v=1.0.2') }}"></script>

    <script>
      // dselect(document.querySelector('.show-hosts'), {
      //   search: true,
      //   creatable: true,
      // });
    </script>
@endsection