<?php

namespace App\Controller;

use App\Model\Permission;
use App\Model\User;
use App\Model\Ucionica;
use App\Model\Kategorija;
use App\Model\Predmet;
use App\Model\Rezervacija;
use App\Model\Role;
use App\Model\Mjera;
use Auth;
use Redirect;
use Excel;

class ExcelController extends App\Controller\BaseController {

	const SHEET_NAME_USERS = 'Djelatnici';
	const SHEET_NAME_ROLES = 'Uloge';
	const SHEET_NAME_UCIONICE = 'Učionice';
	const SHEET_NAME_KATEGORIJE = 'Kategorije';
	const SHEET_NAME_PREDEMTI = 'Predmeti';
	const SHEET_NAME_PREDEMT_USER = 'PredmetDjelatnik';
	const SHEET_NAME_MJERE = 'Mjere';
	const SHEET_NAME_REZERVACIJE = 'Rezervacije';
	const SHEET_NAME_NAPLATE = 'Naplate';
	const SHEET_NAME_KLIJENTI = 'Klijenti';
	const SHEET_NAME_KLIJENT_REZERVACIJA = 'KlijentRezervacija';
        
    public function __construct() {
        $this->beforeFilter(function() {
            if (!Auth::user()->hasPermission(Permission::PERMISSION_DOWNLOAD_DATA)) {
                return Redirect::to('logout');
            }
        });
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('Excel.index');
	}

        /**
         * 
         * @param string $sheet Name of referenced sheet
         * @param int $totalCount Number of rows in sheet (without heading)
         * @param string $cellName Name of cell to reference
         * @param string $displayColumn Name of cell for hiperlink name
         * @return string
         */
	public static function getHyperlink($sheet, $totalCount, $cellName, $displayColumn = 'B'){
		return '=INDEX('.$sheet.'!$'.$displayColumn.'$2:$'.$displayColumn.'$'.($totalCount+1).
				',MATCH('.$cellName.','.$sheet.'!$A$2:$A$'.($totalCount+1).'))';
		//return '=HYPERLINK("#"&ADDRESS(MATCH('.$cellName.','.$sheet.'!$A$2:$A$'.($totalCount+1).')+1,1,,,"'.$sheet.'"))';//,'.
		//	'INDEX('.$sheet.'!$'.$displayColumn.'$2:$'.$displayColumn.'$'.($totalCount+1).
		//		',MATCH('.$cellName.','.$sheet.'!$A$2:$A$'.($totalCount+1).')))';
	}

	public function download(){
		$formats = array('xls', 'xlsx');
		if(in_array(Input::get('format'), $formats))
			$format = Input::get('format');
		else $format = 'xls';
		$month = Input::get('startMonth');
		if(!($month>0 && $month < 13))
			$month = 1;
		$year = Input::get('startYear');
		if(!($year>=2014 && $year <= date('Y')))
			$year = 2014;
		$from = "$year-$month-1 00:00:00";
		$month = Input::get('endMonth');
		if(!($month>0 && $month < 13))
			$month = 1;
		$year = Input::get('endYear');
		if(!($year>=2014 && $year <= date('Y')))
			$year = 2014;
		$month++;
		if($month > 12){
			$month = 1;
			$year++;
		}
		$to = "$year-$month-1 00:00:00";
		Excel::create('test', function($excel) use($from, $to){
			$usersCount = User::count();
			$predmetCount = Predmet::count();
			$mjeraCount = Mjera::count();
			$ucionicaCount = Ucionica::count();
			$ulogaCount = Role::count();
			$kategorijaCount = Kategorija::count();
			$rezervacijaCount = 0;

			$excel->setCreator('Hrvoje');

			$excel->sheet(self::SHEET_NAME_USERS, function($sheet) use ($ulogaCount){
				$sheet->appendRow(array('ID', 'Ime', 'Broj mobitela', 'Email',
					'Boja', 'ID uloge', 'Dodan', 'Zadnja promjena', 'Uloga'));
				$i = 1;
				foreach (User::select('id', 'name', 'broj_mobitela', 'email',
					'boja', 'role_id', 'created_at', 'updated_at')->get() as $user) {
					$i++;
					$row = $user->toArray();
					$row[] = self::getHyperlink(self::SHEET_NAME_ROLES, $ulogaCount, 'F'.$i);
					$sheet->appendRow($row);
				}
			});

			$excel->sheet(self::SHEET_NAME_ROLES, function($sheet){
				$sheet->appendRow(array('ID', 'Ime uloge', 'Opis', 'Dodana', 'Zadnja promjena'));
				$sheet->fromModel(
					Role::all(),
				null, 'A2', true, false);
			});

			$excel->sheet(self::SHEET_NAME_UCIONICE, function($sheet){
				$sheet->appendRow(array('ID', 'Ime učionice', 'Najveći broj učenika',
					'Adresa', 'Sprat', 'Opis', 'Dodana', 'Zadnja promjena'));
				$i = 1;
				foreach (Ucionica::select('id', 'naziv', 'max_broj_ucenika', 'adresa',
					'kat', 'opis', 'created_at', 'updated_at')->get() as $ucionica) {
					$i++;
					$row = $ucionica->toArray();
					$sheet->appendRow($row);
				}
			});

			$excel->sheet(self::SHEET_NAME_KATEGORIJE, function($sheet) use ($kategorijaCount){
				$sheet->appendRow(array('ID', 'Ime kategorije', 'ID nadkategorije',
					'Dodana', 'Zadnja promjena', 'Nadkategorija'));
				$i = 1;
				foreach (Kategorija::select('id', 'ime', 'nadkategorija_id', 'created_at',
					'updated_at')->get() as $kategorija) {
					$i++;
					$row = $kategorija->toArray();
					$row[] = self::getHyperlink(self::SHEET_NAME_KATEGORIJE, $kategorijaCount, 'C'.$i);
					$sheet->appendRow($row);
				}
			});

			$excel->sheet(self::SHEET_NAME_PREDEMTI, function($sheet) use ($kategorijaCount){
				$sheet->appendRow(array('ID', 'Ime predmeta', 'ID kategorije',
					'Dodana', 'Zadnja promjena', 'Kategorija'));
				$i = 1;
				foreach (Predmet::select('id', 'ime', 'kategorija_id', 'created_at',
					'updated_at')->get() as $predmet) {
					$i++;
					$row = $predmet->toArray();
					$row[] = self::getHyperlink(self::SHEET_NAME_KATEGORIJE, $kategorijaCount, 'C'.$i);
					$sheet->appendRow($row);
				}
			});

			$excel->sheet(self::SHEET_NAME_PREDEMT_USER, function($sheet) use ($predmetCount, $usersCount){
				$sheet->appendRow(array('ID djelatnika', 'ID predmeta', 'Djelatnik', 'Predmet'));
				$i = 1;
				foreach (DB::table('predmet_user')->select('user_id', 'predmet_id')
					->orderBy('user_id', 'predmet_id')->get() as $predmetUser) {
					$i++;
					$row = array();
					$row[] = $predmetUser->user_id;
					$row[] = $predmetUser->predmet_id;
					$row[] = self::getHyperlink(self::SHEET_NAME_USERS, $usersCount, 'A'.$i);
					$row[] = self::getHyperlink(self::SHEET_NAME_PREDEMTI, $predmetCount, 'B'.$i);
					$sheet->appendRow($row);
				}
			});

			$excel->sheet(self::SHEET_NAME_MJERE, function($sheet){
				$sheet->appendRow(array('ID', 'Ime mjere', 'Kratica', 'Trajanje'));
				$i = 1;
				foreach (Mjera::select('id', 'znacenje', 'simbol', 'trajanje')->get() as $mjera) {
					$i++;
					$row = $mjera->toArray();
					$sheet->appendRow($row);
				}
			});

			$naplate = array();
			$klijenti = array();
			$klijentRezervacije = array();

			$excel->sheet(self::SHEET_NAME_REZERVACIJE, function($sheet) use (&$naplate, &$klijenti, &$klijentRezervacije,
				$usersCount, $predmetCount, $mjeraCount, $ucionicaCount, &$rezervacijaCount, $from, $to){
				$sheet->appendRow(array('ID', 'Količina', 'ID djelatnika', 'ID predmeta', 'ID mjere',
					'ID učionice', 'Napomena', 'Dodana', 'Zadnja promjena', 'Djelatnik', 'Predmet', 'Mjera', 'Učionica'));
				$i = 1;
				$rezervacije = Rezervacija::select('id', 'kolicina', 'instruktor_id', 'predmet_id', 'mjera_id',
				'ucionica_id', 'napomena', 'created_at', 'updated_at')->with('naplata', 'klijenti')
				->whereBetween('pocetak_rada', array($from, $to))
				->get();
				$rezervacijaCount = $rezervacije->count();
				foreach ($rezervacije as $rezervacija) {
					$i++;
					$row = $rezervacija->toArray();
					unset($row['naplata']);
					unset($row['klijenti']);
					$row[] = self::getHyperlink(self::SHEET_NAME_USERS, $usersCount, 'C'.$i);
					$row[] = self::getHyperlink(self::SHEET_NAME_PREDEMTI, $predmetCount, 'D'.$i);
					$row[] = self::getHyperlink(self::SHEET_NAME_MJERE, $mjeraCount, 'E'.$i);
					$row[] = self::getHyperlink(self::SHEET_NAME_UCIONICE, $ucionicaCount, 'F'.$i);
					$sheet->appendRow($row);
					if(!$rezervacija->naplata)
						$sheet->row($sheet->getHighestRow(), function($row){
							$row->setBackground('#ff6666');
						});
					else $naplate[] = $rezervacija->naplata->toArray();
					foreach ($rezervacija->klijenti as $klijent) {
						$klijentRezervacije[] = array(
							'klijent_id' => $klijent->broj_mobitela,
							'rezervacija_id' => $rezervacija->id,
							'missed' => $klijent->pivot->missed);
						$klijenti[$klijent->broj_mobitela] = $klijent;
					}
				}
			});

			$excel->sheet(self::SHEET_NAME_NAPLATE, function($sheet) use ($naplate, $rezervacijaCount, $mjeraCount){
				$sheet->appendRow(array('ID', 'Ukupno naplaćeno', 'Za djelatnika', 'Za tvrtku', 'Naplaćena količina',
					'ID naplaćene mjere', 'Napomena', 'Dodana', 'Zadnja promjena', 'predmet rezervacije', 'Naplaćena mjera'));
				$i = 1;
				foreach ($naplate as $naplata) {
					$i++;
					$row = $naplata;
					$row[] = self::getHyperlink(self::SHEET_NAME_REZERVACIJE, $rezervacijaCount, 'A'.$i, 'K');
					$row[] = self::getHyperlink(self::SHEET_NAME_MJERE, $mjeraCount, 'C'.$i);
					$sheet->appendRow($row);
				}
			});

			$klijentCount = count($klijenti);

			$excel->sheet(self::SHEET_NAME_KLIJENT_REZERVACIJA, function($sheet) use ($klijentRezervacije, $rezervacijaCount, $klijentCount){
				$sheet->appendRow(array('Broj polaznika', 'ID rezervacije', 'Izostao', 'Predmet rezervacije', 'Klijent'));
				$i = 1;
				foreach ($klijentRezervacije as $klijentRezervacija) {
					$i++;
					$row = $klijentRezervacija;
					$row[] = self::getHyperlink(self::SHEET_NAME_REZERVACIJE, $rezervacijaCount, 'B'.$i, 'J');
					$row[] = self::getHyperlink(self::SHEET_NAME_KLIJENTI, $klijentCount, 'A'.$i);
					$sheet->appendRow($row);
				}
			});

			$excel->sheet(self::SHEET_NAME_KLIJENTI, function($sheet) use ($klijenti, $rezervacijaCount){
				$sheet->appendRow(array('Broj mobitela', 'Ime', 'Email', 'Facebook', 'Dodana', 'Zadnja promjena'));
				$i = 1;
				foreach ($klijenti as $klijent) {
					$i++;
					$row = $klijent->toArray();
					unset($row['pivot']);
					$sheet->appendRow($row);
				}
			});
		})->download($format);
	}


}
