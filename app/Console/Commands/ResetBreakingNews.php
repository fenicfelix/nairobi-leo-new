<?php

namespace App\Console\Commands;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetBreakingNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:breaking-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all breaking news that have elapsed allowed time';

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
        $date = date('Y-m-d H:i:s', strtotime("-30 minutes"));
        $posts = Post::where("is_breaking", "=", "1")->where("published_at", "<", $date)->get();
        if ($posts) {
            foreach ($posts as $post) {
                $post->is_breaking = "0";
                try {
                    $post->save();
                } catch (\Throwable $th) {
                    info("Unable to reset breaking news for ID " . $post->id);
                }
            }
        }

        return Command::SUCCESS;
    }
}
