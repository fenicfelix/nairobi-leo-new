<?php

if (! function_exists('load_sitemap_template')) {
    function load_sitemap_template($templateName, $data)
    {
        return response()->view('sitemaps.' . $templateName, $data)->header('Content-Type', 'text/xml');
    }
}
