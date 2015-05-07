<?php namespace Filipac\Banip\Updates;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateIpsTable extends Migration
{

    public function up()
    {
        Schema::create('filipac_banip_ips', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('address');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('filipac_banip_ips');
    }

}
