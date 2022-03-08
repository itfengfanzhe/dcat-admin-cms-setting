<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateCmsMakeSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itffz_cms_make_setting', function (Blueprint $table) {
            $table->id();
            $table->text('tab_name')->comment('table标签名称');
            $table->jsonb("field_json")->nullable()->comment('字段');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itffz_cms_make_setting');
    }
}
