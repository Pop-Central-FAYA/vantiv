<?php

use Illuminate\Database\Seeder;

use Vanguard\Models\TvStation;

/**
 * This will loop through and create a tv station and associate with a publisher
 * (If that tv station and publisher is not created yet)
 */
class TvStationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regional_stations = $this->getRegionalTvStations();
        $national_stations = $this->getNationalTvStations();
        $satellite_stations = $this->getSatelliteTVStations();

        $this->create($regional_stations);
        $this->create($national_stations);
        $this->create($satellite_stations);
    }

    protected function create($station_list) {
        foreach ($station_list as $station) {
            $key = md5("{$station['name']}-{$station['type']}-{$station['state']}-{$station['city']}-{$station['region']}");
            $station_attrs = array(
                'name' => $station['name'],
                'type' => $station['type'],
                'state' => $station['state'],
                'city' => $station['city'],
                'region' => $station['region']
            );
            TvStation::firstOrCreate(['key' => $key], $station_attrs);
        }
    }

    protected function getRegionalTvStations()
    {
        return [
            ['name' => 'BCA TV','type' => 'Regional', 'state' => 'Abia', 'city' => 'Umuahia', 'region' => 'South East'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Abia', 'city' => 'Aba', 'region' => 'South East'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Abia', 'city' => 'Umuahia', 'region' => 'South East'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Abuja', 'city' => 'Abuja', 'region' => 'North Central'],
            ['name' => 'Channels TV', 'type' => 'Regional', 'state' => 'Abuja', 'city' => 'Abuja', 'region' => 'North Central'],
            ['name' => 'ITV', 'type' => 'Regional', 'state' => 'Abuja', 'city' => 'Abuja', 'region' => 'North Central'],
            ['name' => 'Silverbird TV', 'type' => 'Regional', 'state' => 'Abuja', 'city' => 'Abuja', 'region' => 'North Central'],
            ['name' => 'Wazobia Max', 'type' => 'Regional', 'state' => 'Abuja', 'city' => 'Abuja', 'region' => 'North Central'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Abuja', 'city' => 'Abuja', 'region' => 'North Central'],

            ['name' => 'ABS', 'type' => 'Regional', 'state' => 'Anambra', 'city' => 'Onitsha', 'region' => 'South East'],
            ['name' => 'ABS', 'type' => 'Regional', 'state' => 'Anambra', 'city' => 'Awka', 'region' => 'South East'],
            ['name' => 'MST', 'type' => 'Regional', 'state' => 'Anambra', 'city' => 'Obosi', 'region' => 'South East'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Anambra', 'city' => 'Awka', 'region' => 'South East'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Anambra', 'city' => 'Onitsha', 'region' => 'South East'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Bauchi', 'city' => 'Bauchi', 'region' => 'North East'],
            ['name' => 'BATV', 'type' => 'Regional', 'state' => 'Bauchi', 'city' => 'Bauchi', 'region' => 'North East'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Bauchi', 'city' => 'Bauchi', 'region' => 'North East'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Borno', 'city' => 'Borno', 'region' => 'North East'],
            ['name' => 'BRTV', 'type' => 'Regional', 'state' => 'Borno', 'city' => 'Maiduguri', 'region' => 'North East'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Borno', 'city' => 'Maiduguri', 'region' => 'North East'],

            ['name' => 'CRTV', 'type' => 'Regional', 'state' => 'Cross River', 'city' => 'Calabar', 'region' => 'South South'],
            ['name' => 'CRTV', 'type' => 'Regional', 'state' => 'Cross River', 'city' => 'Ikom', 'region' => 'South South'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Cross River', 'city' => 'Calabar', 'region' => 'South South'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Edo', 'city' => 'Benin', 'region' => 'South South'],
            ['name' => 'Channels TV', 'type' => 'Regional', 'state' => 'Edo', 'city' => '', 'region' => 'South South'],
            ['name' => 'EBS', 'type' => 'Regional', 'state' => 'Edo', 'city' => 'Benin', 'region' => 'South South'],
            ['name' => 'ITV', 'type' => 'Regional', 'state' => 'Edo', 'city' => 'Benin', 'region' => 'South South'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Edo', 'city' => 'Benin', 'region' => 'South South'],
            ['name' => 'Silverbird TV', 'type' => 'Regional', 'state' => 'Edo', 'city' => 'Benin', 'region' => 'South South'],

            ['name' => 'Ekiti State Televison', 'type' => 'Regional', 'state' => 'Ekiti', 'city' => '', 'region' => 'South West'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Ekiti', 'city' => 'Ado-Ekiti', 'region' => 'South West'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Enugu', 'city' => 'Enugu', 'region' => 'South East'],
            ['name' => 'ETV', 'type' => 'Regional', 'state' => 'Enugu', 'city' => 'Enugu', 'region' => 'South East'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Enugu', 'city' => 'Enugu', 'region' => 'South East'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Kaduna', 'region' => 'North West'],
            ['name' => 'Capital TV', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Kaduna', 'region' => 'North West'],
            ['name' => 'DITV', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Kaduna', 'region' => 'North West'],
            ['name' => 'KSTV', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Kaduna', 'region' => 'North West'],
            ['name' => 'Liberty TV', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Kaduna', 'region' => 'North West'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Kaduna', 'region' => 'North West'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Kafanchan', 'region' => 'North West'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Zaria', 'region' => 'North West'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Kaduna', 'city' => 'Birnin-Gwari', 'region' => 'North West'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Kano', 'city' => 'Kano', 'region' => 'North West'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Kano', 'city' => 'Kano', 'region' => 'North West'],
            ['name' => 'CTV', 'type' => 'Regional', 'state' => 'Kano', 'city' => 'Kano', 'region' => 'North West'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Kwara', 'city' => 'Ilorin', 'region' => 'North Central'],
            ['name' => 'KTV', 'type' => 'Regional', 'state' => 'Kwara', 'city' => 'Ilorin', 'region' => 'North Central'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Kwara', 'city' => 'Ilorin', 'region' => 'North Central'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'Channels TV', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'Galaxy TV', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'Kwese Free Sports', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'Lagos Continental', 'type' => 'Regional', 'state' => 'Lagos', 'city' => '', 'region' => 'Lagos'],
            ['name' => 'LTV', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'MITV', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'NTA 2', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'ONTV', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'Silverbird TV', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],
            ['name' => 'Super Screen', 'type' => 'Regional', 'state' => 'Lagos', 'city' => '', 'region' => 'Lagos'],
            ['name' => 'Wazobia Max', 'type' => 'Regional', 'state' => 'Lagos', 'city' => 'Lagos', 'region' => 'Lagos'],

            ['name' => 'NSTV', 'type' => 'Regional', 'state' => 'Niger', 'city' => 'Minna', 'region' => 'North Central'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Niger', 'city' => 'Minna', 'region' => 'North Central'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Oyo', 'city' => 'Ibadan', 'region' => 'South West'],
            ['name' => 'BCOS', 'type' => 'Regional', 'state' => 'Oyo', 'city' => 'Ibadan', 'region' => 'South West'],
            ['name' => 'Galaxy TV', 'type' => 'Regional', 'state' => 'Oyo', 'city' => 'Ibadan', 'region' => 'South West'],
            ['name' => 'MITV', 'type' => 'Regional', 'state' => 'Oyo', 'city' => 'Ibadan', 'region' => 'South West'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Oyo', 'city' => 'Ibadan', 'region' => 'South West'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Plateau', 'city' => 'Jos', 'region' => 'North Central'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Plateau', 'city' => 'Jos', 'region' => 'North Central'],
            ['name' => 'PRTV', 'type' => 'Regional', 'state' => 'Plateau', 'city' => 'Jos', 'region' => 'North Central'],
            ['name' => 'Silverbird TV', 'type' => 'Regional', 'state' => 'Plateau', 'city' => 'Jos', 'region' => 'North Central'],

            ['name' => 'AIT', 'type' => 'Regional', 'state' => 'Rivers', 'city' => 'Port Harcourt', 'region' => 'South South'],
            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Rivers', 'city' => 'Port Harcourt', 'region' => 'South South'],
            ['name' => 'RSTV', 'type' => 'Regional', 'state' => 'Rivers', 'city' => 'Port Harcourt', 'region' => 'South South'],
            ['name' => 'Silverbird TV', 'type' => 'Regional', 'state' => 'Rivers', 'city' => 'Port Harcourt', 'region' => 'South South'],
            ['name' => 'Wazobia Max', 'type' => 'Regional', 'state' => 'Rivers', 'city' => 'Port Harcourt', 'region' => 'South South'],

            ['name' => 'NTA', 'type' => 'Regional', 'state' => 'Sokoto', 'city' => 'Sokoto', 'region' => 'North West'],
            ['name' => 'Sokoto State Television', 'type' => 'Regional', 'state' => 'Sokoto', 'city' => '', 'region' => 'North West']
        ];
    }

    protected function getNationalTvStations()
    {
        return [
            ['name' => 'Silverbird TV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'AIT','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'ITV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Galaxy TV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Liberty TV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MITV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'PRTV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'TVC','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Wazobia Max','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Channels TV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Kwese','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'LTV','type' => 'Network', 'state' => '', 'city' => '', 'region' => ''],
        ];
    }

    protected function getSatelliteTVStations()
    {
        return [
            ['name' => 'Africa Magic','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Africa Magic (Hausa)','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Africa Magic (Yoruba)','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Africa Magic Epic','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Africa Magic Family','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Africa Magic (Igbo)','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Africa Magic Showcase','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Africa Magic Urban','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'AIT International','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'AMC','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'AMC Movies','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'AMC Music','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'AMC Series','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Arewa 24','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'BBC','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'BBC Lifestyle','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'BBC News','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'BET','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Bollywood','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Canal TV','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Cartoon Network','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'CBS','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'CCTV','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'CCTV 4','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'CNN','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Comedy Central','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Discovery Channel','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Discovery Family','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Discovery Science','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Discovery Idx','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Discovery Idx','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Disney','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Disney','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'E! Entertainment','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox Crime','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox Entertainment','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox Movie','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox Movie','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox News','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Fox Sports','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Iroko','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Iroko 1','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Iroko 2','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net Movies Action','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net Movies City','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net Movies Premiere','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net Movies Star','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net Movies Zone','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net Smile','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net West','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'M-Net West','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MBC','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MBC 1','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MBC 3','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MBC 4','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MBC 4','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MBC Action','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MBC Max','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MTV','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'MTV Base','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'My TV','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'My TV Africa','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'My TV Series','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'My TV Yoruba','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'National Geographic','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'National Geographic Wild','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Nick Jr','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Nick Toons','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Nickelodeon','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Entertainment','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Hausa','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Igbo','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Igbo','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Knowledge','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA News 24','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Parliament','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Sports','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA Yoruba','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'PIUS TV Africa','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'RFI (France) Hausa Service','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'RFI (France) World Service','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'ROK','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'ROK 2','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'ROK 3','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Silverbird International','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Sky News','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Sound City','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 1','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 2','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 3','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 4','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 5','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 6','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 7','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 8','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 9','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 10','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 11','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport 12','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport Blitz','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport Select 4','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'SuperSport Select 1','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Star Life','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Star TV','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Star World','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Startimes Bollywood','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Startimes Bollywood Africa','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Startimes Orisun','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Startimes Yoruba','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Startimes','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Studio Universal','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Telemundo','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Trace Naija','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Viasat','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Vuzu','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'WAP TV','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Wazobia TV','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Zee World','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Kwese Sports 1','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Kwese Sports 2','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Kwese International','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'Activate','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => ''],
            ['name' => 'NTA International','type' => 'Satellite', 'state' => '', 'city' => '', 'region' => '']
        ];

    }
}
// php artisan db:seed --class=TvStationTableSeeder