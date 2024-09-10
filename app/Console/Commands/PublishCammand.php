<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishCammand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Publications';

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
        $now = date("Y-m-d");
        $data = DB::table('publications')->where('publish', 0)->whereRaw("date(published_at)>='$now'")->get();
        $data->each(function ($item) {
            DB::table('publications')->where('id', $item->id)->update(['publish' => 1]);
        });
        return 0;
    }
}
