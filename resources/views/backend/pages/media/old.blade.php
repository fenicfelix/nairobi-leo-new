@extends('backend.layouts.backend')

@section('styles')

    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/photoswipe/photoswipe.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/backend/vendor/photoswipe/default-skin/default-skin.css') }}">
    
@endsection

@section( 'main_body')

      <!-- Main body -->
      <div id="main-body">

        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Media</li>
          </ol>
        </nav>

        
          <input type="hidden" id="library_page" value="">
          <input type="hidden" id="library_page-prev" value="">
          <input type="hidden" id="library_page-next" value="">
          <div class="row">
              <div class="col-8">
                  <div class="row mt-4">
                      <div class="col-md-8 col-sm-12">
                          <h3 class="fw-black">Uploaded Images</h3>
                            <p class="text-secondary font-size-sm">
                                List of all uploaded media files from recently uploaded to oldest.
                            </p>
                      </div>
                      <div class="col-md-4 col-sm-12">
                      <form action="" id="search-media-form">
                          <div class="input-group">
                              <input type="text" class="form-control" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2" id="search-input" value="">
                              <button type="submit" class="input-group-text btn-primary" id="basic-addon2">Go</button>
                          </div>
                      </form>
                      </div>
                  </div>
                  <section class="mt-3">
                      <div id="edit-media-gallery" class="row row-cols-3 row-cols-sm-4 row-cols-xl-5 g-1 file-manager-grid photos-grid">
                          
                      </div>
                  </section>
                  <section id="section1" class="mt-4">
                      <nav aria-label="Page navigation example">
                      <ul class="pagination mb-0">
                          <li class="page-item" id="pagination-prev"><a class="page-link has-icon a-paginate" data-page="prev" href="#"><i class="fas fa-chevron-left"></i>&nbsp;&nbsp;Previous</a></li>
                          <li class="page-item" id="pagination-next"><a class="page-link has-icon a-paginate" data-page="next" href="#">Next&nbsp;&nbsp;<i class="fas fa-chevron-right"></i></a></li>
                      </ul>
                      </nav>
                  </section>
              </div>
              <div class="col-4 border-left-gray">
                  <h3 class="fw-black">Image details</h3>
                  <fieldset class="form-fieldset">
                      <legend>Attachment Details</legend>
                      <div class="row">
                          <div class="col-4 media" id="thumb-img"></div>
                          <div class="col-8 text-muted" id="thumb-info"></div>
                      </div>
                      <hr>
                      <form action="" id="update-attachment-details">
                          @csrf
                          <input type="hidden" id="media-id" name="id" value="">
                          <div class="form-floating">
                              <input type="text" class="form-control" id="media-alt_text" name="alt_text" placeholder="Enter alt text">
                              <label for="media-alt_text" class="form-label">Alt Text</label>
                          </div>
                          <div class="form-floating mt-2">
                              <input type="text" class="form-control" id="media-title" name="title" placeholder="Image title">
                              <label for="media-title" class="form-label">Image Title</label>
                          </div>

                          <div class="form-floating mt-2">
                              <textarea class="form-control custom-h-100" id="media-caption" name="caption" rows="5" placeholder="Image caption"></textarea>
                              <label for="media-caption" class="form-label">Image caption</label>
                          </div>

                          <div class="form-floating mt-2">
                              <textarea class="form-control h-120" id="media-description" name="description" rows="5" placeholder="Image description"></textarea>
                              <label for="media-description" class="form-label">Image description</label>
                          </div>
                          <div class="row mt-3">
                              <div class="col-12">
                                  <button class="btn has-icon btn-primary" type="submit">Save Changes</button>
                                  <button class="btn has-icon btn-danger" type="submit">Delete Image</button>
                                  <span id="media-loader" class="form-text ms-2 submit-edit hidden">
                                      <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                  </span>
                              </div>
                          </div>
                          
                      </form>
                  </fieldset>
              </div>
          </div>
      </div>
      <!-- /Main body -->

@endsection

@section('scripts')


  <script>
    var fetch_thumbnails_url = "{{ route('media.fetch_images', '1') }}";
      var image_tags_update_url = '{{ route("update_image_tags") }}';
    $(function () {

    fetch_images({});

        function fetch_images(data) {
            $.ajax({
                url: fetch_thumbnails_url,
                type: 'GET',
                data: data,
                success: function (result) {
                    console.log(result);
                    if (result["status"] == "000") {
                        $("#edit-media-gallery").html(result["items"]);
                        if (result["previous_page"] == 0) $("#pagination-prev").addClass("disabled");
                        else {
                            $("#library_page-prev").val(result["previous_page"]);
                            $("#pagination-prev").removeClass("disabled");
                        }
                        if (result["next_page"] == 0) $("#pagination-next").addClass("disabled");
                        else {
                            $("#library_page-next").val(result["next_page"]);
                            $("#pagination-next").removeClass("disabled");
                        }
                    }
                }
            });
        }

    })
  </script>
    
@endsection