<?php

namespace App\Jobs;

use DOMDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupInnerImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $post = $this->post;
            $body = $post->body;
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($body);
            libxml_clear_errors();
            $dom->preserveWhiteSpace = false;
            $images = $dom->getElementsByTagName('img');

            foreach ($images as $image) {
                $file_name_array = explode("https://swalanyeti.co.ke/uploads/posts/", $image->getAttribute('src'));
                $file_name = $file_name_array[1];

                $body = str_replace("https://swalanyeti.co.ke/uploads/posts/", "https://swalanyeti.co.ke/storage/uploads/" . date('Y/m/', strtotime($post->created_at)), $body);
            }

            $post->body = $body;
            if (!$post->save()) echo $post->id . " NOT UPDATED</br>";
        } catch (\Throwable $th) {
            echo $post->id . " NOT UPDATED</br>";
        }
    }
}
