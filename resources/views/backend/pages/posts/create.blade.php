@extends('backend.layouts.backend')

@section('styles')
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
             <li class="breadcrumb-item"><a href="{{ route('posts.index', 'all') }}">Posts</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
          </ol>
        </nav>

        <div class="row g-5">
          <div class="col-12">
            <h3 class="fw-black">New Post</h3>
            <p>Fill in the form below and submit. All fields are important.</p>
          </div>
        </div>

        <form class="ajax-form" id="ajax-form-add" method="POST">
          @csrf
          
            <input type="hidden" id="add-seo_status" name="seo_status" value="0">
            <input type="hidden" id="add-task" name="task" value="publish">
            <input type="hidden" class="form-control" id="upload-featured_image" name="featured_image" placeholder="Featured image">
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
                    <label class="form-label" for="add-body">Body</label>
                    <textarea class="form-control summernote" id="add-body" name="body"></textarea>
                  </div>

                  <div>
                    <label class="form-label" for="add-in_summary">In summary</label>
                    <textarea class="form-control in-summary" id="add-in_summary" name="in_summary"></textarea>
                  </div>

                  <div>
                    <label class="form-label" for="add-excerpt">Excerpt</label>
                    <textarea class="form-control" id="add-excerpt" name="excerpt" rows="5" maxlength="255"></textarea>
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
                    <label class="form-label">Published at</label>
                    <div class="form-check">
                      <input type="radio" id="add-publish_immediately" name="publish_type" class="form-check-input" value="immediate" data-bs-toggle="collapse" data-bs-target=".inline-datepicker" checked>
                      <label class="form-check-label" for="add-publish_immediately">Immediately</label>
                    </div>
                    <div class="form-check">
                      <input type="radio" id="add-schedule" name="publish_type" class="form-check-input" value="scheduled" data-bs-toggle="collapse" data-bs-target=".inline-datepicker">
                      <label class="form-check-label" for="add-schedule">Set date and time</label>
                    </div>
                    <div class="collapse inline-datepicker">
                      <input type="text" class="form-control datetimepicker-inline" name="schedule_time" placeholder="Choose date">
                    </div>
                  </div>
                  <div>
                      <label for="add-category_id" class="form-label" >Post Category</label>
                      <select class="form-control" id="add-category_id" name="category_id">
                          <option value="">--- Select ---</option>
                          @forelse ($categories as $category)
                              <option value="{{ $category->id }}">{!! $category->name !!}</option>
                          @empty
                              
                          @endforelse
                      </select>
                  </div>
                  <div>
                    <label class="form-label" for="post-tags">Tags</label>
                    <input type="text" class="form-control" id="add-tags" name="tags" data-role="tagsinput" placeholder="Tags" />
                  </div>

                  <div>
                    <label class="form-label" for="add-authors">Authors</label>
                    <select class="form-select dselect post-authors" id="add-authors" name="authors[]" multiple required>
                      <option value="">Authors</option>
                      @forelse ($authors as $author)
                          @php
                            $selected = "";
                            if($author->id == Auth::id()) $selected = "selected";
                          @endphp 
                            <option value="{{ $author->id }}" {{ $selected }}>{{ $author->display_name }}</option>
                        @empty
                            
                        @endforelse
                    </select>
                    <div class="invalid-feedback">
                      Please provide at least one author.
                    </div>
                  </div>
                  <div>
                    <label class="form-label" for="blogTitle">Featured Image</label>
                    <div class="post-thumbnail w-100"><img id="img-featured_image" class="selected-image post-thumbnail w-100 img-fluid" src="{{ $image = fetch_image(NULL, "md") }}" alt="Responsive image"></div>
                    </br>
                    <a href="#" data-dest="featured_image" class="btn btn-primary btn-upload-img" data-bs-toggle="modal" data-bs-target="#modalSetFeaturedImage">Select Image</a>
                  </div>
                  <div>
                    <label class="form-label" for="add-post_label">Post Label</label>
                    <input type="text" class="form-control" id="add-post_label" name="post_label" autocomplete="off" placeholder="Add a label on the story">
                  </div>
                  <div>
                    <label class="form-label">Post Options</label>
                    <div class="form-check mt-2">
                      <input type="checkbox" id="add-is_breaking" name="is_breaking" class="form-check-input">
                      <label class="form-check-label" for="add-is_breaking">Breaking News</label>
                    </div>
                    <div class="form-check mt-2">
                      <input type="checkbox" id="add-is_featured" name="is_featured" class="form-check-input">
                      <label class="form-check-label" for="add-is_featured">Featured Story</label>
                    </div>
                    <div class="form-check mt-2">
                      <input type="checkbox" id="add-is_sponsored" name="is_sponsored" class="form-check-input">
                      <label class="form-check-label" for="add-is_sponsored">Sponsored Post</label>
                    </div>
                    <div class="form-check mt-2">
                      <input type="checkbox" id="add-send_notification" name="send_notification" class="form-check-input">
                      <label class="form-check-label" for="add-send_notification">Send push notification</label>
                    </div>
                    <div class="form-check mt-2">
                      <input type="checkbox" id="add-display_ads" name="display_ads" class="form-check-input" checked>
                      <label class="form-check-label" for="add-display_ads">Display Ads</label>
                    </div>
                  </div>
                  <div>
                        <label for="add-homepage_ordering" class="form-label">Homepage Ordering</label>
                    <select class="form-control" name="homepage_ordering" id="add-homepage_ordering">
                        <option value="0">--- Select ---</option>
                        @for ($i = 0; $i < get_option('ak_sticky_posts'); $i++)
                          @php
                              $j = $i;
                              $j++;
                          @endphp
                            <option value="{{ $j }}">Story {{ $j }}</option>
                        @endfor
                    </select>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <span id="add-loader" class="form-text ms-2 submit-edit hidden">
                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                      </span>
                    </div>
                  </div>
                  <div>
                    <div class="btn-group btn-group w-100 mb-1">
                      <button class="btn d-flex btn-light justify-content-center gap-1 btn-post-add" data-type="save" type="button">
                        <i class="fas fa-save mt-1"></i> Save
                      </button>
                      <button class="btn d-flex btn-light justify-content-center gap-1 btn-post-add" data-type="preview" type="button">
                        <i class="fas fa-eye mt-1 text-default"></i> Preview
                      </button>
                    </div>
                    <button class="btn d-flex justify-content-center align-items-center gap-1 w-100 btn-primary mt-2" type="submit">
                      Publish <i class="fas fa-arrow-right"></i>
                    </button>
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
      let submit_add_url = '{{ route("posts.store") }}';
      var file_upload_url = '{{ route("upload_file") }}';
      var intext_image_upload_url = '{{ route("upload_intext_file") }}';
      var image_tags_update_url = '{{ route("update_image_tags") }}';
      var tags = {!! json_encode($tags) !!};
      var fetch_thumbnails_url = "{{ route('media.fetch_images', '1') }}";
      var post_task = "add";
      var can_enable_delete = false;
    </script>
    <script src="{{ asset('theme/backend/js/posts.js?v=1.0.2') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-lite.js?v=1.0.3') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-image-captionit.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/dselect/dselect.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/slugify/slugify.js') }}"></script>
    <script src="{{ asset('theme/backend/js/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('theme/backend/js/typeahead.js') }}"></script>
    <script src="{{ asset('theme/backend/js/summernote.js') }}"></script>
    <script src="{{ asset('theme/backend/js/media.js?v=1.0.3') }}"></script>
@endsection