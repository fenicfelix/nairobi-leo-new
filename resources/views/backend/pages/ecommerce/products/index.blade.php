@extends('backend.layouts.backend')

@section('styles')
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/datatables/datatables.min.css') }}">
@endsection

@section('main_body')
    <!-- Main body -->
    <div id="main-body">

        <nav aria-label="breadcrumb" id="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>


        <div class="row g-5">
            <div class="col-12">
                <h3 class="fw-black">All Products</h3>
                <div class="row my-3">
                    <div class="d-flex justify-content-end">
                        <div class="p-2 bd-highlight">
                            <a class="btn btn-primary text-white pull-right" href="{{ route('products.create') }}">Add
                                New</a>
                        </div>
                    </div>
                </div>
                <div class="vstack gap-2">
                    <div class="table-responsive">
                        <table id="dt-server-side" class="table table-bordered table-striped dt-custom-table w-100">
                            <!-- Filter columns -->
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Published On</th>
                                    <th>SEO</th>
                                    <th>Unit Price</th>
                                    <th>Per</th>
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
    <script src="{{ asset('theme/backend/vendor/datatables/datatables.min.js') }}"></script>
    <script>
        datatable_url = "{{ route('datatable.get_products') }}";
        tableDefaultFilter = [0, "DESC"];
    </script>
@endsection
