<div class="row">
    <div class="col-12">
        <section id="section6" class="mt-3">
            <p class="text-secondary font-size-sm">
                Select a file to set as a featured image. Max size 1MB
            </p>
            <form action="#" id="file-upload-form" enctype="multipart/form-data" >
                @csrf
                <div class="row">
                    <div class="col-6">
                        <input type="file" class="form-control" name="file" id="upload-file" required>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary" id="btn-upload">Upload</button>
                       <span id="upload-loader" class="form-text ms-2 submit-add hidden">
                            <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                        </span>
                    </div>
                </div>
            </form>
            <hr>
            <div class="row">
                <div class="col-8">
                    <div class="mt-2 w-100">
                        <div class="image-area  mb-2"><img id="uploaded-image" class="selected-image post-thumbnail w-100" src="{{ $image = fetch_image(NULL, "lg") }}" class="img-fluid" alt="Responsive image"></div>
                    </div>
                </div>
                <div class="col-4 mt-5 text-muted" id="upload-image-details">
                    <span id="upload-results"></span>
                    <a class="upload-trash hidden" href="#">Delete Permanently</a>
                </div>
            </div>
        </section>
    </div>
</div>