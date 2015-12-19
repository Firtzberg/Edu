<?php

class Cjenovnik extends Eloquent {

    const NOT_FOUND_MESSAGE = 'Zadani cjenovnik nije pronađen u sustavu.';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cjenovnici';
    protected $guarded = array('id');

    /**
     * 
     * @param array $input
     * @return null|string
     */
    public function getErrorOrSync($input) {
        $ime = $this->ime;
        if (isset($input['ime']))
            $ime = $input['ime'];
        if (!$ime)
            return 'Ime cjenovnika je obvezno.';

        $cijena_1_osoba = $this->cijena_1_osoba;
        if (isset($input['cijena_1_osoba']))
            $cijena_1_osoba = $input['cijena_1_osoba'];
        $cijena_2_osobe = $this->cijena_2_osobe;
        if (isset($input['cijena_2_osobe']))
            $cijena_2_osobe = $input['cijena_2_osobe'];
        $cijena_3_osobe = $this->cijena_3_osobe;
        if (isset($input['cijena_3_osobe']))
            $cijena_2_osobe = $input['cijena_3_osobe'];
        $cijena_4_osobe = $this->cijena_4_osobe;
        if (isset($input['cijena_4_osobe']))
            $cijena_4_osobe = $input['cijena_4_osobe'];
        $cijena_vise_osoba = $this->cijena_vise_osoba;
        if (isset($input['cijena_vise_osoba']))
            $cijena_vise_osoba = $input['cijena_vise_osoba'];
        $instruktor_1_osoba = $this->instruktor_1_osoba;
        if (isset($input['instruktor_1_osoba']))
            $instruktor_1_osoba = $input['instruktor_1_osoba'];
        $instruktor_2_osobe = $this->instruktor_2_osobe;
        if (isset($input['instruktor_2_osobe']))
            $instruktor_2_osobe = $input['instruktor_2_osobe'];
        $instruktor_3_osobe = $this->instruktor_3_osobe;
        if (isset($input['instruktor_3_osobe']))
            $instruktor_3_osobe = $input['instruktor_3_osobe'];
        $instruktor_4_osobe = $this->instruktor_4_osobe;
        if (isset($input['instruktor_4_osobe']))
            $instruktor_4_osobe = $input['instruktor_4_osobe'];
        $instruktor_udio_vise_osoba = $this->instruktor_udio_vise_osoba;
        if (isset($input['instruktor_udio_vise_osoba']))
            $instruktor_udio_vise_osoba = $input['instruktor_udio_vise_osoba'];

        //provjera zauzetosti imena
        $query = Cijenovnik::where('ime', '=', $ime);
        if ($this->id > 0)
            $query = $query->where('id', '!=', $this->id);
        if ($query->count() > 0)
            return 'Već postoji cjenovnik s imenom ' . $ime . '.';
        //kraj provjere zauzetosti imena
        //pohrana podataka
        $this->ime = $ime;
        $this->cijena_1_osoba = $cijena_1_osoba;
        $this->cijena_2_osobe = $cijena_2_osobe;
        $this->cijena_3_osobe = $cijena_3_osobe;
        $this->cijena_4_osobe = $cijena_4_osobe;
        $this->cijena_vise_osoba = $cijena_vise_osoba;
        $this->instruktor_1_osoba = $instruktor_1_osoba;
        $this->instruktor_2_osobe = $instruktor_2_osobe;
        $this->instruktor_3_osobe = $instruktor_3_osobe;
        $this->instruktor_4_osobe = $instruktor_4_osobe;
        $this->instruktor_udio_vise_osoba = $instruktor_udio_vise_osoba;
        if (isset($input['opis']))
            $this->opis = $input['opis'];
        $this->save();
    }

    /**
     * 
     * @return string
     */
    public function link() {
        if (Auth::user()->hasPermission(Permission::PERMISSION_VIEW_CJENOVNIK)) {
            return link_to_route('Cjenovnik.show', $this->ime, array('id' => $this->id));
        }
        return $this->ime;
    }

}
