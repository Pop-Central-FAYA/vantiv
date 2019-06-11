<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Matrix\Exception;
use Vanguard\Models\Publisher;

class PublisherSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:publisher-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command collects the company id and update settings column on the publishers table';

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
        $company_id = $this->ask('Please enter the publisher/company id');
        $ad_pattern = $this->ask('Please enter your Ad Pattern');
        try{
            $settings = [
                'ad_pattern' => [
                    'length' => 4,
                    'unit' => 'breaks',
                    'interval' => '1 hour'
                ]
            ];
            $publisher = Publisher::where('company_id', $company_id)->first();
            $publisher->settings = json_encode($settings);
            $publisher->save();
            $this->info('Ad Pattern stored successfully');
        }catch (Exception $exception){
            $this->error($exception);
        }
    }
}
