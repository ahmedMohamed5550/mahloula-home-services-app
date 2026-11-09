<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        if ($app['config']->get('database.default') === 'sqlite') {
            \Illuminate\Support\Facades\DB::connection('sqlite')->setSchemaGrammar(
                new class extends \Illuminate\Database\Schema\Grammars\SQLiteGrammar {
                    public function compileChange(\Illuminate\Database\Schema\Blueprint $blueprint, \Illuminate\Support\Fluent $command, \Illuminate\Database\Connection $connection)
                    {
                        return [];
                    }

                    protected function modifyNullable(\Illuminate\Database\Schema\Blueprint $blueprint, \Illuminate\Support\Fluent $column)
                    {
                        if ($column->name === 'phone') {
                            return ' null';
                        }
                        return $column->nullable ? ' null' : ' not null';
                    }
                }
            );
        }

        return $app;
    }
}
