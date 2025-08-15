<!-- Required JS -->

    <script src="{{ asset('theme/backend/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/bootstrap/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/simplebar/simplebar.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/slugify/slugify.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('theme/backend/js/script.js') }}"></script>
    
    <script src="{{ asset('theme/backend/js/functions.js?v=1.1.1') }}"></script>

    <script src="{{ asset('theme/backend/vendor/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('theme/backend/vendor/datatables/datatables.min.js') }}"></script>

    <script>
        let datatable_url = "{{ route('datatable.get_users') }}";
        let tableDefaultFilter = [0, "DESC"];
        var app_url = "{{ get_option('ak_app_title') }}";
        function update_seo_status(status) {
            let status_message = '';
            if (status >= 80) status_message = '<i class="fas fa-circle text-success"></i>';
            else if (status >= 60) status_message = '<i class="fas fa-circle text-warning"></i>';
            else status_message = '<i class="fas fa-circle text-danger"></i>';

            return status_message;
        }
  </script>

  @if (session('error'))
      <script>
        toastr["error"]("{{ session('error') }}", { closeButton: true, progressBar: true, timeOut: 5000 });
      </script>
  @endif

  @if (session('warning'))
      <script>
        toastr["error"]("{{ session('warning') }}", { closeButton: true, progressBar: true, timeOut: 5000 });
      </script>
  @endif

  @if (session('success'))
      <script>
        toastr["success"]("{{ session('success') }}", { closeButton: true, progressBar: true, timeOut: 5000 });
      </script>
  @endif
  
  @yield('scripts')

    <div class="modal fade delete-modal" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <form class="form" id="ajax-form-delete" data-form_type="modal">
                    @csrf
                    @method("delete")
                    <input type="hidden" id="delete-id" name="identifier"required>
                    <div class="modal-body">
                        <p class="font-30">Are you sure you want to delete <strong><span id="delete-name"></span></strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-success">Yes... Delete Now.</button>
                        <span id="delete-loader" class="form-text ms-2 submit-edit hidden">
                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="recoverConfirmationModal" tabindex="-1" aria-labelledby="recoverConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <form class="form" id="ajax-form-recover" data-form_type="modal">
                    @csrf
                    @method("post")
                    <input type="hidden" id="recover-id" name="identifier"required>
                    <div class="modal-body">
                        <p class="font-30">Are you sure you want to recover <strong><span id="recover-name"></span></strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-success">Yes... Recover Now.</button>
                        <span id="recover-loader" class="form-text ms-2 submit-edit hidden">
                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="permanentDeleteConfirmationModal" tabindex="-1" aria-labelledby="permanentDeleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <form class="form" id="ajax-form-permanent-delete" data-form_type="modal">
                    @csrf
                    @method("post")
                    <input type="hidden" id="permanent-delete-id" name="identifier"required>
                    <div class="modal-body">
                        <p class="font-30">Are you sure you want to permanently delete <strong><span id="permanent-delete-name"></span></strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-success">Yes... Delete Now.</button>
                        <span id="permanent-delete-loader" class="form-text ms-2 submit-edit hidden">
                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
  
</body>

</html>