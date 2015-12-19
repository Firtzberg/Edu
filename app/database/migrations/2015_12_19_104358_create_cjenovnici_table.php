<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCjenovniciTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cjenovnici', function(Blueprint $table) {
            $table->increments('id');
            $table->string('ime', 50);
            $table->text('opis')->nullable();
            $table->integer('cijena_1_osoba')
                    ->unsigned();
            $table->integer('cijena_2_osobe')
                    ->unsigned();
            $table->integer('cijena_3_osobe')
                    ->unsigned();
            $table->integer('cijena_4_osobe')
                    ->unsigned();
            $table->integer('cijena_vise_osoba')
                    ->unsigned();
            $table->integer('instruktor_1_osoba')
                    ->unsigned();
            $table->integer('instruktor_2_osobe')
                    ->unsigned();
            $table->integer('instruktor_3_osobe')
                    ->unsigned();
            $table->integer('instruktor_4_osobe')
                    ->unsigned();
            $table->integer('instruktor_udio_vise_osoba')
                    ->unsigned();
            $table->timestamps();
        });

        //Populate
        $res = DB::table('cijene')
                ->groupBy('individualno', 'popust', 'minimalno')
                ->get();
        $ukupno = function ($t, $n) {
            $x = $t->individualno - ($n - 1) * $t->popust;
            if ($x < $t->minimalno)
                return $t->minimalno;
            return $x;
        };
        $instruktoru = function ($t, $n) use ($ukupno) {
            $u = $ukupno($t, $n);
            return $u - Naplata::getSatnicaZaTvrtku($u);
        };
        $i = 0;
        foreach ($res as $triplet) {
            $i++;
            $triplet->cjenovnik_id = Cjenovnik::create(
                            array(
                                'ime' => "Cjenovnik $i",
                                'cijena_1_osoba' => $ukupno($triplet, 1),
                                'cijena_2_osobe' => $ukupno($triplet, 2),
                                'cijena_3_osobe' => $ukupno($triplet, 3),
                                'cijena_4_osobe' => $ukupno($triplet, 4),
                                'cijena_vise_osoba' => $triplet->minimalno,
                                'instruktor_1_osoba' => $instruktoru($triplet, 1),
                                'instruktor_2_osobe' => $instruktoru($triplet, 2),
                                'instruktor_3_osobe' => $instruktoru($triplet, 3),
                                'instruktor_4_osobe' => $instruktoru($triplet, 4),
                                'instruktor_udio_vise_osoba' => 50,
                            )
                    )->id;
        }
        //Populated

        Schema::create('c_m_p', function(Blueprint $table) {
            $table->integer('cjenovnik_id')
                    ->unsigned()
                    ->index();
            $table->integer('mjera_id')
                    ->unsigned()
                    ->index();
            $table->integer('predmet_id')
                    ->unsigned()
                    ->index();
            $table->primary(array('cjenovnik_id', 'mjera_id', 'predmet_id'));
        });

        Schema::table('c_m_p', function(Blueprint $table) {
            $table->foreign('cjenovnik_id')
                    ->references('id')->on('cjenovnici')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('mjera_id')
                    ->references('id')->on('mjere')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('predmet_id')
                    ->references('id')->on('predmeti')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
        });

        // Populate c_m_p
        foreach ($res as $triplet) {
            $relations = DB::table('cijene')
                    ->where('individualno', '=', $triplet->individualno)
                    ->where('popust', '=', $triplet->popust)
                    ->where('minimalno', '=', $triplet->minimalno)
                    ->get();
            foreach ($relations as $relation) {
                DB::table('c_m_p')->insert(array(
                    'cjenovnik_id' => $triplet->cjenovnik_id,
                    'mjera_id' => $relation->mjera_id,
                    'predmet_id' => $relation->predmet_id,
                ));
            }
        }
        // populated c_m_p
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('c_m_p', function(Blueprint $table) {
            $table->dropForeign('c_m_p_cjenovnik_id_foreign');
            $table->dropForeign('c_m_p_mjera_id_foreign');
            $table->dropForeign('c_m_p_predmet_id_foreign');
        });
        Schema::drop('c_m_p');
        Schema::drop('cjenovnici');
        Permission::where('ime', 'in', array(
            Permission::PERMISSION_VIEW_CJENOVNIK,
            Permission::PERMISSION_MANAGE_CJENOVNIK,
            Permission::PERMISSION_REMOVE_CJENOVNIK
        ))->delete();
    }

}
