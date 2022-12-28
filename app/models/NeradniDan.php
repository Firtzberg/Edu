<?php

/**
 * NeradniDan
 *
 * @property integer $id
 * @property string $naziv
 * @property integer $dan
 * @property integer $mjesec
 * @property integer|null $godina
 * @method static \Illuminate\Database\Query\Builder|\NeradniDan whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\NeradniDan Datum($value)
 */

class NeradniDan extends Eloquent{
	const NOT_FOUND_MESSAGE = 'Zadani neradni dan nije pronađena u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'neradni_dani';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $guarded = array('id');

    /**
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $datum
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeDatum($query, $datum) {
        $dto = new DateTime($datum);
        $godina = $dto->format('Y');
        $mjesec = $dto->format('m');
        $dan = $dto->format('d');
        return $query->where(function($andQuery) use ($godina, $mjesec, $dan) {
            $andQuery->whereDan($dan)
                ->whereMjesec($mjesec)
                ->where(function($orQuery) use ($godina){
                    $orQuery->whereNull('godina')->orWhere('godina', $godina);
                });
        });
    }

    public function getErrorOrSync($input){
        if(!is_array($input))
            return "Wrong input";

        //provjera postojanja nužnih podataka
        //provjera naziva
        $naziv = $this->naziv;
        if(!$naziv && !isset($input['naziv']))
            return 'Naziv je obvezan';
        if(isset($input['naziv']))
            $naziv = $input['naziv'];
        //provjera dana
        $dan = $this->dan;
        if(!$dan && !isset($input['dan']))
            return 'Dan je obvezan';
        if(isset($input['dan']))
            $dan = $input['dan'];
        //provjera mjeseca
        $mjesec = $this->mjesec;
        if(!$mjesec && !isset($input['mjesec']))
            return 'Mjesec je obvezan';
        if(isset($input['mjesec']))
            $mjesec = $input['mjesec'];
        //provjera broja učenika
        $godina = null;
        if(isset($input['godina']))
            $godina = $input['godina'];
        //kraj provjere nužnih podataka

        //provjera vrijednosti podataka
        if($mjesec < 1 || $mjesec > 12)
            return 'Mjesec mora biti od 1 do 12.';
        if($dan < 1 || $dan > cal_days_in_month(CAL_GREGORIAN, $mjesec, 2024))
            return 'Broj dana u danom mjesecu mora biti od 1 do ' . cal_days_in_month(CAL_GREGORIAN, $mjesec, 2024) . '.';
        //kraj provjere vrijednosti podataka

        //pohrana podataka
        $this->naziv = $naziv;
        $this->dan = $dan;
        $this->mjesec = $mjesec;
        $this->godina = $godina;

        $this->save();
    }
}