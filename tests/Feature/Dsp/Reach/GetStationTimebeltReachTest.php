<?php

namespace Tests\Feature\Dsp\Reach;

class GetStationTimebeltReachTest extends ReachTestCase
{
    protected $route_name = 'reach.get-timebelts';

    public function test_unauthenticated_user_cannot_access_timebelt_get_route()
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

    public function test_attempting_to_get_timebelt_reach_for_non_existent_plan_returns_404()
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

    public function test_403_returned_if_attempting_to_get_timebelt_reach_for_plan_user_does_not_have_access_to()
    {
        $this->markTestSkipped("Come back to this");

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
            [["day_part" => "All", "state" => "All", "day" => "", "station_type" => "All"]], //invalid day
            [["day_part" => "All", "state" => "All", "day" => "All", "station_type" => ""]], //invalid station type
            [["day_part" => "All", "state" => "All", "day" => "All", "station_type" => "All", "station_key" => ""]] //invalid station key
        ];
    }

    /**
     * @dataProvider validGetReachDataProvider
     */
    public function test_rated_timebelts_for_stations_successfully_returned($params, $expected)
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
                ["day_part" => "Late Night", "state" => "all", "day" => "all", "station_type" => "all", "station_key" => ["9a2addb2610752c29ab0cf48af53cf64"]],
                [
                    [
                        "program" => "Unknown Program", "day" => "Thu", "start_time" => "22:00:00", "end_time" => "22:15:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Thu-22:00:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Thu", "start_time" => "22:15:00", "end_time" => "22:30:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Thu-22:15:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Thu", "start_time" => "22:30:00", "end_time" => "22:45:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Thu-22:30:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Thu", "start_time" => "22:45:00", "end_time" => "23:00:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Thu-22:45:00")
                    ]
                ] 
            ],
            [
                ["day_part" => "Primetime", "state" => "all", "day" => "Mon", "station_type" => "all", "station_key" => ["ba041ea4acf277b105a0b8db9764c8a1", "5213314f77a5ac013f5f52bbf73e3b1d"]],
                [
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "19:00:00", "end_time" => "19:15:00",  "total_audience" => 5706, 
                        "rating" => 20.54,  "station_key" => "ba041ea4acf277b105a0b8db9764c8a1", "station" => "AIT", "station_type" => "Network", "state" => "",
                        "key" => md5("ba041ea4acf277b105a0b8db9764c8a1-Mon-19:00:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "19:15:00", "end_time" => "19:30:00",  "total_audience" => 5706, 
                        "rating" => 20.54,  "station_key" => "ba041ea4acf277b105a0b8db9764c8a1", "station" => "AIT", "station_type" => "Network", "state" => "",
                        "key" => md5("ba041ea4acf277b105a0b8db9764c8a1-Mon-19:15:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "20:00:00", "end_time" => "20:15:00",  "total_audience" => 2853, 
                        "rating" => 10.27,  "station_key" => "ba041ea4acf277b105a0b8db9764c8a1", "station" => "AIT", "station_type" => "Network", "state" => "",
                        "key" => md5("ba041ea4acf277b105a0b8db9764c8a1-Mon-20:00:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "20:15:00", "end_time" => "20:30:00",  "total_audience" => 2853, 
                        "rating" => 10.27,  "station_key" => "ba041ea4acf277b105a0b8db9764c8a1", "station" => "AIT", "station_type" => "Network", "state" => "",
                        "key" => md5("ba041ea4acf277b105a0b8db9764c8a1-Mon-20:15:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "20:30:00", "end_time" => "20:45:00",  "total_audience" => 2853, 
                        "rating" => 10.27,  "station_key" => "ba041ea4acf277b105a0b8db9764c8a1", "station" => "AIT", "station_type" => "Network", "state" => "",
                        "key" => md5("ba041ea4acf277b105a0b8db9764c8a1-Mon-20:30:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "20:45:00", "end_time" => "21:00:00",  "total_audience" => 2853, 
                        "rating" => 10.27,  "station_key" => "ba041ea4acf277b105a0b8db9764c8a1", "station" => "AIT", "station_type" => "Network", "state" => "",
                        "key" => md5("ba041ea4acf277b105a0b8db9764c8a1-Mon-20:45:00")
                    ]
                ] 
            ],
            [
                ["day_part" => "Breakfast", "state" => "all", "day" => "all", "station_type" => "regional", "station_key" => ["9a2addb2610752c29ab0cf48af53cf64"]],
                [
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "08:00:00", "end_time" => "08:15:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Mon-08:00:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "08:15:00", "end_time" => "08:30:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Mon-08:15:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "08:30:00", "end_time" => "08:45:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Mon-08:30:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Mon", "start_time" => "08:45:00", "end_time" => "09:00:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Mon-08:45:00")
                    ],
                    [
                        "program" => "Unknown Program", "day" => "Tue", "start_time" => "09:00:00", "end_time" => "09:15:00",  "total_audience" => 3602, 
                        "rating" => 12.97,  "station_key" => "9a2addb2610752c29ab0cf48af53cf64", "station" => "BCOS", "station_type" => "Regional", "state" => "Oyo",
                        "key" => md5("9a2addb2610752c29ab0cf48af53cf64-Tue-09:00:00")
                    ]
                ] 
            ],
        ];
    }
}
