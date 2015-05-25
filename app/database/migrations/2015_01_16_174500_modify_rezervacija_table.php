<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyRezervacijaTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('rezervacije', function(Blueprint $table) {
            $table->dateTime('kraj_rada');
        });
        
        $rezervacije = Rezervacija::all();
        
        foreach($rezervacije as $rezervacija){
            $rezervacija->kraj_rada = $rezervacija->kraj_rada();
            $rezervacija->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('rezervacije', function(Blueprint $table) {
            $table->dropColumn('kraj_rada');
        });
    }

}
