<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ac extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ac';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // get multiple controllers names from console and create them at once
        $controllers = $this->ask('Enter controllers names separated by comma');
        $answer = $this->confirm('Do you want to create models for these controllers?');
        $controllers = explode(',', $controllers);
        foreach ($controllers as $controller) {
        //    execute artisan make:controller command
            $this->call('make:controller', [
                'name' => 'Api/'.$controller.'Controller',
                '--api' => true,
            ]);
            $this->call('make:model', [
                'name' => $controller,
            ]);

            $this->info('DONE');
        }
        $this->info('Controllers created successfully');
    }
}
