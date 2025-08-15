<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PullVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull videos from Youtube';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Pulling videos from Youtube!');
        $limit = 10;
        if (get_option('ak_youtube_channel_id') && get_option('ak_youtube_api_key')) {
            //https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=UC_zA9UIWE1fB-jfFk_DBSYw&maxResults=10&order=date&type=video&key=AIzaSyACREnYRZxc2ORXnfVEN_KLECLZ_e4yra0
            $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=" . get_option('ak_youtube_channel_id') . "&maxResults=" . $limit . "&order=date&type=video&key=" . get_option('ak_youtube_api_key');
            $this->info('URL: ' . $url);
            $response = json_decode(Http::get($url));
            if (isset($response->items)) {
                $videos = $response->items;
                $result = true;
                foreach ($videos as $item) {
                    $video = Video::where("video_id", "=", $item->id->videoId)->first();
                    if ($video) {
                        $video->description = $item->snippet->description;
                        $video->thumbnail_sm = $item->snippet->thumbnails->default->url;
                        $video->thumbnail_md = $item->snippet->thumbnails->medium->url;
                        $video->thumbnail_lg = $item->snippet->thumbnails->high->url;
                        $video->published_at = date('Y-m-d H:i:s', strtotime($item->snippet->publishedAt));
                        try {
                            $video->save();
                        } catch (\Throwable $th) {
                            $this->info('Error ocurred on: ' . $video->video_id);
                            $result = false;
                        }
                    } else {
                        $data = [
                            "title" => $item->snippet->title,
                            "source" => "Youtube",
                            "video_id" => $item->id->videoId,
                            "description" => $item->snippet->description,
                            "thumbnail_sm" => $item->snippet->thumbnails->default->url,
                            "thumbnail_md" => $item->snippet->thumbnails->medium->url,
                            "thumbnail_lg" => $item->snippet->thumbnails->high->url,
                            "live" => ($item->snippet->liveBroadcastContent == "live") ? "1" : "0",
                            "published_at" => date('Y-m-d H:i:s', strtotime($item->snippet->publishedAt)),
                        ];
                        $insert = Video::query()->create($data);
                        if (!$insert) {
                            $this->info('Error ocurred on: ' . $video->video_id);
                            $result = false;
                        }
                    }
                }
                if ($result) {
                    $this->info('SUCCESS! The process completed successfully');
                } else {
                    $this->info('ERROR! The process could not complete successfully');
                }
            } else {
                $this->info('ERROR! ' . $response->error->message);
            }
        } else {
            $this->info('ERROR! Missing either Youtube Channel ID or Youtube API Key');
        }
        return Command::SUCCESS;
    }
}
