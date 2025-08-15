@extends('backend.layouts.backend')

@section('styles')

    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/datatables/datatables.min.css') }}">
    
@endsection

@section( 'main_body')

    <!-- Main body -->
      <div id="main-body">

        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
          </ol>
        </nav>


        <div class="row g-5">
          <div class="col-12 col-md-4">
            <div id="add-div">
                <form class="ajax-form" id="ajax-form-add" method="POST" autocomplete="false" aria-autocomplete="false">
                    @csrf
                    <h3 class="fw-black">Add User</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                    <div class="vstack gap-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="add-first_name" name="first_name" placeholder="First name" required>
                            <label for="add-first_name" class="form-label">First name</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="add-last_name" name="last_name" placeholder="Last name" required>
                            <label for="add-last_name" class="form-label">Last name</label>
                        </div>
                        <div class="form-floating">
                            <select class="form-control" name="group_id" id="add-group_id" required>
                                <option value="">--- Select ---</option>
                                @forelse ($user_groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @empty
                                    
                                @endforelse
                            </select>
                            <label for="add-group_id" class="form-label">User group</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control add-form-phone-number" data-task="add" id="add-phone_number" name="phone_number" placeholder="Phone Number">
                            <label for="add-phone_number" class="form-label">Phone Number</label>
                        </div>
                        <div class="form-floating">
                            <input type="email" class="form-control add-form-email" data-task="add" id="add-email" name="email" placeholder="Email Address" required>
                            <label for="add-email" class="form-label">Email address</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control add-form-username" id="add-username" name="username" placeholder="Username" required>
                            <label for="add-username" class="form-label">Username</label>
                        </div>
                        <label for="add-password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="add-password" name="password" placeholder="Password" aria-label="Password" aria-describedby="show-hide-password" value="{{ get_option('ak_default_password') }}">
                            <button type="button" class="input-group-text show-hide-password">Show</button>
                        </div>
                        <small class="fst-italic">Copy the password before submitting the form.</small>
                        <div class="form-check mt-2">
                            <input type="checkbox" id="add-password_copy" class="form-check-input" required>
                            <label class="form-check-label" for="add-password_copy">YES, I have copied the password.</label>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-danger" value="Reset">
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
                    <h3 class="fw-black">Edit User</h3>
                    <p>Fill in the form below and submit. All fields are important.</p>
                    <div class="vstack gap-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit-first_name" name="first_name" placeholder="First name" required>
                            <label for="edit-first_name" class="form-label">First name</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit-last_name" name="last_name" placeholder="Last name" required>
                            <label for="edit-last_name" class="form-label">Last name</label>
                        </div>
                        <div class="form-floating">
                            <select class="form-control" name="group_id" id="edit-group_id" required>
                                <option value="">--- Select ---</option>
                                @forelse ($user_groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @empty
                                    
                                @endforelse
                            </select>
                            <label for="edit-group_id" class="form-label">User group</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control add-form-phone-number" data-task="edit" id="edit-phone_number" name="phone_number" placeholder="Phone Number">
                            <label for="edit-phone_number" class="form-label">Phone Number</label>
                        </div>
                        <div class="form-floating">
                            <input type="email" class="form-control" id="edit-email" name="email" placeholder="Email Address" required>
                            <label for="edit-email" class="form-label">Email address</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit-username" name="username" placeholder="Username" required>
                            <label for="edit-username" class="form-label">Username</label>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Save Changes">
                            <input type="reset" class="btn btn-danger btn-edit-reset" value="Cancel">
                            <span id="edit-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
          </div>
          <div class="col-12 col-md-8">
            <h3 class="fw-black">All Users</h3>
            <div class="vstack gap-2">
                <div class="table-responsive">
                    <table id="dt-server-side" class="table table-bordered table-striped dt-custom-table w-100">
                        <!-- Filter columns -->
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Posts</th>
                                <th>Status</th>
                                @if ($logged_user->group_id == 1)
                                    <th>Action</th>
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

    <div class="modal fade" id="deleteUserConfirmationModal" tabindex="-1" aria-labelledby="deleteUserConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <form class="form" id="ajax-form-permanent-delete-user" data-form_type="modal" action="{{ route("delete_user") }}" method="POST">
                    @csrf
                    <input type="hidden" id="permanent-delete-id" name="identifier"required>
                    <div class="modal-body">
                        <p class="font-30">Are you sure you want to permanently delete <strong><span id="permanent-delete-name"></span></strong> ?</p>
                    </div>
                    <div class="row mx-2">
                        <div class="col-12">
                            <label for="trash-user_id" class="form-label">Re-assign content to?</label>
                        </div>
                        <div class="col-12">
                            <select class="form-control" name="user_id" id="trash-user_id" required>
                                <option value="">--- Select ---</option>
                                @forelse ($active_users as $user)
                                    <option value="{{ $user->id }}">{{ $user->display_name }}</option>
                                @empty
                                    
                                @endforelse
                            </select>
                            <small class="fst-italic">Select a user to assign all content to.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-success">Yes... Delete Now.</button>
                        <span id="permanent-delete-user-loader" class="form-text ms-2 submit-edit hidden">
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
        let submit_add_url = '{{ route("users.store") }}';
        let submit_edit_url = '{{ route("update_user") }}';
        datatable_url = "{{ route('datatable.get_users') }}";
        tableDefaultFilter = [0, "DESC"];

        $(".btn-edit-reset").on('click', function(e) {
            e.preventDefault();
            $("#add-div").show();
            $("#edit-div").hide();
        });

        $(document).on("click", ".btn-edit", function (e) {
            e.preventDefault();
            $("#add-div").hide();
            $("#edit-div").show();

            $("#edit-id").val($(this).data('id'));
            $("#edit-first_name").val($(this).data('fname'));
            $("#edit-last_name").val($(this).data('lname'));
            $("#edit-group_id").val($(this).data('group'));
            $("#edit-phone_number").val($(this).data('phone'));
            $("#edit-email").val($(this).data('email'));
            $("#edit-username").val($(this).data('username'));
        });

        $(".add-form-email").on("keyup", function(e) {
            let email = $("#"+$(this).data('task')+"-email").val();
            var emailArray = email.split("@");
            $("#"+$(this).data('task')+"-username").val(emailArray[0]);
        });

        $(document).on("click", ".btn-trash-user", function (e) {
            e.preventDefault();
            $("#permanent-delete-id").val($(this).data('id'));
            $("#permanent-delete-name").html($(this).data('name'));
        });
    </script>
@endsection