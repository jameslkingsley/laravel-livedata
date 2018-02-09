<?php

namespace Kingsley\LiveData\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PullDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livedata:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls the live database data into the local.';

    /**
     * Local database connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $local;

    /**
     * Live database connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $live;

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
     * @return void
     */
    public function handle()
    {
        $this->live = $this->database(config('livedata.live'));
        $this->local = $this->database(
            is_null(config('livedata.local'))
            ? config('database.default')
            : config('livedata.local')
        );

        $this->local->select('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($this->tables() as $name => $table) {
            $this->truncate($name);
            $this->pull($name, $table->get());
        }

        $this->local->select('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Gets the live database connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public function database(string $connection)
    {
        return DB::connection($connection);
    }

    /**
     * Gets the live table objects (does not include tables not on local).
     *
     * @return array
     */
    public function tables()
    {
        $tables = [];

        foreach (array_map('reset', $this->local->select('SHOW TABLES')) as $table) {
            $tables[$table] = $this->live->table($table);
        }

        return $tables;
    }

    /**
     * Drops all rows on the given local table.
     *
     * @return void
     */
    public function truncate($table)
    {
        $this->local->table($table)->truncate();
    }

    /**
     * Pulls the rows from the given table to local.
     *
     * @return void
     */
    public function pull($table, $rows)
    {
        foreach ($rows as $row) {
            $this->local->table($table)->insert((array) $row);
        }
    }
}
