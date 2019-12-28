<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Store;
use Carbon\Carbon;

class TtlCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ttl_check:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TTL Check';

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
     * @return mixed
     */
    public function handle()
    {
        $this->line('==================');
        $this->line('Running my job at ' . Carbon::now());
        Store::where('ttl', '<', Carbon::now()->subMinutes(5)->toDateTimeString())->delete();
    }
}
