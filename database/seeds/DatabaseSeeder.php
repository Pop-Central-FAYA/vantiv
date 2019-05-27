<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompanyTypeSeeder::class);
        $this->call(RateCardPriority::class);
        $this->call(CriteriasTableSeeder::class);
        $this->call(MpsTableSeeder::class);
        $this->call(StatePopulationsTableSeeder::class);
        $this->call(MpsAudiencesTableSeeder::class);
        $this->call(MpsAudienceProgramActivitiesTableSeeder::class);
        $this->call(StationsTableSeeder::class);
        $this->call(PublisherFeatureSeeder::class);
        $this->call(PublisherRoleSeeder::class);
        /**
         * These section were commented out because they are only used
         * to generate fake data for testing and demo purposes. In the future,
         * the seeds will actually be modified to generate actual data.
         * If anyone is getting errors on the dashboards (campaign management and
         * inventory management), uncomment the piece and run seeder. This should
         * fix the error.
         */
        /*$this->call(FakeTimeBeltTransactionSeeder::class);
        $this->call(FakeTimeBeltRevenueSeeder::class);
        $this->call(PublisherTableSeeder::class);*/
    }
}
