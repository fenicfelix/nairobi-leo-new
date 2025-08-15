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

        <form class="ajax-form" id="ajax-form-edit" method="POST">
          @csrf
          @method('PUT')
            <input type="hidden" id="edit-seo_status" name="seo_status" value="{{ $show->seo_status }}">
            <input type="hidden" class="form-control" id="upload-banner_img" name="banner_img" placeholder="Banner image" value="{{ $show->banner->id ?? NULL }}">
            <input type="hidden" class="form-control" id="upload-mobile_img" name="mobile_img" placeholder="Mobile image" value="{{ $show->mobile_banner->id ?? NULL }}">
            <input type="hidden" class="form-control slug" id="edit-slug" name="slug" placeholder="Shiw slug" value="{{ $show->slug }}" required>
          <div class="row g-5">
            <div class="col-lg-8">
              <div class="">
                <div class="vstack gap-4">
                  <div>
                    <label class="form-label" for="blogTitle">Title</label>
                    <input type="text" class="form-control slugify title" data-task="add" data-type="title" id="edit-title" name="title" placeholder="Post title" value="{{ $show->title }}" required>
                    <p class="pt-2 text-muted"><i class="page-permalink">{{ get_option('ak_app_url') }}/<span id="edit-hint-slug">{{ $show->slug }}</span><small> <a href="#">update</a> </small></i></p>
                  </div>

                  <div>
                    <label class="form-label" for="edit-synopsis">Synopsis</label>
                    <textarea class="form-control in-summary" id="edit-synopsis" name="synopsis" rows="5" maxlength="255">{{ $show->synopsis }}</textarea>
                  </div>

                  <div>
                    <label class="form-label" for="edit-description">Long Description</label>
                    <textarea class="form-control long-description" id="edit-description" name="description">{{ $show->description }}</textarea>
                  </div>

                  <fieldset class="form-fieldset">
                    <legend>SEO Details <span id="edit-seo-status" class="seo-status"><i class="fas fa-circle text-danger"></i></span></legend>
                    <div class="card seo-preview" id="edit-seo-preview">
                        <div class="card-body">
                            <p><i> <span id="hint-seo-logo"><img src="{{ config('cms.app_icon') }}" alt=""></span>{{ get_option('ak_app_url') }}/<span id="edit-hint-seo-permalink"></span></i></p>
                            <p class="hint-seo-title text-primary"><span id="edit-hint-seo-title"></span><span id="edit-hint-seo-domain"> - {{ get_option('ak_app_title') }}</span></p>
                            <p class="hint-seo-description" id="edit-hint-seo-description">Please provide an SEO description by editing the snippet below. If you don’t, search engines will take part of your post body to show in the search results.</p>
                        </div>
                    </div>
                    <div class=" mt-3">
                        <label for="edit-seo_keywords" class="form-label">Keywords <small>(Comma separated)</small></label>
                        <input type="text" class="form-control seo seo-keywords" data-task="add" data-type="keywords" id="edit-seo_keywords" name="seo_keywords" maxlength="255" value="{{ $show->seo_keywords }}" placeholder="Focus keywords">
                        <div class="progress" id="edit-seo_keywords_progress" style="height: 0.1rem;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="form-floating mt-3">
                        <input type="text" class="form-control seo" data-task="add" data-type="title" id="edit-seo_title" name="seo_title" value="{{ $show->seo_title }}" placeholder="SEO title">
                        <label for="edit-seo_title" class="form-label">Title</label>
                        <div class="progress" id="edit-seo_title_progress" style="height: 0.1rem;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="form-floating mt-3">
                        <textarea class="form-control h-120 seo" data-task="add" data-type="description" id="edit-seo_description" name="seo_description" maxlength="200" rows="5" placeholder="SEO description">{{ $show->seo_description }}</textarea>
                        <label for="edit-seo_description" class="form-label">Description</label>
                        <div class="progress" id="edit-seo_description_progress" style="height: 0.1rem;">
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
                    <input type="text" class="form-control" data-task="add" id="edit-hosts" name="hosts" placeholder="Show hosts" value="{{ $show->hosts }}" required>
                  </div>
                  {{-- <div>
                    <label class="form-label" for="edit-hosts">Authors</label>
                    <select class="form-select dselect show-hosts" id="edit-hosts" name="hosts[]" multiple required>
                      <option value="">Hosts</option>
                        @forelse ($hosts as $host)
                            @php
                                $selected = "";
                            @endphp
                            @foreach ($show->hosts as $show_host)
                                @if ($show_host->id == $host->id)
                                    @php
                                        $selected = "selected";
                                        break;
                                    @endphp
                                @endif
                            @endforeach
                            <option value="{{ $host->id }}" {{ $selected }}>{{ $host->display_name }}</option>
                        @empty
                            
                        @endforelse
                    </select>
                    <div class="invalid-feedback">
                      Please provide at least one show host.
                    </div>
                  </div> --}}
                  <div>
                    <label class="form-label" for="blogTitle">Banner Image</label>
                    @php
                      if($show->banner) $banner = Storage::disk('public')->url($show->banner->file_name);
                      else $banner = fetch_image(NULL, "md");
                    @endphp
                    <div class="post-thumbnail w-100">
                      <img id="img-banner_img" class="selected-image post-thumbnail w-100 img-fluid" src="{{ $banner }}" alt="Responsive image">
                    </div>
                    <br>
                    <a href="#" data-dest="banner_img" class="btn btn-primary btn-upload-img" data-bs-toggle="modal" data-bs-target="#modalSetFeaturedImage">Select Image</a>
                  </div>
                  <div>
                    <label class="form-label" for="blogTitle">Mobile Image</label>
                    @php
                      if($show->mobile_banner) $mobile_banner = Storage::disk('public')->url($show->mobile_banner->file_name);
                      else $mobile_banner = fetch_image(NULL, "md");
                    @endphp
                    <div class="post-thumbnail w-100"><img id="img-mobile_img" class="selected-image post-thumbnail w-100 img-fluid" src="{{ $mobile_banner }}" alt="Responsive image"></div>
                    <br>
                    <a href="#" data-dest="mobile_img" class="btn btn-primary btn-upload-img" data-bs-toggle="modal" data-bs-target="#modalSetFeaturedImage">Select Image</a>
                  </div>
                  <div class="form-check mt-2">
                    <input type="checkbox" id="edit-active" name="active" class="form-check-input" {{ ($show->active == 1) ? 'checked' : '' }}> 
                    <label class="form-check-label" for="edit-active">Active?</label>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-12">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-light btn-edit-reset" value="Cancel">
                        <span id="edit-loader" class="form-text ms-2 submit-add hidden">
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
      let submit_edit_url = '{{ route("tv.shows.update", $show->id) }}';
      var file_upload_url = '{{ route("upload_file") }}';
      var intext_image_upload_url = '{{ route("upload_intext_file") }}';
      var image_tags_update_url = '{{ route("update_image_tags") }}';
      var fetch_thumbnails_url = "{{ route('media.fetch_images', '1') }}";
      var post_task = "edit";
    </script>
    <script src="{{ asset('theme/backend/vendor/slugify/slugify.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/dselect/dselect.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-lite.js?v=1.0.3') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-image-captionit.js') }}"></script>
    <script src="{{ asset('theme/backend/js/summernote.js?v=1.0.1') }}"></script>
    <script src="{{ asset('theme/backend/js/media.js') }}"></script>

    <script>
      // dselect(document.querySelector('.show-hosts'), {
      //   search: true,
      //   creatable: true,
      // });
    </script>
@endsection