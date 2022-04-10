<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Contracts\Bus\Dispatcher;

use App\Jobs\RetrieveBooksJob;

class RetrieveBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrieve:books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command used to retrieve books from the API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        logger()->info('Retrieving books from the API');

        RetrieveBooksJob::dispatch();

        // app(Dispatcher::class)->dispatch((new RetrieveBooksJob())->onQueue('retrieve-books'));

        logger()->info('Job pushed for retrieving books from the API');
    }
}
