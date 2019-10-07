<?php

namespace Tests\Feature\Dsp\Reach;

class GetReachTest extends ReachTestCase
{
    protected $route_name = 'agency.media_plan.create-ratings';

    public function test_unauthenticated_user_cannot_access_reach_get_route()
    {
        $params = ["plan_id" => uniqid()];
        $response = $this->getJson(route($this->route_name, $params));
        $response->assertStatus(401);
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $params = ["plan_id" => uniqid()];
        $response = $this->getResponse($user, $params);
        $response->assertStatus(403);
    }

    public function test_attempting_to_get_reach_for_non_existent_plan_returns_404()
    {
        $user = $this->setupUserWithPermissions();

        $params = [
            "plan_id" => uniqid(),
            "day" => "all",
            "day_part" => "all",
            "state" => "all",
            "station_type" => "all"
        ];
        $response = $this->getResponse($user, $params);

        $response->assertStatus(404);
    }

    public function test_403_returned_if_attempting_to_get_reach_for_plan_user_does_not_have_access_to()
    {
        $user = $this->setupUserWithPermissions();
        $media_plan = $this->setupMediaPlan($user);

        $another_user = $this->setupAuthUser();
        $another_media_plan = $this->setupMediaPlan($another_user);

        $params = [
            "plan_id" => $another_media_plan->id,
            "day" => "all",
            "day_part" => "all",
            "state" => "all",
            "station_type" => "all"
        ];
        $response = $this->getResponse($user, $params);

        $response->assertStatus(403);
    }

    /**
     * @dataProvider invalidGetReachDataProvider
     */
    public function test_attempting_to_get_reach_with_invalid_parameters_returns_422($params)
    {
        $user = $this->setupUserWithPermissions();
        $media_plan = $this->setupMediaPlan($user);

        $params["plan_id"] = $media_plan->id;
        $response = $this->getResponse($user, $params);

        $response->assertStatus(422);
    }

    public static function invalidGetReachDataProvider()
    {
        return [
            [[]], //empty filters
            [["day_part" => "Supper Time", "state" => "Lagos", "day" => "All", "station_type" => "All"]], //invalid day part
            [["day_part" => "All", "state" => "Invalid State", "day" => "All", "station_type" => "All"]], //invalid state
            [["day_part" => "All", "state" => "Invalid State", "day" => "", "station_type" => "All"]], //invalid day
            [["day_part" => "All", "state" => "Invalid State", "day" => "", "station_type" => ""]] //invalid station type
        ];
    }

    /**
     * @dataProvider validGetReachDataProvider
     */
    public function test_rated_stations_for_media_plans_successfully_returned($params, $expected)
    {
        $overrides = [
            "target_population" => 11859,
            "population" => 27775,
            "criteria_age_groups" => json_encode([["min" => 15, "max" => 50]]),
            "gender" => json_encode(["Male", "Female"]),
            "criteria_region" => json_encode(["South South", "North Central", "South West"]),
            "criteria_social_class" => json_encode(["A", "B", "C", "D", "E"])
        ];

        $user = $this->setupUserWithPermissions();
        $media_plan = $this->setupMediaPlan($user, $overrides);

        $params["plan_id"] = $media_plan->id;
        $response = $this->getResponse($user, $params);

        $response->assertStatus(200);
        
        //assert the json response
        $actual = $response->json()["data"];
        $this->assertEquals(count($expected), count($actual));
        foreach ($expected as $index => $value) {
            $this->assertArraySubset($value, $actual[$index]);
        }
        
    }

    public function validGetReachDataProvider()
    {
        return [
            [
                ["day_part" => "all", "state" => "all", "day" => "all", "station_type" => "all"],
                [
                    ["name" => "NTA", "key" => "5213314f77a5ac013f5f52bbf73e3b1d", "type" => "Network", "state" => "", "total_audience" => 9006],
                    ["name" => "AIT", "key" => "ba041ea4acf277b105a0b8db9764c8a1", "type" => "Network", "state" => "", "total_audience" => 5706],
                    ["name" => "AIT", "key" => "b8bfc1b3d299e052db5327cbf529389a", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "RSTV", "key" => "0d534d9fd76ea8ff604675a482ccdd22", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "Silverbird Television", "key" => "8067465358bda7958002ee0f61c3608d", "type" => "Network", "state" => "", "total_audience" => 5706],
                    ["name" => "Silverbird Television", "key" => "d39b1d9cb81387b042c74423d0b73794", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "BCOS", "key" => "9a2addb2610752c29ab0cf48af53cf64", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "Galaxy TV", "key" => "c276d0fc1405bb547030f5730e3f796f", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "NTA 45 & 7", "key" => "095f139dea2f1b05de7c454d420156f9", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "NTA", "key" => "0deed05c94247f321f3807fe9c439e1e", "type" => "Regional", "state" => "Rivers", "total_audience" => 2853],
                    ["name" => "Zee World", "key" => "3a03ffaeecb41caafbb6a3c422cba947", "type" => "International", "state" => "", "total_audience" => 2853],
                    ["name" => "Channels Television", "key" => "e1e9453b27232018b7c9a85993f816df", "type" => "International", "state" => "", "total_audience" => 2551],
                    ["name" => "NTA", "key" => "57862b9746faa9a1bf325ef7f12a33b3", "type" => "Regional", "state" => "Niger", "total_audience" => 2551]
                ] //response
            ], //all filters
            [
                ["day_part" => "all", "state" => "all", "day" => "all", "station_type" => "regional"],
                [
                    ["name" => "AIT", "key" => "b8bfc1b3d299e052db5327cbf529389a", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "RSTV", "key" => "0d534d9fd76ea8ff604675a482ccdd22", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "Silverbird Television", "key" => "d39b1d9cb81387b042c74423d0b73794", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "BCOS", "key" => "9a2addb2610752c29ab0cf48af53cf64", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "Galaxy TV", "key" => "c276d0fc1405bb547030f5730e3f796f", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "NTA 45 & 7", "key" => "095f139dea2f1b05de7c454d420156f9", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "NTA", "key" => "0deed05c94247f321f3807fe9c439e1e", "type" => "Regional", "state" => "Rivers", "total_audience" => 2853],
                    ["name" => "NTA", "key" => "57862b9746faa9a1bf325ef7f12a33b3", "type" => "Regional", "state" => "Niger", "total_audience" => 2551],
                ] //response
            ], //regional filters
            [
                ["day_part" => "all", "state" => "all", "day" => "all", "station_type" => "network"],
                [
                    ["name" => "NTA", "key" => "5213314f77a5ac013f5f52bbf73e3b1d", "type" => "Network", "state" => "", "total_audience" => 9006],
                    ["name" => "AIT", "key" => "ba041ea4acf277b105a0b8db9764c8a1", "type" => "Network", "state" => "", "total_audience" => 5706],
                    ["name" => "Silverbird Television", "key" => "8067465358bda7958002ee0f61c3608d", "type" => "Network", "state" => "", "total_audience" => 5706],
                ] //response
            ], //network
            [
                ["day_part" => "all", "state" => "all", "day" => "all", "station_type" => "international"],
                [
                    ["name" => "Zee World", "key" => "3a03ffaeecb41caafbb6a3c422cba947", "type" => "International", "state" => "", "total_audience" => 2853],
                    ["name" => "Channels Television", "key" => "e1e9453b27232018b7c9a85993f816df", "type" => "International", "state" => "", "total_audience" => 2551]
                ] //response
            ], //international
            [
                ["day_part" => "all", "state" => "all", "day" => "Mon", "station_type" => "regional"],
                [
                    ["name" => "AIT", "key" => "b8bfc1b3d299e052db5327cbf529389a", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "Silverbird Television", "key" => "d39b1d9cb81387b042c74423d0b73794", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "BCOS", "key" => "9a2addb2610752c29ab0cf48af53cf64", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "Galaxy TV", "key" => "c276d0fc1405bb547030f5730e3f796f", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "RSTV", "key" => "0d534d9fd76ea8ff604675a482ccdd22", "type" => "Regional", "state" => "Rivers", "total_audience" => 2853],
                    ["name" => "NTA", "key" => "57862b9746faa9a1bf325ef7f12a33b3", "type" => "Regional", "state" => "Niger", "total_audience" => 2551]
                ] //response
            ], //regional and day
            [
                ["day_part" => "Late Night", "state" => "all", "day" => "all", "station_type" => "all"],
                [
                    ["name" => "Silverbird Television", "key" => "8067465358bda7958002ee0f61c3608d", "type" => "Network", "state" => "", "total_audience" => 5706],
                    ["name" => "Silverbird Television", "key" => "d39b1d9cb81387b042c74423d0b73794", "type" => "Regional", "state" => "Rivers", "total_audience" => 5706],
                    ["name" => "BCOS", "key" => "9a2addb2610752c29ab0cf48af53cf64", "type" => "Regional", "state" => "Oyo", "total_audience" => 3602],
                    ["name" => "AIT", "key" => "ba041ea4acf277b105a0b8db9764c8a1", "type" => "Network", "state" => "", "total_audience" => 2853],
                    ["name" => "AIT", "key" => "b8bfc1b3d299e052db5327cbf529389a", "type" => "Regional", "state" => "Rivers", "total_audience" => 2853],
                    ["name" => "NTA", "key" => "5213314f77a5ac013f5f52bbf73e3b1d", "type" => "Network", "state" => "", "total_audience" => 2853],
                    ["name" => "NTA", "key" => "0deed05c94247f321f3807fe9c439e1e", "type" => "Regional", "state" => "Rivers", "total_audience" => 2853],
                    ["name" => "Zee World", "key" => "3a03ffaeecb41caafbb6a3c422cba947", "type" => "International", "state" => "", "total_audience" => 2853],
                ] //response
            ], //all and day part
            [
                ["day_part" => "Late Night", "state" => "all", "day" => "Sat", "station_type" => "all"],
                []
            ],
        ];
    }
}
