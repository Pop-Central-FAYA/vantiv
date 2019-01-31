<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Models\ParentCompany;

class CreateParentCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:parent-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This artisan commands create parent company';

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
        $name = $this->ask('What is the name of the parent company?');
        try{
            ParentCompany::create([
                'id' => uniqid(),
                'name' => $name
            ]);
            $this->info($name.' was added successfully');
        }catch(\Exception $exception){
            $this->error($exception->getMessage().' There was an error adding this parent company');
        }
    }
}
