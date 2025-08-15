<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as Img;

class GenerateThumbnails implements ShouldQueue
{
    public $image;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($image)
    {
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $image = $this->image;
        $file_name = $image->file_name;
        $dest_file_name = $image->dest_file_name;

        $src = public_path('storage/dumps/uploads/oldposts/' . $file_name);
        $dest = public_path('storage/new/' . $dest_file_name);

        $base_path = pathinfo($src, PATHINFO_DIRNAME);
        $filename = pathinfo($src, PATHINFO_FILENAME); //get filename without extension
        $extension = pathinfo($src, PATHINFO_EXTENSION); //get filename without extension

        $paths = explode("/", $image->dest_file_name);
        $path = $paths[1] . "-" . $paths[2];

        try {
            $copy = File::copy($src, $dest);

            if (!$copy) echo "File not copied</br>";

            $thumbnail_sizes = get_thumbnail_sizes();
            if ($thumbnail_sizes) {
                foreach ($thumbnail_sizes as $key => $value) {
                    $name_array = explode(".", $image->file_name);
                    $thumbnail = $filename . '-' . $key . '.' . $extension;

                    $dest = public_path('storage/new/uploads/' . str_replace("-", "/", $path) . "/" . $thumbnail);
                    $copy = File::copy($src, $dest);

                    try {
                        $this->createThumbnail($dest, $value, $value);
                    } catch (Exception $e) {
                        info($e->getMessage());
                    }
                }
            }



            echo "File COPIED</br>";
        } catch (\Throwable $th) {
            echo $th->getMessage() . " - Error Ocurred</br>";
        }
    }

    public function createThumbnail($path, $width, $height)
    {
        $img = Img::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }
}
