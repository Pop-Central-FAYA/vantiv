<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

/**
 * @todo Fix this media plan factory
 */
$factory->define(Vanguard\Models\MediaPlan::class, function (Faker $faker) {
    return [
        'id' => $faker->word,
        'campaign_name' => $faker->word,
        'client_id' => uniqid(),
        'brand_id' => uniqid(),
        'product_name' => $faker->word,
        'budget' => $faker->randomFloat(),
        'criteria_gender' => $faker->word,
        'media_type' => $faker->word,
        'criteria_lsm' => $faker->text,
        'criteria_social_class' => $faker->text,
        'criteria_region' => $faker->text,
        'criteria_state' => $faker->text,
        'criteria_age_groups' => $faker->text,
        'agency_commission' => $faker->randomFloat(),
        'start_date' => $faker->date(),
        'end_date' => $faker->date(),
        'planner_id' => $faker->word,
        'status' => $faker->word,
        'state_list' => $faker->text,
        'filters' => $faker->word,
        'gender' => $faker->word,
        'company_id' => uniqid(),
        'target_population' => $faker->randomNumber(),
        'population' => $faker->randomNumber(),
        'gross_impressions' => $faker->randomNumber(),
        'total_insertions' => $faker->randomNumber(),
        'net_reach' => $faker->randomNumber(),
        'net_media_cost' => $faker->randomFloat(),
        'cpm' => $faker->randomFloat(),
        'cpp' => $faker->randomFloat(),
        'avg_frequency' => $faker->randomFloat(),
        'total_grp' => $faker->randomFloat(),
    ];
});
