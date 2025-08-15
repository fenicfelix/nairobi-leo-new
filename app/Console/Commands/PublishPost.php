<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts';

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
        $this->info('Checking scheduled posts to publish!');
        $time = date('Y-m-d H:i:s');
        $posts = Post::isScheduled()->where("published_at", "<=", $time)->get();
        if ($posts) {
            $this->info("Publish Posts at : ".$time);
            foreach ($posts as $post) {
                $post->status_id = 3;
                $post->save();

                if ($post->homepage_ordering != "0") clear_homepage_ordering($post->id, $post->homepage_ordering);
                if ($post->is_breaking) clear_breaking_stories($post->id);
            }
        }
        $this->info('SUCCESS! Process completed');
        return Command::SUCCESS;
    }
}
