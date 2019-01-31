
<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table)
        {
            $table->string('id', 25);
            $table->string('email', 50)->unique();
            $table->string('username', 50)->nullable();
            $table->string('password');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->default('');
            $table->unsignedInteger('country_id')->nullable();
            $table->date('birthday')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('confirmation_token', 60)->nullable();
            $table->string('status', 20);
            $table->integer('two_factor_country_code')->nullable();
            $table->integer('two_factor_phone')->nullable();
            $table->text('two_factor_options')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->primary('id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        DB::statement("INSERT into users (id, email, username, password, firstname, lastname, phone_number, avatar, 
                                                address, country_id, birthday, last_login, confirmation_token, status, 
                                                two_factor_country_code, two_factor_phone, two_factor_options, 
                                                remember_token, created_at, updated_at)
                                    SELECT id, email, null, password, firstname, lastname, phone_number, null, '', 
                                    null, null, null, null, 'Active', null, null, null, null, time_created, 
                                    time_modified from user_old
                        ");


    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
