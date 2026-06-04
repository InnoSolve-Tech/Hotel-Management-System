<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('users')
            ->whereIn('name', ['Hotelio Admin', 'Hotello Admin'])
            ->update(['name' => 'Hot-L Admin']);
    }

    public function down()
    {
        DB::table('users')
            ->where('name', 'Hot-L Admin')
            ->update(['name' => 'Hotelio Admin']);
    }
};
