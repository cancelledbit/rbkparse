<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Parsers\DataProviders\BaseProvider;

class ParseNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:parse {provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse news for specific provider';

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
        $provider = $this->argument('provider');
        $parser = BaseProvider::getParserInstance($provider);
        $parser->parseNews();
    }
}
