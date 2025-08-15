
<input type="hidden" id="library_page" value="">
<input type="hidden" id="library_page-prev" value="">
<input type="hidden" id="library_page-next" value="">
<div class="row">
    <div class="col-12">
        <div class="row mt-4">
            <div class="col-md-8 col-sm-12">
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
            <div id="add-media-gallery" class="row row-cols-3 row-cols-sm-4 row-cols-xl-5 g-1 file-manager-grid photos-grid">
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
</div>