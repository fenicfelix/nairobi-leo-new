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
             <li class="breadcrumb-item"><a href="{{ route('posts.index', 'all') }}">Pages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit page</li>
          </ol>
        </nav>

        <div class="row g-5">
          <div class="col-12">
            <h3 class="fw-black">Update Page</h3>
            <p>Fill in the form below and submit. All fields are important.</p>
          </div>
        </div>

        <form class="ajax-form" id="ajax-form-edit" method="POST">
          @csrf
          @method("put")
            <input type="hidden" id="edit-seo_status" name="seo_status" value="{{ $page->seo_status }}">
            <input type="hidden" id="edit-id" name="id" value="{{ $page->id }}">
          <div class="row g-5">
            <div class="col-lg-8">
              <div class="">
                <div class="vstack gap-4">
                  <div class="form-floating">
                        <input type="text" class="form-control slugify" data-task="edit" data-type="title" id="edit-name" name="title" placeholder="Title" value="{{ $page->title }}" required>
                        <label for="edit-name" class="form-label">Title</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control slug" data-task="edit" id="edit-slug" name="slug" placeholder="Slug" value="{{ $page->slug }}" required>
                        <label for="edit-slug" class="form-label">Slug</label>
                    </div>
                    <div>
                        <label class="form-label" for="edit-body">Body</label>
                        <textarea class="form-control summernote" id="edit-body" name="body">{{ $page->body }}</textarea>
                    </div>
                    <fieldset class="form-fieldset">
                        <legend>SEO Details <span id="edit-seo-status" class="seo-status"><i class="fas fa-circle text-danger"></i></span></legend>
                        <div class="row">
                          <div class="col-12 col-md-6">
                            <div class="card seo-preview" id="edit-seo-preview">
                              <div class="card-body">
                                  <p><i> <span id="hint-seo-logo"><img src="{{ config('cms.app_icon') }}" alt=""></span>{{ get_option('ak_app_url') }}/<span id="edit-hint-seo-permalink">{{ $page->slug }}</span></i></p>
                                  <p class="hint-seo-title text-primary"><span id="edit-hint-seo-title">{{ $page->seo_title }}</span><span id="edit-hint-seo-domain"> - {{ get_option('ak_app_title') }}</span></p>
                                  <p class="hint-seo-description" id="edit-hint-seo-description">{{ $page->seo_description }}</p>
                                  <h6>SEO Hints</h6>
                                  <ol>
                                      <li>Focus keyword field is a must starting with the most relevant keyword/key phrase.</li>
                                      <li>The first focus key phrase should appear on the SEO title, SEO description and the body</li>
                                      <li>The SEO title should be between 55 and 60 characters</li>
                                      <li>The SEO description should be between 155 and 160 characters</li>
                                  </ol>
                              </div>
                          </div>
                          </div>
                        </div>
                        <div class="mt-3">
                            <label for="edit-seo_keywords" class="form-label">Keywords <small>(Comma separated)</small></label>
                            <input type="text" class="form-control seo seo-keywords" data-task="edit" data-type="keywords" id="edit-seo_keywords" name="seo_keywords" maxlength="255" placeholder="Focus keyword" value="{{ $page->seo_keywords }}">
                            <div class="progress" id="edit-seo_keywords_progress" style="height: 0.1rem;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="form-floating mt-3">
                            <input type="text" class="form-control seo" data-task="edit" data-type="title" id="edit-seo_title" name="seo_title" placeholder="SEO title" value="{{ $page->seo_title }}">
                            <label for="edit-seo_title" class="form-label">Title</label>
                            <div class="progress" id="edit-seo_title_progress" style="height: 0.1rem;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="fst-italic"><span id="edit-txt-seo-title">0</span> characters</small>
                        </div>
                        <div class="form-floating mt-3">
                            <textarea class="form-control h-120 seo" data-task="edit" data-type="description" id="edit-seo_description" name="seo_description" maxlength="200" rows="5" placeholder="SEO description">{{ $page->seo_description }}</textarea>
                            <label for="edit-seo_description" class="form-label">Description</label>
                            <div class="progress" id="edit-seo_description_progress" style="height: 0.1rem;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="fst-italic"><span id="edit-txt-seo-description">0</span> characters</small>
                        </div>
                    </fieldset>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body vstack gap-4">
                  <div>
                    <label class="form-label">Page Settings</label>
                  </div>
                  <div>
                    <label for="edit-category_id" class="form-label">Associated Category</label>
                    <select class="form-control" name="category_id" id="edit-category_id">
                        <option value="">--- Select ---</option>
                        @foreach ($categories as $category)
                            @php
                              $selected = ($category->id == $page->category_id) ? "selected" : "";  
                            @endphp
                            <option value="{{ $category->id }}" {{ $selected }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div>
                    <label for="edit-template" class="form-label">Page Template</label>
                    <select class="form-control" name="template" id="edit-template" required>
                        <option value="">--- Select ---</option>
                        <option value="homepage" {{ ($page->template == "homepage") ? "selected" : "" }}>Homepage</option>
                        <option value="pages" {{ ($page->template == "pages") ? "selected" : "" }}>Pages</option>
                    </select>
                  </div>
                  <div class="row">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Update">
                            <input type="reset" class="btn btn-light btn-edit-reset" value="Cancel">
                            <span id="edit-loader" class="form-text ms-2 submit-edit hidden">
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

@endsection
{{-- upload_intext_file --}}
@section('scripts')
    <script src="{{ asset('theme/backend/vendor/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-lite.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-image-captionit.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/dselect/dselect.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/slugify/slugify.js') }}"></script>
    <script src="{{ asset('theme/backend/js/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('theme/backend/js/typeahead.js') }}"></script>

    <script>
      let submit_edit_url = '{{ route("pages.update", $page->id) }}';
      var file_upload_url = '{{ route("upload_file") }}';
      var intext_image_upload_url = '{{ route("upload_intext_file") }}';
      var post_task = "edit";
    </script>
    <script src="{{ asset('theme/backend/js/pages.js') }}"></script>
@endsection