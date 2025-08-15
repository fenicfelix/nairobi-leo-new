<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    "app_icon_light" => env('APP_ICON_LIGHT'),
    "app_icon_dark" => env('APP_ICON_DARK'),
    "app_favicon" => env('APP_FAVICON', 'favicon.ico'),
    "thumbnail_separator" => env('THUMBNAIL_SEPARATOR', '-'),
    'has_tv_section' => env('HAS_TV_SECTION', false),
    'ecommerce' => env('ENABLE_ECOMMERCE', false),
    'has_premium_feature' => env('HAS_PREMIUM_FEATURE', false),
];
