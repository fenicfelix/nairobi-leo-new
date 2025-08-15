@extends('backend.layouts.backend')

@section('styles')

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
@endsection

@section( 'main_body')

    <!-- Main body -->
      <div id="main-body">

        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Menus</li>
          </ol>
        </nav>



        <div class="row g-5">
          <div class="col-12 col-md-4">
            <div class="row">
                <div class="row">
                    <div class="col-12">
                        <h3 class="fw-black">Menu Items</h3>
                    </div>
                </div>
                <div class="col-12">
                    <div class="accordion" id="menuItemsAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header fw-bold" id="pagesMenuItem">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                                    Pages
                                </button>
                            </h2>
                            <div id="collapsePages" class="accordion-collapse collapse show" aria-labelledby="pagesMenuItem" data-bs-parent="#menuItemsAccordion">
                                <div class="accordion-body">
                                    <table class="table table-bordered">
                                        @foreach ($pages as $page)
                                            <tr>
                                                <td>{{ ucwords(strtolower($page->title)) }}</td>
                                                <td>
                                                    <form class="menu-item-form">
                                                        <input type="hidden" name="type" value="page">
                                                        <input type="hidden" name="reference_id" value="{{ $page->id }}">
                                                        <input type="hidden" name="title" value="{{ $page->title }}">
                                                        <input type="hidden" name="display_title" value="{{ $page->title }}">
                                                        <input type="hidden" name="slug" value="{{ $page->slug }}">
                                                        <input type="hidden" name="url" value="#">
                                                        <small><input type="submit" class="btn btn-primary btn-sm" value="Add to Menu"/></small>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="categoriesMenuItem">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="false" aria-controls="collapseCategories">
                                    Categories
                                </button>
                            </h2>
                            <div id="collapseCategories" class="accordion-collapse collapse" aria-labelledby="categoriesMenuItem" data-bs-parent="#menuItemsAccordion">
                                <div class="accordion-body">
                                    <table class="table table-bordered">
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>{{ ucwords(strtolower($category->title)) }}</td>
                                                <td>
                                                    <form class="menu-item-form">
                                                        <input type="hidden" name="type" value="category">
                                                        <input type="hidden" name="reference_id" value="{{ $category->id }}">
                                                        <input type="hidden" name="title" value="{{ $category->title }}">
                                                        <input type="hidden" name="display_title" value="{{ $category->title }}">
                                                        <input type="hidden" name="slug" value="{{ $category->slug }}">
                                                        <input type="hidden" name="url" value="#">
                                                        <small><input type="submit" class="btn btn-primary btn-sm" value="Add to Menu"/></small>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="postsMenuItem">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePosts" aria-expanded="false" aria-controls="collapsePosts">
                                Posts
                            </button>
                            </h2>
                            <div id="collapsePosts" class="accordion-collapse collapse" aria-labelledby="postsMenuItem" data-bs-parent="#menuItemsAccordion">
                                <div class="accordion-body">
                                    <table class="table table-bordered">
                                        @foreach ($posts as $post)
                                            <tr>
                                                <td>{{ ucwords(strtolower($post->title)) }}</td>
                                                <td>
                                                    <form class="menu-item-form">
                                                        <input type="hidden" name="type" value="post">
                                                        <input type="hidden" name="reference_id" value="{{ $post->id }}">
                                                        <input type="hidden" name="title" value="{{ $post->title }}">
                                                        <input type="hidden" name="display_title" value="{{ $post->title }}">
                                                        <input type="hidden" name="slug" value="{{ $post->slug }}">
                                                        <input type="hidden" name="url" value="#">
                                                        <small><input type="submit" class="btn btn-primary btn-sm" value="Add to Menu"/></small>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="customLinksMenuItem">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustomLinks" aria-expanded="false" aria-controls="collapseCustomLinks">
                                Custom Links
                            </button>
                            </h2>
                            <div id="collapseCustomLinks" class="accordion-collapse collapse" aria-labelledby="customLinksMenuItem" data-bs-parent="#menuItemsAccordion">
                                <div class="accordion-body">
                                    <form class="menu-item-form">
                                        <input type="hidden" name="type" value="custom">
                                        <input type="hidden" name="reference_id" value="">
                                        <div class="row">
                                            <div class="col-12 col-md-3">
                                                <label for="add-custom_title" class="col-form-label">Link Title</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" id="add-custom_title" class="form-control slugify title" name="title" placeholder="Enter title to be displayed" required>
                                            </div>
                                        </div>
                                        <input type="hidden" id="add-custom-display-title" name="display_title" value="">
                                        <input type="hidden" id="slug" name="slug" value="">
                                        <div class="row mt-3">
                                            <div class="col-12 col-md-3">
                                                <label for="add-custrom_url" class="col-form-label">URL</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" id="add-custrom_url" class="form-control" name="url" placeholder="https://" required>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <input type="submit" class="btn btn-primary float-end" value="Add to Menu">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>

          <div class="col-12 col-md-8">
            <div class="col-12">
                <h3 class="fw-black">Create New Menu</h3>
            </div>
            <div class="card">
                <div class="card-body">
                    <form class="ajax-form" id="ajax-form-add" method="POST">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-2">
                                <label for="add-menu_name" class="col-form-label">Menu Name</label>
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" id="add-menu_name" class="form-control slugify" name="title" data-task="add" placeholder="Enter menu name">
                                <input type="hidden" id="add-slug" class="form-control" name="slug">
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="submit" class="btn btn-primary" value="Create Menu">
                                <span id="add-loader" class="form-text ms-2 submit-add hidden">
                                    <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <small class="fst-italic mt-4">Key in your menu name above, then click <b>Create Menu</b></small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if ($menus)
                <div class="col-12">
                    <h3 class="fw-black mt-4">Menu Structure</h3>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-2">
                                <label for="add-menu_name" class="col-form-label">Select a menu to edit</label>
                            </div>
                            <div class="col-12 col-md-4">
                                <select class="form-control" id="select-menu" name="menu_id">
                                    <option value="">Select Menu</option>
                                    @foreach ($menus as $menu)
                                        <option value="{{ $menu->id }}">{{ $menu->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <span id="add-loader" class="form-text ms-2 submit-add hidden">
                                    <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                </span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h5 class="fw-bold">Menu Items</h5>
                            </div>
                            <div class="col-12 col-md-6">
                                <form id="add-menu-items-form">
                                    @csrf
                                    <input type="hidden" id="add-menu-id" name="menu_id">
                                    <input type="hidden" id="add-menu-items" name="menu_items">
                                    <input type="hidden" id="add-menu-items-order" name="menu_order">
                                </form>
                                <div class="menu-structure">
                                    <ul class="list-group" id="sortable">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="save-menu-items" class="btn btn-primary">Save Changes</button>
                        {{-- <button class="btn btn-danger">Delete Menu</button> --}}
                    </div>
                </div>
            @endif
          </div>

        </div>
      </div>
      
      <!-- /Main body -->

@endsection

@section('scripts')
<script src="{{ asset('theme/backend/js/jquery.serialize-object.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script>
    var menu_array = [];
    var count = 0;
    let submit_add_url = '{{ route("settings.menus.store") }}';
    var get_menu_items = '{{ route("get_menu_items") }}';
    var add_menu_items_url = '{{ route("settings.menu_items.store") }}';
    var selectedMenu = '{{ session("menu") }}';
</script>
<script src="{{ asset('theme/backend/js/menu.js') }}"></script>

@endsection