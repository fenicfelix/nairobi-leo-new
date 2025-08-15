@extends('backend.layouts.backend')

@section('styles')
<link rel="stylesheet" href="{{ asset('theme/backend/css/bootstrap-tagsinput.css') }}">
    
@endsection

@section( 'main_body')

    <!-- Main body -->
      <div id="main-body">
        <nav aria-label="breadcrumb" id="main-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
          </ol>
        </nav>

        <div class="row g-4">
          <div class="col-md-4 col-lg-3 d-none d-md-block">
            <div class="card">
              <div class="card-body navbar-light">
                <div class="navbar-nav nav">
                  <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3 active" href="#general" role="tab" aria-selected="true">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                    General
                  </a>
                  <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3" href="#social" role="tab" aria-selected="false">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                    Social Media
                  </a>
                  <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3" href="#advanced" role="tab" aria-selected="false">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                    Advanced
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-lg-9">
            <div class="card">
              <div class="card-body tab-content">
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                  <h3 class="fw-black">General Settings</h3>
                  <p class="small text-secondary mb-4">
                    Paste in any codes that you would like to be pulled to the Header and/or Footer sections
                  </p>
                  <form class="ajax-form-settings" method="POST">
                    @csrf
                    <div class="row">
                        <label for="add-ak_app_title" class="col-sm-2 col-form-label"> App Title</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_app_title" name="ak_app_title" placeholder="App Name" value="{{ get_option('ak_app_title') }}">
                            <p class="text-muted">
                                <small>Enter the name of the application.</small>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label for="add-ak_app_url" class="col-sm-2 col-form-label"> App URL</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_app_url" name="ak_app_url" placeholder="App URL" value="{{ get_option('ak_app_url') }}">
                            <p class="text-muted">
                                <small>Paste app url without trailing slash '/'</small>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label for="add-ak_theme" class="col-sm-2 col-form-label"> Theme</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_theme" name="ak_theme" placeholder="Theme" value="{{ get_option('ak_theme') }}">
                            <p class="text-muted">
                                <small>Enter the name of the theme being used</small>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label for="add-ak_sticky_posts" class="col-sm-2 col-form-label"> Sticky posts</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_sticky_posts" name="ak_sticky_posts" placeholder="Sticky posts" value="{{ get_option('ak_sticky_posts') ?? 0 }}">
                            <p class="text-muted">
                                <small>How many posts would be made sticky on the homepage?</small>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label for="add-ak_load_more_limit" class="col-sm-2 col-form-label"> Posts load more limit</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_load_more_limit" name="ak_load_more_limit" placeholder="Load More limit" value="{{ get_option('ak_load_more_limit') ?? 0 }}">
                            <p class="text-muted">
                                <small>How many posts should be loaded when users click Load More on the categoy page?</small>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="add-ak_header_js" class="col-sm-2 col-form-label"> Header Codes</label>
                        <div class="col-sm-10">
                            <textarea class="form-control h-120" data-task="add" data-type="description" id="add-ak_header_js" name="ak_header_js" rows="5" placeholder="Header Codes">{{ get_option('ak_header_js') }}</textarea>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="add-ak_footer_js" class="col-sm-2 col-form-label"> Footer Codes</label>
                        <div class="col-sm-10">
                            <textarea class="form-control h-120" data-task="add" data-type="description" id="add-ak_footer_js" name="ak_footer_js" rows="5" placeholder="Footer Codes">{{ get_option('ak_footer_js') }}</textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Save Changes">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="settings-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                  </form>
                </div>
                <div class="tab-pane fade" id="social" role="tabpanel">
                  <h3 class="fw-black">Social Media Settings</h3>
                  <p class="small text-secondary mb-4">
                    {{-- This information will be displayed publicy so be careful what you share. --}}
                  </p>
                  <form class="ajax-form-settings" method="POST">
                    @csrf
                    <div class="row">
                        <label for="soc_facebook" class="col-sm-2 col-form-label"> <i class="fab fa-facebook-f facebook" aria-hidden="true"></i> Facebook</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_facebook" name="ak_facebook" placeholder="Facebook URL" value="{{ get_option('ak_facebook') }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_twitter" class="col-sm-2 col-form-label"> <i class="fab fa-twitter twitter" aria-hidden="true"></i> Twitter</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_twitter" name="ak_twitter" value="{{ get_option('ak_twitter') }}" placeholder="Twitter URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_twitter_username" class="col-sm-2 col-form-label"> <i class="fab fa-twitter twitter" aria-hidden="true"></i> Twitter Username</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_twitter_username" name="ak_twitter_username" value="{{ get_option('ak_twitter_username') }}" placeholder="Twitter Username">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_youtube" class="col-sm-2 col-form-label"> <i class="fab fa-youtube youtube" aria-hidden="true"></i> Youtube</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_youtube" name="ak_youtube" value="{{ get_option('ak_youtube') }}" placeholder="Youtube URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_instagram" class="col-sm-2 col-form-label"> <i class="fab fa-instagram instagram" aria-hidden="true"></i> Instagram</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_instagram" name="ak_instagram" value="{{ get_option('ak_instagram') }}" placeholder="Instagram URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_telegram" class="col-sm-2 col-form-label"> <i class="fab fa-telegram-plane telegram" aria-hidden="true"></i> Telegram</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_telegram" name="ak_telegram" value="{{ get_option('ak_telegram') }}" placeholder="Telegram URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_linkedin" class="col-sm-2 col-form-label"> <i class="fab fa-linkedin linkedin" aria-hidden="true"></i> Linkedin</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_linkedin" name="ak_linkedin" value="{{ get_option('ak_linkedin') }}" placeholder="Linkedin URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_tiktok" class="col-sm-2 col-form-label"> <i class="fab fa-tiktok tiktok" aria-hidden="true"></i> Tiktok</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_tiktok" name="ak_tiktok" value="{{ get_option('ak_tiktok') }}" placeholder="Tiktok URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_rss_feed" class="col-sm-2 col-form-label"> <i class="fas fa-rss rss" aria-hidden="true"></i> RSS</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_rss_feed" name="ak_rss_feed" value="{{ get_option('ak_rss_feed') }}" placeholder="RSS Feeds URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_email" class="col-sm-2 col-form-label"> <i class="fa fa-envelope email" aria-hidden="true"></i> Email Address</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_email" name="ak_email" value="{{ get_option('ak_email') }}" placeholder="email URL">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="soc_phone_number" class="col-sm-2 col-form-label"> <i class="fa fa-phone phone" aria-hidden="true"></i> Phone Number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="soc_phone_number" name="ak_phone_number" value="{{ get_option('ak_phone_number') }}" placeholder="Phone Number">
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Save Changes">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="settings-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                  </form>
                </div>
                <div class="tab-pane fade" id="advanced" role="tabpanel">
                  <h3 class="fw-black">Advanced Settings</h3>
                  <p class="small text-secondary mb-4">
                    This is an advanced level settings area. Please handle the settings with care.
                  </p>
                  <form class="ajax-form-settings" method="POST">
                    @csrf
                    <div class="row">
                        <label for="add-ak_thumbnail_sizes" class="col-sm-2 col-form-label"> Thumnail Sizes </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_thumbnail_sizes" name="ak_thumbnail_sizes" placeholder="Allowed thumbnail sizes" data-role="tagsinput" value="{{ get_option('ak_thumbnail_sizes') }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="add-ak_alt_image" class="col-sm-2 col-form-label"> Alt Image </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_alt_image" name="ak_alt_image" placeholder="Alternative image" value="{{ get_option('ak_alt_image') }}">
                            <p class="text-muted">
                                <small>Paste path to the alyernative image</small>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label for="add-ak_breaking_news_validity" class="col-sm-2 col-form-label"> Breaking validity</label>
                        <div class="col-sm-10">
                            <input type="number" min="0" step="5" class="form-control" id="ak_breaking_news_validity" name="ak_breaking_news_validity" placeholder="Breaking News Validity" value="{{ get_option('ak_breaking_news_validity') }}">
                            <p class="text-muted">
                                <small>Time in minutes.</small>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label for="add-ak_trending_validity" class="col-sm-2 col-form-label"> Trending Validity</label>
                        <div class="col-sm-10">
                            <input type="number" min="0" step="5" class="form-control" id="ak_trending_validity" name="ak_trending_validity" placeholder="Trending Validity" value="{{ get_option('ak_trending_validity') }}">
                            <p class="text-muted">
                                <small>Tracking period fr breaking news in hours.</small>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <label for="add-ak_facebook_id" class="col-sm-2 col-form-label"> Facebook App ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_facebook_id" name="ak_facebook_id" placeholder="Facebook App ID" value="{{ get_option('ak_facebook_id') }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="add-ak_facebook_pages" class="col-sm-2 col-form-label"> Facebook Pages</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_facebook_pages" name="ak_facebook_pages" placeholder="One Signal App ID" value="{{ get_option('ak_facebook_pages') }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="add-ak_onesignal_appid" class="col-sm-2 col-form-label"> OneSignal App ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_onesignal_appid" name="ak_onesignal_appid" placeholder="One Signal App ID" value="{{ get_option('ak_onesignal_appid') }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="add-ak_onesignal_key" class="col-sm-2 col-form-label"> OneSignal Key</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_onesignal_key" name="ak_onesignal_key" placeholder="OneSignal Key" value="{{ get_option('ak_onesignal_key') }}">
                        </div>
                    </div>
                    @env('HAS_TV_SECTION')
                        <div class="row mt-4">
                            <label for="add-ak_youtube_channel_id" class="col-sm-2 col-form-label"> YouTube Channel ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="ak_youtube_channel_id" name="ak_youtube_channel_id" placeholder="YouTube Channel ID" value="{{ get_option('ak_youtube_channel_id') }}">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <label for="add-ak_youtube_api_key" class="col-sm-2 col-form-label"> YouTube API Key</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="ak_youtube_api_key" name="ak_youtube_api_key" placeholder="YouTube API Key" value="{{ get_option('ak_youtube_api_key') }}">
                            </div>
                        </div>
                    @endenv
                    <div class="row mt-4">
                        <label for="add-ak_default_password" class="col-sm-2 col-form-label"> Default Password</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_default_password" name="ak_default_password" placeholder="Default Password" value="{{ get_option('ak_default_password') }}">
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-4">
                        <label for="add-ak_seo_keywords" class="col-sm-2 col-form-label"> SEO Keywords </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ak_seo_keywords" name="ak_seo_keywords" placeholder="Default SEO Keywords" data-role="tagsinput" value="{{ get_option('ak_seo_keywords') }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <label for="add-ak_seo_description" class="col-sm-2 col-form-label"> SEO Description </label>
                        <div class="col-sm-10">
                            <textarea class="form-control h-120" id="add-ak_seo_description" name="ak_seo_description" rows="5" placeholder="Enter default SEO Description">{{ get_option('ak_seo_description') }}</textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Save Changes">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="settings-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /Main body -->

@endsection

@section('scripts')

<script>
  let submit_add_url = '{{ route("store_options") }}';
</script>
<script src="{{ asset('theme/backend/js/bootstrap-tagsinput.js') }}"></script>
    
@endsection