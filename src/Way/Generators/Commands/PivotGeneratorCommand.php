<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PivotGeneratorCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:pivot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a pivot table';

    /**
     * Create a pivot table migration
     */
    public function fire()
    {
        list($tableOne, $tableTwo) = $this->sortDesiredTables();

        $this->call('generate:migration', [
            'migrationName' => "create_{$tableOne}_{$tableTwo}_table",
            '--fields' => $this->getMigrationFields($tableOne, $tableTwo)
        ]);
    }

    /**
     * Sort the provided pivot tables in alphabetical order
     *
     * @return array
     */
    public function sortDesiredTables()
    {
        $tables = Arr::except(array_map('Illuminate\Support\Str::singular', $this->argument()), 'command');

        sort($tables);

        return $tables;
    }

    /**
     * Get the fields for the pivot migration.
     *
     * @param $tableOne
     * @param $tableTwo
     * @return array
     */
    public function getMigrationFields($tableOne, $tableTwo)
    {
        return implode(', ', [
            "{$tableOne}_id:integer:unsigned:index",
            "{$tableOne}_id:foreign:references('id'):on('" . Str::plural($tableOne) . "'):onDelete('cascade')",
            "{$tableTwo}_id:integer:unsigned:index",
            "{$tableTwo}_id:foreign:references('id'):on('" . Str::plural($tableTwo) . "'):onDelete('cascade')",
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['tableOne', InputArgument::REQUIRED, 'Name of the first table'],
            ['tableTwo', InputArgument::REQUIRED, 'Name of the second table']
        ];
    }

}
