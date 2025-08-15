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
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
          </ol>
        </nav>

        <div class="row g-5">
          <div class="col-12">
            <h3 class="fw-black">New Page</h3>
            <p>Fill in the form below and submit. All fields are important.</p>
          </div>
        </div>

        <form class="ajax-form" id="ajax-form-add" method="POST">
          @csrf
            <input type="hidden" id="add-seo_status" name="seo_status" value="0">
          <div class="row g-5">
            <div class="col-lg-8">
              <div class="">
                <div class="vstack gap-4">
                  <div class="form-floating">
                        <input type="text" class="form-control slugify" data-task="add" data-type="title" id="add-name" name="title" placeholder="Title" required>
                        <label for="add-name" class="form-label">Title</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control slug" data-task="add" id="add-slug" name="slug" placeholder="Slug" required>
                        <label for="add-slug" class="form-label">Slug</label>
                    </div>
                    <div>
                        <label class="form-label" for="add-body">Body</label>
                        <textarea class="form-control summernote" id="add-body" name="body"></textarea>
                    </div>
                    <fieldset class="form-fieldset">
                        <legend>SEO Details <span id="add-seo-status" class="seo-status"><i class="fas fa-circle text-danger"></i></span></legend>
                        <div class="col-12 col-md-6">
                          <div class="card seo-preview" id="add-seo-preview">
                            <div class="card-body">
                                <p><i> <span id="hint-seo-logo"><img src="{{ config('cms.app_icon') }}" alt=""></span>{{ get_option('ak_app_url') }}/<span id="add-hint-seo-permalink"></span></i></p>
                                <p class="hint-seo-title text-primary"><span id="add-hint-seo-title"></span><span id="add-hint-seo-domain"> - {{ get_option('ak_app_title') }}</span></p>
                                <p class="hint-seo-description" id="add-hint-seo-description">Please provide an SEO description by editing the snippet below. If you don’t, search engines will take part of your post body to show in the search results.</p>
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
                        <div class="mt-3">
                            <label for="add-seo_keywords" class="form-label">Keywords <small>(Comma separated)</small></label>
                            <input type="text" class="form-control seo seo-keywords" data-task="add" data-type="keywords" id="add-seo_keywords" name="seo_keywords" maxlength="255" placeholder="Focus keyword">
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
                            <small class="fst-italic"><span id="add-txt-seo-title">0</span> characters</small>
                        </div>
                        <div class="form-floating mt-3">
                            <textarea class="form-control h-120 seo" data-task="add" data-type="description" id="add-seo_description" name="seo_description" maxlength="200" rows="5" placeholder="SEO description"></textarea>
                            <label for="add-seo_description" class="form-label">Description</label>
                            <div class="progress" id="add-seo_description_progress" style="height: 0.1rem;">
                              <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="fst-italic"><span id="add-txt-seo-description">0</span> characters</small>
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
                    <label for="add-category_id" class="form-label">Associated Category</label>
                    <select class="form-control" name="category_id" id="add-category_id">
                        <option value="">--- Select ---</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div>
                    <label for="add-template" class="form-label">Page Template</label>
                    <select class="form-control" name="template" id="add-template" required>
                        <option value="">--- Select ---</option>
                        <option value="homepage">Homepage</option>
                        <option value="pages">Pages</option>
                    </select>
                  </div>
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
      let submit_add_url = '{{ route("pages.store") }}';
      var file_upload_url = '{{ route("upload_file") }}';
      var intext_image_upload_url = '{{ route("upload_intext_file") }}';
      var post_task = "add";
    </script>
    <script src="{{ asset('theme/backend/js/pages.js') }}"></script>
@endsection