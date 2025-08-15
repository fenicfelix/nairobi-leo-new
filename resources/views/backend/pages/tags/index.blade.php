@extends('backend.layouts.backend')

@section('styles')

    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/css/bootstrap-tagsinput.css') }}">
    
@endsection

@section( 'main_body')

    <!-- Main body -->
      <div id="main-body">

        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tags</li>
          </ol>
        </nav>


        <div class="row g-5">
          <div class="col-12 col-md-4">
            <div id="add-div">
                <form class="ajax-form" id="ajax-form-add" method="POST">
                    @csrf
                    <input type="hidden" id="add-seo_status" name="seo_status" value="0">
                    <h3 class="fw-black">Add Tag</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                    <div class="vstack gap-3">
                        <div class="form-floating">
                            <input type="text" class="form-control slugify title" data-task="add" data-type="title" id="add-name" name="name" placeholder="Category name" required>
                            <label for="add-name" class="form-label">Name</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control slug" data-task="add" id="add-slug" name="slug" placeholder="Category slug" required>
                            <label for="add-slug" class="form-label">Slug</label>
                        </div>
                        <fieldset class="form-fieldset">
                            <legend>SEO Details <span id="add-seo-status" class="seo-status"><i class="fas fa-circle text-danger"></i></span></legend>
                            <div class="card seo-preview" id="add-seo-preview">
                                <div class="card-body">
                                    <p><i> <span id="hint-seo-logo"><img src="{{ config('cms.app_icon') }}" alt=""></span>{{ get_option('ak_app_url') }}/tag/<span id="add-hint-seo-permalink"></span></i></p>
                                    <p class="hint-seo-title text-primary"><span id="add-hint-seo-title"></span><span id="add-hint-seo-domain"> - {{ get_option('ak_app_title') }}</span></p>
                                    <p class="hint-seo-description" id="add-hint-seo-description">Please provide an SEO description by editing the snippet below. If you don’t, search engines will take part of your post body to show in the search results.</p>
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
                    <div class="row">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-light btn-edit-reset" value="Cancel">
                            <span id="add-loader" class="form-text ms-2 submit-add hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                </form>
            </div>

            <div id="edit-div" class="hidden">
                <form class="ajax-form" id="ajax-form-edit" method="POST">
                    @csrf
                    <input type="hidden" id="edit-seo_status" name="seo_status" value="0">
                    <input type="hidden" id="edit-id" name="id" value="0">
                    <h3 class="fw-black">Edit Tag</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                    <div class="vstack gap-3">
                        <div class="form-floating">
                            <input type="text" class="form-control slugify title seo" data-task="edit" data-type="title" id="edit-name" name="name" placeholder="Category name" required>
                            <label for="edit-name" class="form-label">Name</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control slug" data-task="edit" id="edit-slug" name="slug" placeholder="Category slug" required>
                            <label for="edit-slug" class="form-label">Slug</label>
                        </div>
                        <fieldset class="form-fieldset">
                            <legend>SEO Details <span id="edit-seo-status" class="seo-status"><i class="fas fa-circle text-danger"></i></span></legend>
                            <div class="card seo-preview" id="edit-seo-preview">
                                <div class="card-body">
                                    <p><i> <span id="hint-seo-logo"><img src="{{ config('cms.app_icon') }}" alt=""></span>{{ get_option('ak_app_url') }}/tag/<span id="edit-hint-seo-permalink"></span></i></p>
                                    <p class="hint-seo-title text-primary"><span id="edit-hint-seo-title"></span><span id="edit-hint-seo-domain"> - {{ get_option('ak_app_title') }}</span></p>
                                    <p class="hint-seo-description" id="edit-hint-seo-description">Please provide an SEO description by editing the snippet below. If you don’t, search engines will take part of your post body to show in the search results.</p>
                                </div>
                            </div>
                           <div class="mt-3">
                                <label for="edit-seo_keywords" class="form-label">Keywords <small>(Comma separated)</small></label>
                                <input type="text" class="form-control seo seo-keywords" data-task="edit" data-type="keywords" id="edit-seo_keywords" name="seo_keywords" maxlength="255" placeholder="Focus keyword">
                                <div class="progress" id="edit-seo_keywords_progress" style="height: 0.1rem;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="form-floating mt-3">
                                <input type="text" class="form-control seo" data-task="edit" data-type="title" id="edit-seo_title" name="seo_title" placeholder="SEO title">
                                <label for="edit-seo_title" class="form-label">Title</label>
                                <div class="progress" id="edit-seo_title_progress" style="height: 0.1rem;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="form-floating mt-3">
                                <textarea class="form-control h-120 seo" data-task="edit" data-type="description" id="edit-seo_description" name="seo_description" rows="5" maxlength="200" placeholder="SEO description"></textarea>
                                <label for="edit-seo_description" class="form-label">Description</label>
                                <div class="progress" id="edit-seo_description_progress" style="height: 0.1rem;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Save Changes">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="edit-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
          </div>
          <div class="col-12 col-md-8">
            <h3 class="fw-black">All Tags</h3>
            <div class="vstack gap-2">
                <div class="table-responsive">
                    <table id="dt-server-side" class="table table-bordered table-striped dt-custom-table w-100">
                        <!-- Filter columns -->
                        <thead>
                            <tr>
                                <td>#</td>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Posts</th>
                                <th>SEO Status</th>
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
<script src="{{ asset('theme/backend/js/bootstrap-tagsinput.js') }}"></script>
    <script>
        let submit_add_url = '{{ route("tags.store") }}';
        let submit_edit_url = '{{ route("update_tag") }}';
        let submit_delete_url = "";
        datatable_url = "{{ route('datatable.get_tags') }}";
        tableDefaultFilter = [0, "DESC"];

        $('.seo-keywords').tagsinput({});

        $(document).on("click", ".btn-edit", function (e) {
            e.preventDefault();
            $("#add-div").hide();
            $("#edit-div").show();

            $("#edit-id").val($(this).data('id'));
            $("#edit-name").val($(this).data('name'));
            $("#edit-slug").val($(this).data('slug'));
            $("#edit-seo_title").val($(this).data('seo_title'));
            $("#edit-seo_description").val($(this).data('seo_description'));
            $("#edit-seo_status").val($(this).data('seo_status'));

            
            if($(this).data('slug')) $("#edit-hint-seo-permalink").html($(this).data('slug'));
            if($(this).data('seo_title')) $("#edit-hint-seo-title").html($(this).data('seo_title'));
            if($(this).data('seo_description')) $("#edit-hint-seo-description").html($(this).data('seo_description'));

            $("#edit-seo-status").html(update_seo_status($(this).data('seo_status')));
            $("#edit-seo_keywords").tagsinput('removeAll');
            $("#edit-seo_keywords").tagsinput('add', $(this).data('seo_keywords'));
        });

        $(".btn-edit-reset").on('click', function(e) {
            e.preventDefault();
            $("#add-div").show();
            $("#edit-div").hide();
        });

        $(".title").on('keyup', function(e) {
            var title = $("#"+$(this).data('task')+"-name").val();

            $("#"+$(this).data('task')+"-seo_title").val(title);
            $("#"+$(this).data('task')+"-hint-title").html(title);
        });
        
    </script>
@endsection