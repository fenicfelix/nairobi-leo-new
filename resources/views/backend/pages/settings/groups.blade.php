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
            <li class="breadcrumb-item active" aria-current="page">User groups</li>
          </ol>
        </nav>


        <div class="row g-5">
          <div class="col-12 col-md-4">
            <div id="add-div">
                <form class="ajax-form" id="ajax-form-add" method="POST">
                    @csrf
                    <input type="hidden" id="add-seo_status" name="seo_status" value="0">
                    <h3 class="fw-black">Add Group</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                    <div class="vstack gap-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" data-task="add" id="add-name" name="name" placeholder="Group name" required>
                            <label for="add-name" class="form-label">Group Name</label>
                        </div>
                        <div class="form-floating">
                            <textarea class="form-control h-120" data-task="add" id="add-description" name="description" maxlength="255" rows="5" placeholder="Description"></textarea>
                            <label for="add-description" class="form-label">Description</label>
                        </div>
                    </div>
                    <div class="row mt-3">
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

            <div id="edit-div" class="hidden">
                <form class="ajax-form" id="ajax-form-edit" method="POST">
                    @csrf
                    <input type="hidden" id="edit-id" name="id" value="0">
                    <h3 class="fw-black">Edit Category</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                    <div class="vstack gap-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" data-task="edit" id="edit-name" name="name" placeholder="Group name" required>
                            <label for="edit-name" class="form-label">Group Name</label>
                        </div>
                        <div class="form-floating">
                            <textarea class="form-control h-120" data-task="edit" id="edit-description" name="description" maxlength="255" rows="5" placeholder="Description"></textarea>
                            <label for="edit-description" class="form-label">Description</label>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Save Changes">
                            <input type="reset" class="btn btn-light btn-edit-reset" value="Cancel">
                            <span id="edit-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
          </div>
          <div class="col-12 col-md-8">
            <h3 class="fw-black">All user groups</h3>
            <div class="vstack gap-2">
                <div class="table-responsive">
                    <table id="dt-server-side" class="table table-bordered table-striped dt-custom-table w-100">
                        <!-- Filter columns -->
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Group Name</th>
                                <th>Description</th>
                                @if ($logged_user->group_id == 1)
                                    <th><center>Action</center></th>
                                @endif 
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
        let submit_add_url = '{{ route("settings.user_groups.store") }}';
        let submit_edit_url = '{{ route("update_user_group") }}';
        let submit_delete_url = "";
        datatable_url = "{{ route('datatable.get_user_groups') }}";
        tableDefaultFilter = [0, "DESC"];

        $('.seo-keywords').tagsinput({});

        $(document).on("click", ".btn-edit", function (e) {
            e.preventDefault();
            $("#add-div").hide();
            $("#edit-div").show();

            $("#edit-id").val($(this).data('id'));
            $("#edit-name").val($(this).data('name'));
            $("#edit-description").val($(this).data('desc'));
        });

        $(".btn-edit-reset").on('click', function(e) {
            e.preventDefault();
            $("#add-div").show();
            $("#edit-div").hide();
        });
        
    </script>
@endsection