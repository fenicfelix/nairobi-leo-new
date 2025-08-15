@extends('backend.layouts.backend')

@section('styles')
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/schedule/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/schedule/jquery.schedule.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/schedule/jquery.schedule-demo.css') }}">
    
@endsection

@section( 'main_body')

    <!-- Main body -->
      <div id="main-body">

        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Program lineup</li>
          </ol>
        </nav>


        <div class="row g-5">
          <div class="col-12">
            <h3 class="fw-black">Program Lineup</h3>
            <div class="row my-3">
              <div class="d-flex justify-content-between">
                <div class="bd-highlight">
                </div>
                <div class="p-2 bd-highlight">
                  <a id="export" class="btn btn-primary text-white pull-right" href="#">Update Changes</a>
                  <a class="btn btn-danger text-white pull-right" data-bs-toggle="modal" data-bs-target="#deleteLineupConfirmationModal">Clear Lineup</a>
                </div>
              </div>
            </div>
            <div class="vstack gap-2">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-12">
                        <div id="schedule3" class="jqs-demo mb-3"></div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </div>
      </div>
      <!-- /Main body -->

      <div class="modal fade" id="deleteLineupConfirmationModal" tabindex="-1" aria-labelledby="deleteLineupConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <form class="form" id="ajax-lineup-clear" data-form_type="modal">
                    @csrf
                    @method("delete")
                    <div class="modal-body">
                        <p class="font-30">Are you sure you want to clear the program lineup?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-success">Yes... Clear Now.</button>
                        <span id="delete-loader" class="form-text ms-2 submit-edit hidden">
                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
  @if (session('success'))
      <script>
        toastr["success"]("{{ session('success') }}", "SUCCESS", { closeButton: true, progressBar: true, timeOut: 5000 });
      </script>
  @endif
  <script>
    let submit_delete_url = '{{ route("tv.program_lineup.destroy", 1) }}';
    var program_lineup = [];//JSON.parse('{!! $program_lineup !!}');
  </script>
  @if ($program_lineup)
      <script>
        program_lineup = JSON.parse('{!! $program_lineup !!}');
      </script>
  @endif
  <script src="{{ asset('theme/backend/vendor/schedule/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('theme/backend/vendor/schedule/jquery.schedule.js') }}"></script>

  <script>
    let shows = JSON.parse('{!! json_encode($shows) !!}');
    var lineup_post_url = "{{ route('tv.program_lineup.store') }}";
    $("#clear-lineup").click(function(e) {
      e.preventDefault();
    });
    $(function () {
      $('#schedule3').jqs({
        //data: program_lineup,
      });

      $('#export').click(function () {
        $("#lineup-loader").show();
        var formData = {
          _token: "{{ csrf_token() }}",
          data: $('#schedule3').jqs('export')
        }
        $.ajax({
            type: "POST",
            url: lineup_post_url,
            data: formData,
            success: function (result) {
                console.log(result);
                $("#lineup-loader").fadeOut(500);
                if (result.status === '000') {
                  toastr["success"](result.message, "SUCCESS", { closeButton: true, progressBar: true, timeOut: 5000 });
                } else {
                  toastr["error"](result.message, "Oops!", { closeButton: true, progressBar: true, timeOut: 10000 });
                }
            },
            error: function (xhr, status, errorThrown) {
              $("#lineup-loader").fadeOut(500);
              toastr["error"](xhr.responseText, xhr.status, { closeButton: true, progressBar: true, timeOut: 10000 });
            }
        });
      });
    });
  </script>
    
@endsection