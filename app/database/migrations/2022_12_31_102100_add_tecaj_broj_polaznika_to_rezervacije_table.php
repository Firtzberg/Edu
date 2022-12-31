<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTecajBrojPolaznikaToRezervacijeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('rezervacije', function(Blueprint $table) {
            $table->integer('tecaj_broj_polaznika')
                ->unsigned()
                ->nullable();
        });
        
        Rezervacija::whereTecaj(true)->with('klijenti')->chunk(500, function($rezervacije){
            foreach($rezervacije as $rezervacija){
                $rezervacija->tecaj_broj_polaznika = $rezervacija->klijenti->count();
                $rezervacija->save();
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('rezervacije', function(Blueprint $table) {
            $table->dropColumn('tecaj_broj_polaznika');
        });
    }
}
