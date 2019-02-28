<?php

use Illuminate\Database\Seeder;

class MonthsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $begin = new DateTime( '2019-01-01' );
        $end = new DateTime( '2019-12-01' );
        $end = $end->modify( '+1 month' );
        $interval = DateInterval::createFromDateString('1 month');

        $period = new DatePeriod($begin, $interval, $end);

        foreach($period as $dt) {
            \Vanguard\Models\Month::create([
                'month_name' => $dt->format( "F" )
            ]);
        }
    }
}
