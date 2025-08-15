<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class LinkTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link specified theme';

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
        $this->info('Linking frontend theme!');
        $theme = get_option('ak_theme');
        $link = base_path() . '/public/theme/frontend';
        $path = base_path() . '/resources/views/theme/' . $theme;
        if (file_exists($link)) shell_exec('rm ' . $link);
        shell_exec('ln -s ' . $path . '/ ' . $link);

        $this->info('SUCCESS! The process completed successfully');
        return Command::SUCCESS;
    }
}
