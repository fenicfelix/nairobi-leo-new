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
                  <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3" href="#homepage" role="tab" aria-selected="false">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                    Homepage
                  </a>
                  <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3" href="#default" role="tab" aria-selected="false">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                    Default
                  </a>
                  @if ($categories)
                      @foreach ($categories as $category)
                          <a data-bs-toggle="pill" class="nav-link d-flex align-items-center gap-3" href="#{{$category->slug}}-category" role="tab" aria-selected="false">
                            <i class="fa fa-circle" aria-hidden="true"></i>
                            {{ $category->name }}
                          </a>
                      @endforeach
                  @endif
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-lg-9">
            <div class="card">
              <div class="card-body tab-content">
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                  <h3 class="fw-black">General</h3>
                  <p class="small text-secondary mb-4">
                    Please name your ad slots before you fill in these sections
                  </p>
                  <form class="ajax-form-settings" method="POST">
                    @csrf
                    <div class="row mt-3">
                        <label for="ad-ak_homepage_ads" class="col-sm-2 col-form-label"> Homepage ads</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ad-ak_homepage_ads" name="ak_homepage_ads" value="{{ get_option('ak_homepage_ads') }}" data-role="tagsinput" placeholder="Homepage ads" />
                            <p class="text-muted">
                              <small>Enter the name of the ad slots for the home page</small>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <label for="ad-ak_category_ads" class="col-sm-2 col-form-label"> Category ads</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ad-ak_category_ads" name="ak_category_ads" value="{{ get_option('ak_category_ads') }}" data-role="tagsinput" placeholder="Category pages ads" />
                            <p class="text-muted">
                              <small>Enter the name of the ad slots for the category pages</small>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-3">
                      <label for="ad-ak_inner_page_ads" class="col-sm-2 col-form-label"> Category inner page ads</label>
                      <div class="col-sm-10">
                          <input type="text" class="form-control" id="ad-ak_inner_page_ads" name="ak_inner_page_ads" value="{{ get_option('ak_inner_page_ads') }}" data-role="tagsinput"  placeholder="Category inner pages ads" />
                          <p class="text-muted">
                            <small>Enter the name of the ad slots for the category inner pages</small>
                          </p>
                      </div>
                    </div>
                    <div class="row">
                        <label for="ad-ak_default_ads" class="col-sm-2 col-form-label"> Default ads</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ad-ak_default_ads" name="ak_default_ads" value="{{ get_option('ak_default_ads') }}" data-role="tagsinput" placeholder="Default ads" />
                            <p class="text-muted">
                              <small>Enter the name of the ad slots for default ads</small>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="settings-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                  </form>
                </div>
                <div class="tab-pane fade" id="homepage" role="tabpanel">
                  <h3 class="fw-black">Homepage ads</h3>
                  <p class="small text-secondary mb-4">
                    {{-- This information will be displayed publicy so be careful what you share. --}}
                  </p>
                  <form class="ajax-form-settings" method="POST">
                    @csrf
                    <div class="row">
                        <label for="add-ak_homepage_header_js" class="col-sm-2 col-form-label"> Header JS Codes</label>
                        <div class="col-sm-10">
                            <textarea class="form-control h-120" data-task="add" data-type="description" id="add-ak_homepage_header_js" name="ak_homepage_header_js" rows="5" placeholder="Homepage header code">{{ get_option('ak_homepage_header_js') }}</textarea>
                        </div>
                    </div>
                    @php
                      $homepage_ads = explode(",", get_option('ak_homepage_ads'));
                    @endphp
                    @forelse ($homepage_ads as $ad)
                    @php
                      $ad_key = "ak_homepage_".str_replace(" ", "_", strtolower($ad));
                    @endphp
                        <div class="row mt-3">
                          <label for="add-{{$ad_key}}" class="col-sm-2 col-form-label"> {{ ucfirst(strtolower($ad)) }}</label>
                          <div class="col-sm-10">
                              <textarea class="form-control h-120" data-task="add" data-type="description" id="add-{{$ad_key}}" name="{{$ad_key}}" rows="5" placeholder="Homepage {{ ucfirst(strtolower($ad)) }}">{{ get_option($ad_key) }}</textarea>
                          </div>
                      </div>
                    @empty
                        
                    @endforelse
                    <hr>
                    <div class="row mt-3">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="settings-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                  </form>
                </div>

                <div class="tab-pane fade" id="default" role="tabpanel">
                  <h3 class="fw-black">Default Ads</h3>
                  <p class="small text-secondary mb-4">
                    Use Google Adsense codes here
                  </p>
                  <form class="ajax-form-settings" method="POST">
                    @csrf
                    <div class="row">
                        <label for="add-ak_default_header_js" class="col-sm-2 col-form-label"> Header JS Codes</label>
                        <div class="col-sm-10">
                            <textarea class="form-control h-120" data-task="add" data-type="description" id="add-ak_default_header_js" name="ak_default_header_js" rows="5" placeholder="Default header code">{{ get_option('ak_default_header_js') }}</textarea>
                        </div>
                    </div>
                    @php
                        $default_ads = explode(",", get_option('ak_default_ads'));
                    @endphp
                    @foreach ($default_ads as $item)
                      @php
                          $ad_key = "ak_default_".str_replace(" ", "_", strtolower($item));
                      @endphp
                        <div class="row mt-3">
                          <label for="add-{{$ad_key}}" class="col-sm-2 col-form-label"> {{ $item }}</label>
                          <div class="col-sm-10">
                              <textarea class="form-control h-120" data-task="add" data-type="description" id="add-{{$ad_key}}" name="{{$ad_key}}" rows="5" placeholder="Default {{$item}}">{{ get_option($ad_key) }}</textarea>
                          </div>
                      </div>
                    @endforeach
                    <hr>
                    <div class="row mt-3">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-light" value="Reset">
                            <span id="settings-loader" class="form-text ms-2 submit-edit hidden">
                                <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                            </span>
                        </div>
                    </div>
                  </form>
                </div>

                @if ($categories)
                      @foreach ($categories as $category)
                      @php
                          $header_key = $category->slug;
                      @endphp
                        <div class="tab-pane fade" id="{{$category->slug}}-category" role="tabpanel">
                          <h3 class="fw-black">{{ $category->name." Ads" }}</h3>
                          <p class="small text-secondary mb-4">
                            {{-- This information will be displayed publicy so be careful what you share. --}}
                          </p>
                          <form class="ajax-form-settings" method="POST">
                            @csrf
                            <fieldset>
                              <legend>Category landing page</legend>
                              <div class="row">
                                <label for="add-ak_{{$header_key}}_header_js" class="col-sm-2 col-form-label"> Header JS Codes</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control h-120" data-task="add" data-type="description" id="add-ak_{{$header_key}}_header_js" name="ak_{{$header_key}}_header_js" rows="5" placeholder="Heade JS code">{{ get_option('ak_'.$header_key.'_header_js') }}</textarea>
                                </div>
                              </div>
                              @php
                                  $category_ads = explode(",", get_option('ak_category_ads'));
                              @endphp
                              @foreach ($category_ads as $item)
                                @php
                                  $ad_key = "ak_".$header_key."_".str_replace(" ", "_", strtolower($item));
                                @endphp
                                  <div class="row mt-3">
                                    <label for="add-{{$ad_key}}" class="col-sm-2 col-form-label"> {{ $item }}</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control h-120" data-task="add" data-type="description" id="add-{{$ad_key}}" name="{{$ad_key}}" rows="5" placeholder="Default {{$item}}">{{ get_option($ad_key) }}</textarea>
                                    </div>
                                  </div>
                              @endforeach
                            </fieldset>
                            <fieldset class="mt-4">
                              <legend>Category inner page</legend>
                              @php
                                  $ad_key = $header_key."_single";
                              @endphp
                              <div class="row">
                                <label for="add-ak_{{$ad_key}}_header_js" class="col-sm-2 col-form-label"> Header JS Codes</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control h-120" data-task="add" data-type="description" id="add-ak_{{ $ad_key }}_header_js" name="ak_{{ $ad_key }}_header_js" rows="5" placeholder="Homepage header code">{{ get_option('ak_'.$ad_key.'_header_js') }}</textarea>
                                </div>
                              </div>
                              @php
                                  $category_inner_ads = explode(",", get_option('ak_inner_page_ads'));
                              @endphp
                              @foreach ($category_inner_ads as $item)
                                @php
                                  $ad_key = "ak_".$header_key."_single_".str_replace(" ", "_", strtolower($item));
                                @endphp
                                  <div class="row mt-3">
                                    <label for="add-{{$ad_key}}" class="col-sm-2 col-form-label"> {{ $item }}</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control h-120" data-task="add" data-type="description" id="add-{{$ad_key}}" name="{{$ad_key}}" rows="5" placeholder="Default {{$item}}">{{ get_option($ad_key) }}</textarea>
                                    </div>
                                  </div>
                              @endforeach
                            </fieldset>
                            <hr>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                    <input type="reset" class="btn btn-light" value="Reset">
                                    <span id="settings-loader" class="form-text ms-2 submit-edit hidden">
                                        <i class="fas fa-spinner fa-spin"></i> <small>Please wait...</small>
                                    </span>
                                </div>
                            </div>
                          </form>
                        </div>
                      @endforeach
                  @endif

              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /Main body -->

@endsection

@section('scripts')
<script src="{{ asset('theme/backend/js/bootstrap-tagsinput.js') }}"></script>

<script>
  let submit_add_url = '{{ route("store_options") }}';
</script>
    
@endsection