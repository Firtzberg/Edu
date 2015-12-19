<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCjeneTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        (new CreateCijeneTable())->down();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        (new CreateCijeneTable())->up();
        $relations = DB::table('c_m_p')->get();
        foreach ($relations as $r) {
            $c = Cjenovnik::find($r->cjenovnik_id);
            DB::table('cijene')->insert(array(
                'predmet_id' => $r->predmet_id,
                'mjera_id' => $r->mjera_id,
                'individualno' => $c->cijena_1_osoba,
                'popust' => $c->cijena_1_osoba - $c->cijena_2_osobe,
                'minimalno' => $c->cijena_vise_osoba,
            ));
        }
    }

}
