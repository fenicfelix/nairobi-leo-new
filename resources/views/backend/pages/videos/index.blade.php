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
            <li class="breadcrumb-item active" aria-current="page">Videos</li>
          </ol>
        </nav>


        <div class="row g-5">
          <div class="col-12">
            <h3 class="fw-black">All Videos</h3>
            <div class="vstack gap-2">
                <div class="table-responsive">
                    <table id="dt-server-side" class="table table-bordered table-striped dt-custom-table w-100">
                        <!-- Filter columns -->
                        <thead>
                            <tr>
                                <td>#</td>
                                <th>Thumbnail</th>
                                <th>Title</th>
                                <th>Video ID</th>
                                <th>Source</th>
                                <th>Live</th>
                                <th>Status</th>
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
    <script>
        datatable_url = "{{ route('datatable.get_videos') }}";
        tableDefaultFilter = [0, "DESC"];
        
    </script>
@endsection