@extends('backend.layouts.backend')

@section('styles')
    
@endsection

@section( 'main_body')

    <!-- Main body -->
      <div id="main-body">
        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="j{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
          </ol>
        </nav>

        <div class="row g-4">
          <div class="col-md-4 col-lg-3 d-none d-md-block">
            <div class="card h-100">
              <div class="card-body navbar-light">
                <div class="navbar-nav nav">
                  <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3 active" href="#profile" role="tab" aria-selected="true">
                    <i class="fas fa-user"></i>
                    User Profile
                  </a>
                  <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3" href="#security" role="tab" aria-selected="false">
                    <i class="fas fa-lock"></i>
                    Security
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-lg-9">
            <div class="card h-100">
              <div class="card-body tab-content">
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                  <h3 class="fw-black">User Profile</h3>
                  <p class="small text-secondary mb-4">
                    This information will be displayed publicy so be careful what you share.
                  </p>
                  <form class="ajax-form" id="ajax-form-edit" method="POST">
                    @csrf
                    <input type="hidden" id="edit-id" name="id" value="{{ $user->id }}">
                    <div class="d-flex flex-column flex-lg-row gap-4">
                      <div class="order-1 order-lg-0">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-first_name" name="first_name" placeholder="First name" value="{{ $user->first_name }}" required>
                                    <label for="edit-first_name" class="form-label">First name</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-last_name" name="last_name" placeholder="First name" value="{{ $user->last_name }}" required>
                                    <label for="edit-last_name" class="form-label">Last name</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-display_name" name="display_name" placeholder="Display name" value="{{ $user->display_name }}" required>
                                    <label for="edit-display_name" class="form-label">Display name</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-email" name="phone_number" placeholder="Phone Number" value="{{ $user->phone_number }}" required>
                                    <label for="edit-email" class="form-label">Phone Number</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-email" name="email" placeholder="Email Address" value="{{ $user->email_address }}" required>
                                    <label for="edit-email" class="form-label">Email Address</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control autosize" style="height: 120px" id="edit-biography" name="biography" placeholder="Write something about you">{{ $user->biography }}</textarea>
                                    <label for="edit-biography" class="form-label">Bio</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-facebook" name="facebook" placeholder="Facebook URL" value="{{ $user->facebook }}">
                                    <label for="edit-facebook" class="form-label">Facebook</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-twitter" name="twitter" placeholder="Twitter URL" value="{{ $user->twitter }}">
                                    <label for="edit-twitter" class="form-label">Twitter</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-instagram" name="instagram" placeholder="Instagram URL" value="{{ $user->instagram }}">
                                    <label for="edit-instagram" class="form-label">Instagram</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit-linkedin" name="linkedin" placeholder="LinkedIn URL" value="{{ $user->linkedin }}">
                                    <label for="edit-linkedin" class="form-label">LinkedIn</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                <span id="edit-loader" class="form-text ms-2 submit-add hidden">
                                  <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                              </span>
                            </div>
                        </div>
                      </div>
                      <div class="order-0 order-lg-1 d-flex flex-column align-items-start align-items-lg-center gap-2">
                        <label class="form-label text-center fw-bold">Photo</label>
                        @php
                            $thumbnail = "https://ui-avatars.com/api/?name=" . $user->first_name . "+" . $user->last_name . "&color=4c4e52&background=bdbec1";
                            if($user->thumbnail) $thumbnail = Storage::disk('public')->url($user->thumbnail);
                        @endphp
                        <img src="{{ $thumbnail }}" alt="User" width="150" height="150" class="rounded-circle" loading="lazy">
                      </div>
                    </div>
                  </form>
                </div>
                <div class="tab-pane fade" id="security" role="tabpanel">
                  <h3 class="fw-black">Security</h3>
                  <fieldset class="form-fieldset">
                    <legend>Reset Password</legend>
                    <form class="ajax-form" id="ajax-form-reset-password" method="POST">
                      @csrf
                      <input type="hidden" id="reset-id" name="id" value="{{ $user->id }}">
                      <input type="hidden" id="reset-task" name="task" value="reset">
                      <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Reset Password</button>
                            <span id="reset-loader" class="form-text ms-2 submit-add hidden">
                              <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                          </span>
                        </div>
                      </div>
                    </form>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /Main body -->

@endsection

@section('scripts')

<script>
  let submit_edit_url = '{{ route("update_profile") }}';
  let password_change_url = '{{ route("change_password") }}';
</script>
    
@endsection