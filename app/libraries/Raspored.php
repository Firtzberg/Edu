<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Helpers;

use Illuminate\Support\Facades\Auth;
use Permission;

/**
 * Description of Rapored
 *
 * @author Hrvoje
 */
class Raspored {

    /**
     * height of 15 minutes in the display
     */
    const HEIGHT_15_MIN = 12;
    const MIN_COLUMN_WIGHT = 150;

    /**
     * Dani u tjednu
     */
    public static $dani = array(1 => 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota', 'Nedjelja');

    /**
     * gets a raspored column with hours
     * @return string
     */
    public static function HoursColumn($left) {
        $response = '<div class = "raspored-time col-lg-1 col-md-1 col-sm-2 col-xs-3'.($left?'':' hidden-xs').'">';
        $response .= '<div class = "raspored-heading">Vrijeme</div>';
        for ($hour = \BaseController::START_HOUR; $hour < \BaseController::END_HOUR; $hour++) {
            $response .= '<div class = "raspored-vrijeme" style="height:' .
                    (self::HEIGHT_15_MIN * 4) . 'px;"><p>' . $hour . ':00</p></div>';
        }
        $response .= '<div class = "raspored-heading">Vrijeme</div>';
        $response .= '</div>';
        return $response;
    }

    /**
     * gets a raspored column with hours
     * @return string
     */
    public static function Blocks2HTML($blocks, $day, $week, $year, $ucionica_id = null, $instruktor_id = null) {
        $time = new \DateTime();
        $time->setTime(0, 0);
        $time->setISODate($year, $week, $day);
        $datum = $time->format('Y-m-d');
        $diff = \BaseController::END_HOUR - \BaseController::START_HOUR;
        $response = '<div class = "raspored-blocks" style="height:'.
                ($diff*4*self::HEIGHT_15_MIN).'px" datum="'.$datum.'" ucionica-id="'.$ucionica_id.'"';
        if (Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING)) {
            $response .= ' instruktor-id="'.$instruktor_id.'"';
        }
        $response .= '>';
        for($i = 0; $i < $diff; $i ++){
            $response .= '<hr style="top:'.(self::HEIGHT_15_MIN*4*$i).'px;"/>';
        }
        foreach ($blocks as $key => $block) {
            if (!is_int($key)) {
                continue;
            }
            $response .= '<div style="background-color:#' . $block['boja']
                    . ';height:' . (self::HEIGHT_15_MIN * $block['span']) . 'px;top: ' .
                    (self::HEIGHT_15_MIN * $block['offset']) . 'px;" onclick="if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;">' . $block['rezervacija'] . '<br/>' . $block['extra'] .
                    '</div>';
        }
        $response .= '</div>';
        return $response;
    }

    /**
     * 
     * @param int $dayNumber
     * @return string
     */
    public static function DayHeading($dayNumber, $formatedDate, $week, $year) {
        $response = '<div class = "raspored-heading">';
        $response .= link_to_route('home.raspored', self::$dani[$dayNumber], array('day' => $dayNumber, 'week' => $week, 'year' => $year));
        $response .= '<br/><small>'.$formatedDate.'</small>';
        $response .= '</div>';
        return $response;
    }

    /**
     * 
     * @param \Ucionica $ucionica
     * @return string
     */
    public static function UcionicaHeading($ucionica, $tjedan = null, $godina = null) {
        $response = '<div class = "raspored-heading">';
        if ($ucionica)
        {
            $response .= $ucionica->link($tjedan, $godina);
        }
        else
        {
            $response .= "Uklonjena učionica";
        }
        $response .= '</div>';
        return $response;
    }

    /**
     * Gets all reservations in specified week for specified user.
     * The key is the day number in week, and the value is an array of
     * arrays with offset, span, rezervacija, boja and instruktor each. 
     * @param int $djelatnik User
     * @param int $week ISO week number
     * @param int $year Year
     * @return array
     */
    private static function RezervacijeForUserInWeek($djelatnik, $week, $year) {
        $time = new \DateTime();
        $time->setTime(0, 0);
        $time->setISODate($year, $week);
        $min = $time->format('Y-m-d H:i:s');
        $max = $time->modify('+1 week')->format('Y-m-d H:i:s');
        $rezervacije = \Rezervacija::with('mjera', 'predmet', 'ucionica')
                ->where('instruktor_id', '=', $djelatnik->id)
                ->whereBetween('pocetak_rada', array($min, $max))
                ->get();

        $data = array();
        $time->setISODate($year, $week, 0);
        for ($i = 0; $i < 7; $i++) {
            $data[$i + 1] = array('formatedDate' => $time->modify('+1 day')->format('d.m.Y'));
        }

        foreach ($rezervacije as $r) {
            $pocetak = strtotime($r->pocetak_rada);
            $kraj = strtotime($r->kraj_rada);
            $key = date('N', $pocetak);
            $extra = ($r->ucionica?$r->ucionica->link($week, $year):"Uklonjena učionica");
            $extra .= ' (';
            if ($r->tecaj) {
                $extra .= $r->tecaj_broj_polaznika;
            } else {
                $extra .= $r->klijenti()->count();
                $missed = $r->klijenti()->where('missed', 1)->count();
                if ($missed) {
                    $extra .= '-'.$missed;
                }
            }
            $extra .= ')';
            $data[$key][] = array(
                'offset' => (int) (((date('H', $pocetak) - \BaseController::START_HOUR) * 60 + date('i', $pocetak)) / 15),
                'span' => (int) (($kraj - $pocetak) / 60 / 15),
                'rezervacija' => $r->link(),
                'extra' => $extra,
                'boja' => $djelatnik->boja
            );
        }
        return $data;
    }

    /**
     * Returns whole raspored for specified ucionica. Each day has one column.
     * @param int $user_id Id of user
     * @param int $week ISO week number
     * @param int $year Year
     * @return string
     */
    public static function RasporedForUserInWeek($user_id, $week, $year) {
        $count = 7;
        $widthPercent = 100/$count;
        $response = '<div class = "raspored container-fluid"><div class = "row">';
        $response .= self::HoursColumn(true);
        $response .= '<div class = "raspored-scroller col-lg-10 col-md-10 col-sm-8 col-xs-9">';
        $response .= '<div style="min-width:'.($count*self::MIN_COLUMN_WIGHT).'px;">';
        foreach (self::RezervacijeForUserInWeek(\User::find($user_id), $week, $year) as $dayNumber => $blocks) {
            $response .= '<div style="width:'.$widthPercent.'%;">';
            $response .= self::DayHeading($dayNumber, $blocks['formatedDate'], $week, $year);
            $response .= self::Blocks2HTML($blocks, $dayNumber, $week, $year, '', $user_id);
            $response .= self::DayHeading($dayNumber, $blocks['formatedDate'], $week, $year);
            $response .= '</div>';
        }
        $response .= '</div></div>';
        $response .= self::HoursColumn(false);
        $response .= '</div></div>';
        return $response;
    }

    /**
     * Gets all reservations in specified week for specified ucionica.
     * The key is the day number in week, and the value is an array of
     * arrays with offset, span, rezervacija, boja and instruktor each. 
     * @param int $ucionicaId Id of ucionica
     * @param int $week ISO week number
     * @param int $year Year
     * @return array
     */
    private static function RezervacijeForUcionicaInWeek($ucionicaId, $week, $year) {
        $time = new \DateTime();
        $time->setTime(0, 0);
        $time->setISODate($year, $week);
        $min = $time->format('Y-m-d H:i:s');
        $max = $time->modify('+1 week')->format('Y-m-d H:i:s');
        $rezervacije = \Rezervacija::with('mjera', 'predmet', 'instruktor')
                ->where('ucionica_id', '=', $ucionicaId)
                ->whereBetween('pocetak_rada', array($min, $max))
                ->get();

        $data = array();
        $time->setISODate($year, $week, 0);
        for ($i = 0; $i < 7; $i++) {
            $data[$i + 1] = array('formatedDate' => $time->modify('+1 day')->format('d.m.Y'));
        }

        foreach ($rezervacije as $r) {
            $pocetak = strtotime($r->pocetak_rada);
            $kraj = strtotime($r->kraj_rada);
            $key = date('N', $pocetak);
            $extra = $r->instruktor->link($week, $year);
            $extra .= ' (';
            if ($r->tecaj) {
                $extra .= $r->tecaj_broj_polaznika;
            } else {
                $extra .= $r->klijenti()->count();
                $missed = $r->klijenti()->where('missed', 1)->count();
                if ($missed) {
                    $extra .= '-'.$missed;
                }
            }
            $extra .= ')';
            $data[$key][] = array(
                'offset' => (int) (((date('H', $pocetak) - \BaseController::START_HOUR) * 60 + date('i', $pocetak)) / 15),
                'span' => (int) (($kraj - $pocetak) / 60 / 15),
                'rezervacija' => $r->link(),
                'extra' => $extra,
                'boja' => $r->instruktor->boja
            );
        }
        return $data;
    }

    /**
     * Returns whole raspored for specified ucionica. Each day has one column.
     * @param int $ucionicaId Id of ucionica
     * @param int $week ISO week number
     * @param int $year Year
     * @return string
     */
    public static function RasporedForUcionicaInWeek($ucionicaId, $week, $year) {
        $count = 7;
        $widthPercent = 100/$count;
        $response = '<div class = "raspored container-fluid"><div class = "row">';
        $response .= self::HoursColumn(true);
        $response .= '<div class = "raspored-scroller col-lg-10 col-md-10 col-sm-8 col-xs-9">';
        $response .= '<div style="min-width:'.($count*self::MIN_COLUMN_WIGHT).'px;">';
        foreach (self::RezervacijeForUcionicaInWeek($ucionicaId, $week, $year) as $dayNumber => $blocks) {
            $response .= '<div style="width:'.$widthPercent.'%;">';
            $response .= self::DayHeading($dayNumber, $blocks['formatedDate'], $week, $year);
            $response .= self::Blocks2HTML($blocks, $dayNumber, $week, $year, $ucionicaId);
            $response .= self::DayHeading($dayNumber, $blocks['formatedDate'], $week, $year);
            $response .= '</div>';
        }
        $response .= '</div></div>';
        $response .= self::HoursColumn(false);
        $response .= '</div></div>';
        return $response;
    }

    /**
     * Gets all reservations in specified day for all ucionice.
     * The key is the id of the ucionica, and the value is an array of
     * arrays with offset, span, rezervacija, boja and instruktor each. 
     * @param int $day Offset from first day
     * @param int $week ISO week number
     * @param int $year year
     * @return array
     */
    private static function RezervacijeForDay($day, $week, $year) {
        $time = new \DateTime();
        $time->setTime(0, 0);
        $time->setISODate($year, $week, $day);
        $min = $time->format('Y-m-d H:i:s');
        $max = $time->modify('+1 day')->format('Y-m-d H:i:s');
        $rezervacije = \Rezervacija::with('mjera', 'predmet', 'instruktor')
                ->whereBetween('pocetak_rada', array($min, $max))
                ->get();

        $ucionice = \Ucionica::orderBy('polozaj')->get();
        $data = array();
        foreach ($ucionice as $ucionica) {
            $data[$ucionica->id] = array('ucionica' => $ucionica);
        }

        foreach ($rezervacije as $r) {
            $pocetak = strtotime($r->pocetak_rada);
            $kraj = strtotime($r->kraj_rada);
            $key = $r->ucionica_id;
            $extra = $r->instruktor->link($week, $year);
            $extra .= ' (';
            if ($r->tecaj) {
                $extra .= $r->tecaj_broj_polaznika;
            } else {
                $extra .= $r->klijenti()->count();
                $missed = $r->klijenti()->where('missed', 1)->count();
                if ($missed) {
                    $extra .= '-'.$missed;
                }
            }
            $extra .= ')';
            $data[$key][] = array(
                'offset' => (int) (((date('H', $pocetak) - \BaseController::START_HOUR) * 60 + date('i', $pocetak)) / 15),
                'span' => (int) (($kraj - $pocetak) / 60 / 15),
                'rezervacija' => $r->link(),
                'extra' => $extra,
                'boja' => $r->instruktor->boja
            );
        }
        return $data;
    }

    /**
     * Returns whole raspored for specified day. Each ucionica has one column.
     * @param int $day day of week
     * @param int $week ISO week number
     * @param int $year Year
     * @return string
     */
    public static function RasporedForDay($day, $week, $year) {
        $rezervacije = self::RezervacijeForDay($day, $week, $year);
        $count = count($rezervacije);
        $widthPercent = 100/$count;
        $response = '<div class = "raspored container-fluid">';
        $dto = new \DateTime();
        $dto->setISODate($year, $week, $day);
        $response .= '<p>'.self::$dani[$day].', '.$dto->format('d.m.Y').'</p>';
        $response .= '<div class = "row">';
        $response .= self::HoursColumn(true);
        $response .= '<div class = "raspored-scroller col-lg-10 col-md-10 col-sm-8 col-xs-9">';
        $response .= '<div style="min-width:'.($count*self::MIN_COLUMN_WIGHT).'px;">';
        foreach ($rezervacije as $blocks) {
            $response .= '<div class = "raspored-column"  style="width:'.$widthPercent.'%">';
            if (isset($blocks['ucionica'])) {
                $response .= self::UcionicaHeading($blocks['ucionica'], $week, $year);
            }
            else {
                $response .= self::UcionicaHeading(null);
            }
            $response .= self::Blocks2HTML($blocks, $day, $week, $year, isset($blocks['ucionica']) ? $blocks['ucionica']->id : null);
            if (isset($blocks['ucionica'])) {
                $response .= self::UcionicaHeading($blocks['ucionica'], $week, $year);
            }
            else {
                $response .= self::UcionicaHeading(null);
            }
            $response .= '</div>';
        }
        $response .= '</div></div>';
        $response .= self::HoursColumn(false);
        $response .= '</div></div>';
        return $response;
    }

}
