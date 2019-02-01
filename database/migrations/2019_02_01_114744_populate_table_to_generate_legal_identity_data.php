<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulateTableToGenerateLegalIdentityData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $company_type_broadcasters = \Vanguard\Models\CompanyType::where('name', \Vanguard\Libraries\Enum\CompanyTypeName::BROADCASTER)->first();
        $company_type_agencies = \Vanguard\Models\CompanyType::where('name', \Vanguard\Libraries\Enum\CompanyTypeName::AGENCY)->first();

        DB::connection('api')->statement("
        insert into companies (companies.id, companies.name, companies.address, companies.logo, companies.company_type_id,
        companies.created_at, companies.updated_at, companies.parent_company_id)
        select broadcasters.id, broadcasters.brand, broadcasters.location, broadcasters.image_url, '$company_type_broadcasters->id',
        broadcasters.time_created, broadcasters.time_modified,
        parent_companies.id from broadcasters inner join parent_companies on broadcasters.brand = parent_companies.name;
        
        insert into company_user (company_user.company_id, company_user.user_id)
        select id, user_id from broadcasters;
        
        select agents.id, agents.brand, agents.location, agents.image_url, '$company_type_agencies->id', agents.time_created, 
        agents.time_modified, parent_companies.id from agents inner join parent_companies on agents.brand = parent_companies.name;
        
        insert into company_user (company_user.company_id, company_user.user_id)
        select id, user_id from agents;
        
        insert into channel_company (channel_company.`channel_id`, channel_company.`company_id`)
        select broadcasters.channel_id, companies.id from broadcasters inner join companies on companies.id = broadcasters.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('generate_legal_identity_data', function (Blueprint $table) {
            //
        });
    }
}
