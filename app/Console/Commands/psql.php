<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class psql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psql:createdb {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new psql database schema based on the database config file';

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
        try {
            $schemaName = $this->argument('name');

            $query = "CREATE DATABASE $schemaName;";

            DB::statement($query);

            config(["database.connections.psql.database" => $schemaName]);

            DB::reconnect();
        } catch (QueryException $e) {
            $this->error(sprintf('Failed to create %s database, %s', $this->argument('name'), $e->getMessage()));
        }
    }
}