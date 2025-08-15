<fieldset class="form-fieldset">
    <legend>Attachment Details</legend>
    <p id="img-url"></p>
    <div class="row">
        <div class="col-4 media" id="thumb-img"></div>
        <div class="col-8 text-muted" id="thumb-info"></div>
    </div>
    <hr>
    <form action="" id="update-attachment-details">
        @csrf
        <input type="hidden" id="media-id" name="id" value="">
        <div class="form-floating">
            <input type="text" class="form-control" id="media-alt_text" name="alt_text" placeholder="Enter alt text" maxlength="250">
            <label for="media-alt_text" class="form-label">Alt Text</label>
        </div>
        <div class="form-floating mt-2">
            <input type="text" class="form-control" id="media-title" name="title" placeholder="Image title" maxlength="250">
            <label for="media-title" class="form-label">Image Title</label>
        </div>

        <div class="form-floating mt-2">
            <textarea class="form-control custom-h-100" id="media-caption" name="caption" rows="5" placeholder="Image caption" maxlength="250"></textarea>
            <label for="media-caption" class="form-label">Image caption</label>
        </div>

        <div class="form-floating mt-2">
            <textarea class="form-control h-120" id="media-description" name="description" rows="5" placeholder="Image description" maxlength="250"></textarea>
            <label for="media-description" class="form-label">Image description</label>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button class="btn has-icon btn-primary" type="submit">Save</button>
                <button id="delete-image" class="btn has-icon btn-danger d-none" type="button" data-bs-toggle="modal" data-bs-target="#deleteImageConfirmationModal">Delete Image</button>
                <span id="media-loader" class="form-text ms-2 submit-edit hidden">
                    <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                </span>
            </div>
        </div>
        
    </form>
</fieldset>

