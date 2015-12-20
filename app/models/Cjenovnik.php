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

    public function c_m_p() {
        return $this->belongsToMany('Predmet', 'c_m_p')
                        ->withPivot('mjera_id');
    }

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

            $data[2]['key'] = 'cijena_2_osobe';
            $data[3]['key'] = 'cijena_3_osobe';
            $data[4]['key'] = 'cijena_4_osobe';
            $data[2]['key2'] = 'instruktor_2_osobe';
            $data[3]['key2'] = 'instruktor_3_osobe';
            $data[4]['key2'] = 'instruktor_4_osobe';
            
        $cijena_1_osoba = $this->cijena_1_osoba;
        if (isset($input['cijena_1_osoba']))
            $cijena_1_osoba = $input['cijena_1_osoba'];
        if (!$cijena_1_osoba)
            return 'Cijena za 1 osobu je obvezna.';
        for($i = 2; $i < 5; $i++)
        {
            $data[$i]['value'] = $this[$data[$i]['key']];
            $data[$i]['value2'] = $this[$data[$i]['key2']];
        if (isset($input[$data[$i]['key']]))
            $data[$i]['value'] = $input[$data[$i]['key']];
        if (isset($input[$data[$i]['key2']]))
            $data[$i]['value2'] = $input[$data[$i]['key2']];
        if (!$data[$i]['value'])
            return "Cijena za $i osobe je obvezna.";
        if (!$data[$i]['value2'])
            return "Instruktorov udio za $i osobe je obvezan.";
        }
        $cijena_vise_osoba = $this->cijena_vise_osoba;
        if (isset($input['cijena_vise_osoba']))
            $cijena_vise_osoba = $input['cijena_vise_osoba'];
        if (!$cijena_vise_osoba)
            return 'Cijena za 5 i više osoba je obvezna.';
        
        $instruktor_1_osoba = $this->instruktor_1_osoba;
        if (isset($input['instruktor_1_osoba']))
            $instruktor_1_osoba = $input['instruktor_1_osoba'];
        if (!$instruktor_1_osoba)
            return 'Instruktorov udio za 1 osobu je obvezan.';
        $instruktor_udio_vise_osoba = $this->instruktor_udio_vise_osoba;
        if (isset($input['instruktor_udio_vise_osoba']))
            $instruktor_udio_vise_osoba = $input['instruktor_udio_vise_osoba'];
        if (!$instruktor_udio_vise_osoba)
            return 'Instruktorov udio za 5 i više osoba je obvezan.';

        //provjera zauzetosti imena
        $query = Cjenovnik::where('ime', '=', $ime);
        if ($this->id > 0)
            $query = $query->where('id', '!=', $this->id);
        if ($query->count() > 0)
            return 'Već postoji cjenovnik s imenom ' . $ime . '.';
        //kraj provjere zauzetosti imena
        //pohrana podataka
        $this->ime = $ime;
        $this->cijena_1_osoba = $cijena_1_osoba;
        $this->cijena_vise_osoba = $cijena_vise_osoba;
        $this->instruktor_1_osoba = $instruktor_1_osoba;
        $this->instruktor_udio_vise_osoba = $instruktor_udio_vise_osoba;
        for($i = 2; $i < 5; $i++)
        {
            $this[$data[$i]['key']] = $data[$i]['value'];
            $this[$data[$i]['key2']] = $data[$i]['value2'];
        }
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
