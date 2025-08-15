@extends('theme.'.get_option('ak_theme').'.layout')

@section("styles")

@endsection

@section( 'main_body')

<section class="top-my-3">
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 mt-5 text-center">
                <h1>Page Not Found</h1>
                <h3 class="my-4 text-muted">The page you are looking for was not found.</h3>
                <a href="{{ route('/') }}" title="Back to homepage" class="btn btn-danger">Back to Home Page</a>
            </div>
        </div>
    </div>
</section>

<section class="top my-4">
    <div class="container default py-3">
        <div class="px-3 w-100">
            <div class="row my-4">
                <div class="col-12 col-md-8">
                    <div class="row">
                        <div class="col-12 mt-4">
                            <div class="d-flex">
                                <h4 class="cat-title">Latest Stories</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        @php
                            $latest_stories = latest_stories(12);
                        @endphp
                        
                        @foreach ($latest_stories as $post)
                            <div class="col-6 col-md-4 mb-3 post-red">
                                <div class="post">
                                    @if ($post->main_image)
                                        <a href="{{ get_permalink($post) }}" title="{!! $post->title !!}">
                                            <img class="w-100" src="{{ fetch_image($post->main_image->file_name, "md") }}" alt="{{ $post->main_image->alt_text }}">
                                        </a>
                                    @endif
                                    <p class="mt-2">
                                        <span><a href="{{ get_permalink($post) }}" title="{!! $post->title !!}">{!! $post->title !!}</a></span></br>
                                        <small><span class="text-muted mt-2">{{ get_time_ago($post) }}</span></small>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    @include('theme.'.get_option('ak_theme').'.templates.sidebar')
                </div>
            </div>
        </div>
    </div>
</section>
    
    
@endsection

@section("scripts")

@endsection