@extends('backend.layouts.backend')

@section('styles')
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/summernote/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/dselect.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/bootstrap-tagsinput.css') }}">
@endsection

@section('main_body')

    <!-- Main body -->
    <div id="main-body">
        <nav aria-label="breadcrumb" id="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add new {{ $is_product ? 'product' : 'post' }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <div class="col-12">
                <h3 class="fw-black">{{ $post ? 'Update' : 'New' }} {{ $is_product ? 'Product' : 'Post' }}</h3>
                <p>Fill in the form below and submit. All fields are important.</p>
            </div>
        </div>

        <form class="ajax-form" id="ajax-form-autosave" method="POST">
            @csrf
            <input class="a-input" type="hidden" id="edit-id" name="id" value="{{ $post->id ?? '' }}">
            <input class="a-input" type="hidden" id="edit-task" name="task" value="autosave">
            <input class="a-input" type="hidden" id="edit-seo_status" name="seo_status"
                value="{{ $post->seo_status ?? '' }}">
            <input class="a-input" type="hidden" id="edit-is_product" name="is_product" value="{{ $is_product ?? '' }}">
            <input type="hidden" class="form-control a-input a-input" id="upload-featured_image" name="featured_image"
                value="{{ $post->featured_image ?? '' }}">
            <input type="hidden" class="form-control a-input slug" id="edit-slug" name="slug" placeholder="Post slug"
                value="{{ $post->slug ?? '' }}">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="">
                        <div class="vstack gap-4">
                            <div>
                                <label class="form-label" for="blogTitle">Title</label>
                                <input type="text" class="form-control a-input slugify title" data-task="edit"
                                    data-type="title" id="edit-title" name="title" placeholder="Post title"
                                    maxlength="230" value="{{ $post->title ?? '' }}" required>
                                <p class="pt-2 text-muted"><i class="page-permalink">{{ get_option('ak_app_url') }}/<span
                                            id="edit-hint-slug">{{ $post->slug ?? '' }}</span></i></p>
                            </div>
                            <div>
                                <label class="form-label" for="edit-body">Body</label>
                                <textarea class="form-control a-input summernote word-countable" data-counter_result='edit-body-counter' id="edit-body"
                                    name="body">{!! $post->body ?? '' !!}</textarea>
                                <div class="word-counter mt-2">
                                    <small class="fst-italic"><span id="edit-body-counter">0</span><span
                                            id="edit-body-counter-text"> words</span></small>
                                </div>
                            </div>

                            <div>
                                <label class="form-label" for="edit-in_summary">In summary</label>
                                <textarea class="form-control a-input in-summary" id="edit-in_summary" name="in_summary">{!! $post->in_summary ?? '' !!}</textarea>
                            </div>

                            <div>
                                <label class="form-label" for="edit-excerpt">Excerpt</label>
                                <textarea class="form-control a-input " id="edit-excerpt" name="excerpt" rows="5" maxlength="255">{{ $post->excerpt ?? '' }}</textarea>
                            </div>

                            <fieldset class="form-fieldset">
                                <legend>SEO Details <span id="edit-seo-status" class="seo-status"><i
                                            class="fas fa-circle text-danger"></i></span></legend>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="card seo-preview" id="edit-seo-preview">
                                            <div class="card-body">
                                                <p><i> <span id="hint-seo-logo"><img src="{{ config('cms.app_icon') }}"
                                                                alt=""></span>{{ get_option('ak_app_url') }}/<span
                                                            id="edit-hint-seo-permalink">{{ $post->slug ?? '' }}</span></i>
                                                </p>
                                                <p class="hint-seo-title text-primary"><span
                                                        id="edit-hint-seo-title">{{ $post->seo_title ?? '' }}</span><span
                                                        id="edit-hint-seo-domain"> -
                                                        {{ get_option('ak_app_title') }}</span></p>
                                                <p class="hint-seo-description" id="edit-hint-seo-description">
                                                    {{ $post->seo_description ?? '' }}</p>
                                                <hr>
                                                <h6>SEO Hints</h6>
                                                <ol>
                                                    <li>Focus keyword field is a must starting with the most relevant
                                                        keyword/key phrase.</li>
                                                    <li>The first focus key phrase should appear on the SEO title, SEO
                                                        description and the body</li>
                                                    <li>The SEO title should be between 55 and 60 characters</li>
                                                    <li>The SEO description should be between 155 and 160 characters</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="edit-seo_keywords" class="form-label">Keywords <small>(Comma
                                            separated)</small></label>
                                    <input type="text" class="form-control a-input seo seo-keywords" data-task="edit"
                                        data-type="keywords" id="edit-seo_keywords" name="seo_keywords" maxlength="255"
                                        value="{{ $post->seo_keywords ?? '' }}" placeholder="Focus keyword">
                                    <div class="progress" id="edit-seo_keywords_progress" style="height: 0.1rem;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control a-input seo" data-task="edit"
                                        data-type="title" id="edit-seo_title" name="seo_title" maxlength="255"
                                        value="{{ $post->seo_title ?? '' }}" placeholder="SEO title">
                                    <label for="edit-seo_title" class="form-label">Title</label>
                                    <div class="progress" id="edit-seo_title_progress" style="height: 0.1rem;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="fst-italic"><span id="edit-txt-seo-title">0</span> characters</small>
                                </div>
                                <div class="form-floating mt-3">
                                    <textarea class="form-control a-input h-120 seo" data-task="edit" data-type="description" id="edit-seo_description"
                                        name="seo_description" maxlength="200" rows="5" placeholder="SEO description">{{ $post->seo_description ?? '' }}</textarea>
                                    <label for="edit-seo_description" class="form-label">Description</label>
                                    <div class="progress" id="edit-seo_description_progress" style="height: 0.1rem;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="fst-italic"><span id="edit-txt-seo-description">0</span>
                                        characters</small>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body vstack gap-4">
                            @if (can_publish($logged_user))
                                <div>
                                    <label class="form-label">Published at</label>
                                    @php
                                        $checked = '';
                                        if (!$post || ($post && $post->status_id != 2)) {
                                            $checked = 'checked';
                                        }
                                    @endphp
                                    <div class="form-check">
                                        <input type="radio" id="edit-publish_immediately" name="publish_type"
                                            class="form-check-input" value="immediate" data-bs-toggle="collapse"
                                            data-bs-target=".inline-datepicker" {{ $checked }}>
                                        <label class="form-check-label" for="edit-publish_immediately">Immediately</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" id="edit-schedule" name="publish_type"
                                            class="form-check-input" value="scheduled" data-bs-toggle="collapse"
                                            data-bs-target=".inline-datepicker"
                                            {{ $post && $post->status_id == 2 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit-schedule">Set date and time</label>
                                    </div>
                                    <div
                                        class="collapse inline-datepicker {{ $post && $post->status_id == 2 ? 'show' : '' }}">
                                        <input type="text" class="form-control a-input datetimepicker-inline"
                                            name="schedule_time" placeholder="Choose date"
                                            value="{{ $post && $post->status_id == 2 ? $post->published_at : '' }}">
                                    </div>
                                </div>
                            @endif
                            <div>
                                <label for="edit-category_id" class="form-label">Post Category</label>
                                <select class="form-control a-input " id="edit-category_id" name="category_id">
                                    <option value="">--- Select ---</option>
                                    @forelse ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $post && $post->category_id == $category->id ? 'selected' : '' }}>
                                            {!! $category->name !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            @if (can_publish($logged_user))
                                <div>
                                    <label class="form-label" for="post-tags">Tags</label>
                                    @php
                                        $post_tags = '';
                                        if ($post && $post->tags) {
                                            foreach ($post->tags as $tag) {
                                                $post_tags .= $tag->name . ',';
                                            }
                                        }
                                    @endphp
                                    <input type="text" class="form-control a-input " id="post-tags" name="tags"
                                        data-role="tagsinput" placeholder="Tags" value="{{ $post_tags }}" />
                                </div>
                            @endif
                            <div>
                                <label class="form-label" for="edit-authors">Authors</label>
                                <select class="form-select dselect post-authors" id="edit-authors" name="authors[]"
                                    multiple required>
                                    <option value="">Authors</option>
                                    @forelse ($authors as $author)
                                        @php
                                            $selected = '';
                                        @endphp
                                        @if ($post)
                                            @foreach ($post->authors as $post_author)
                                                @if ($post_author->id == $author->id)
                                                    @php
                                                        $selected = 'selected';
                                                        break;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        @elseif($author->id == $logged_user->id)
                                            @php
                                                $selected = 'selected';
                                            @endphp
                                        @endif
                                        <option value="{{ $author->id }}" {{ $selected }}>
                                            {{ $author->display_name }}</option>
                                    @empty
                                        <option value="{{ $logged_user->id }}" selected>{{ $logged_user->display_name }}
                                        </option>
                                    @endforelse
                                </select>
                                <div class="invalid-feedback">
                                    Please provide at least one author.
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="blogTitle">Featured Image</label>
                                @php
                                    if ($post && $post->main_image) {
                                        $image = Storage::disk('public')->url($post->main_image->file_name);
                                    } else {
                                        $image = fetch_image(null, 'md');
                                    }
                                @endphp
                                <div class="post-thumbnail w-100"><img id="img-featured_image"
                                        class="post-thumbnail w-100 img-fluid" src="{{ $image }}"
                                        alt="Responsive image"></div>
                                </br>
                                <a href="#" data-dest="featured_image" class="btn btn-primary btn-upload-img"
                                    data-bs-toggle="modal" data-bs-target="#modalSetFeaturedImage">Change Image</a>
                            </div>
                            @if (can_publish($logged_user))
                                @if ($is_product)
                                    <div>
                                        <label class="form-label" for="edit-sku">SKU</label>
                                        <input type="text" class="form-control a-input " id="edit-sku"
                                            name="sku" autocomplete="off" placeholder="Product SKU"
                                            value="{{ $product->sku ?? '' }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="edit-post_label">Quantity</label>
                                        <input type="text" class="form-control a-input " id="edit-quantity"
                                            name="quantity" autocomplete="off" placeholder="Stock Quantity"
                                            value="{{ $product->quantity ?? '' }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="edit-post_label">Unit Price</label>
                                        <input type="number" class="form-control a-input " id="edit-unit_price"
                                            name="unit_price" autocomplete="off" placeholder="Unit Price"
                                            value="{{ $product->unit_price ?? '' }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="edit-post_label">Discounted Price</label>
                                        <input type="number" class="form-control a-input " id="edit-discounted_price"
                                            name="discounted_price" autocomplete="off" placeholder="Discounted Price"
                                            value="{{ $product->discounted_price ?? '' }}">
                                    </div>
                                    <div>
                                        <label class="form-label" for="edit-post_label">Unit of Measurement</label>
                                        <input type="text" class="form-control a-input " id="edit-unit_measurement"
                                            name="unit_measurement" autocomplete="off" placeholder="Unit of measurement"
                                            value="{{ $product->unit_measurement ?? '' }}">
                                    </div>
                                @else
                                    <div>
                                        <label class="form-label" for="edit-post_label">Post Label</label>
                                        <input type="text" class="form-control a-input " id="edit-post_label"
                                            name="post_label" autocomplete="off" placeholder="Add a label on the story"
                                            value="{{ $post->post_label ?? '' }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Post Options</label>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" id="edit-is_breaking" name="is_breaking"
                                                class="form-check-input"
                                                {{ $post && $post->is_breaking ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit-is_breaking">Breaking News</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" id="edit-is_featured" name="is_featured"
                                                class="form-check-input"
                                                {{ $post && $post->is_featured ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit-is_featured">Featured Story</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" id="edit-is_sponsored" name="is_sponsored"
                                                class="form-check-input"
                                                {{ $post && $post->is_sponsored ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit-is_sponsored">Sponsored Post</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" id="edit-send_notification" name="send_notification"
                                                class="form-check-input">
                                            <label class="form-check-label" for="edit-send_notification">Send push
                                                notification</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" id="edit-display_ads" name="display_ads"
                                                class="form-check-input"
                                                {{ $post && !$post->display_ads ? '' : 'checked' }}>
                                            <label class="form-check-label" for="edit-display_ads">Display Ads</label>
                                        </div>
                                        @if (config('cms.has_premium_feature'))
                                            <div class="form-check mt-2">
                                                <input type="checkbox" id="edit-is_premium" name="is_premium"
                                                    class="form-check-input"
                                                    {{ $post && !$post->is_premium ? '' : 'checked' }}>
                                                <label class="form-check-label" for="edit-is_premium">Is Premium</label>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if (get_option('ak_sticky_posts') > 0)
                                    <div>
                                        <label for="edit-homepage_ordering" class="form-label">Homepage Ordering</label>
                                        <select class="form-control a-input " name="homepage_ordering"
                                            id="edit-homepage_ordering">
                                            <option value="0"
                                                {{ $post && $post->homepage_ordering == 0 ? 'selected' : '' }}>--- Select
                                                ---</option>
                                            @for ($i = 0; $i < get_option('ak_sticky_posts'); $i++)
                                                @php
                                                    $j = $i;
                                                    $j++;
                                                @endphp
                                                <option value="{{ $j }}"
                                                    {{ $post && $post->homepage_ordering == $j ? 'selected' : '' }}>Story
                                                    {{ $j }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                @endif
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <span id="edit-loader" class="form-text ms-2 submit-edit hidden">
                                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="btn-group btn-group w-100 mb-1">
                                    <button
                                        class="btn d-flex btn-light justify-content-center gap-1 btn-post-add btn-post-edit"
                                        data-type="save" type="button">
                                        <i class="fas fa-save mt-1"></i> Save Changes
                                    </button>
                                    <a href="{{ $preview_url ?? '#' }}" id="a-preview" data-href="0"
                                        class="btn d-flex btn-light justify-content-center gap-1 btn-post-add">
                                        <i class="fas fa-eye mt-1 text-default"></i> Preview
                                    </a>
                                </div>

                                <a id="post-edit-go-back" href="{{ route('posts.index', 'all') }}"
                                    class="btn d-flex btn-danger justify-content-center mt-2 gap-1">
                                    <i class="fas fa-arrow-left mt-1 text-light"></i> Go Back
                                </a>

                                @if (can_publish($logged_user))
                                    @if (!$post || ($post && $post->status_id != 3))
                                        <button id="action-publish"
                                            class="btn d-flex justify-content-center align-items-center gap-1 w-100 btn-primary mt-2 btn-post-edit"
                                            data-type="publish" type="submit" {{ $post ? '' : 'disabled' }}>
                                            Publish <i class="fas fa-arrow-right"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /Main body -->

    <div class="modal fade" id="currentEditorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="currentEditorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="take-over-post-editing" class="form" method="POST" action="{{ route('take_over_post') }}">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id ?? '' }}">
                    <div class="modal-body">
                        <p class="font-30" id="take-over-title">This post is being edited by someone else. Would you like
                            to take over editing?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('posts.index', 'all') }}" type="button" class="btn btn-danger">No</a>
                        <button type="submit" class="btn btn-success">Yes... Take over.</button>
                        <span id="delete-loader" class="form-text ms-2 submit-edit hidden">
                            <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('backend.pages.media.media_modal')

@endsection

@section('scripts')
    <script>
        let submit_edit_url = '{{ route('update_post') }}';
        var file_upload_url = '{{ route('upload_file') }}';
        var image_tags_update_url = '{{ route('update_image_tags') }}';
        var intext_image_upload_url = '{{ route('upload_intext_file') }}';
        var tags = {!! json_encode($tags) !!};
        var fetch_thumbnails_url = "{{ route('media.fetch_images', '1') }}";
        var post_task = "edit";
        var show_editor_modal = '{{ $show_editor_modal ?? '' }}';
        var can_enable_delete = false;
    </script>


    <script src="{{ asset('theme/backend/vendor/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/dselect/dselect.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/slugify/slugify.js') }}"></script>
    <script src="{{ asset('theme/backend/js/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('theme/backend/js/typeahead.js') }}"></script>
    <script src="{{ asset('theme/backend/js/posts.js?v=1.0.5.1') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-lite.js?v=1.0.6') }}"></script>
    <script src="{{ asset('theme/backend/vendor/summernote/summernote-image-captionit.js') }}"></script>
    <script src="{{ asset('theme/backend/js/summernote.js?v1.0.6') }}"></script>
    <script src="{{ asset('theme/backend/js/media.js?v=1.0.5') }}"></script>

    <script>
        $(document).ready(function() {
            if (show_editor_modal) {
                $("#currentEditorModal").modal("show");
            }
        });
    </script>
@endsection
