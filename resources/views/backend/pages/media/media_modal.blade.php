<div class="modal fade" id="modalSetFeaturedImage" tabindex="-1" aria-labelledby="modalSetFeaturedImageLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-custom-dark text-white shadow-none">
                <h6 class="modal-title text-light" id="xlModalLabel">Set Featured Image</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="col-12 col-md-8">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-link a-media-tab" id="nav-upload_a_file-tab" data-bs-toggle="tab" href="#nav-upload_a_file" role="tab" aria-controls="nav-upload_a_file" aria-selected="true"><b>Upload a File</b></a>
                            <a class="nav-link a-media-tab active" id="nav-media_library-tab" data-bs-toggle="tab" href="#nav-media_library" role="tab" aria-controls="nav-media_library" aria-selected="false"><b>Media Library</b></a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade" id="nav-upload_a_file" role="tabpanel" aria-labelledby="nav-upload_a_file-tab">
                        @include('backend.pages.media.upload_files')
                    </div>
                    <div class="tab-pane fade show active" id="nav-media_library" role="tabpanel" aria-labelledby="nav-media_library-tab">
                        @include('backend.pages.media.media_library')
                    </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mt-5 border-left-gray">
                    @include('backend.pages.media.seo')
                </div>
                </div>
            </div>
        </div>
    </div>
</div>