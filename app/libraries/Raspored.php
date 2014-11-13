<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Helpers;

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

    /**
     * Dani u tjednu
     */
    public static $dani = array(1 => 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota', 'Nedjelja');

    /**
     * gets a raspored column with hours
     * @return string
     */
    public static function HoursColumn($widthPercent) {
        $response = '<div class = "raspored-column" style="width:'.$widthPercent.'%;">';
        $response .= '<div class = "raspored-heading">Vrijeme</div>';
        for ($hour = \BaseController::START_HOUR; $hour < \BaseController::END_HOUR; $hour++) {
            $response .= '<div class = "raspored-vrijeme" style="height:' .
                    (self::HEIGHT_15_MIN * 4) . 'px;"><p>' . $hour . ':00</p></div>';
        }
        $response .= '</div>';
        return $response;
    }

    /**
     * gets a raspored column with hours
     * @return string
     */
    public static function Blocks2HTML($blocks) {
        $diff = \BaseController::END_HOUR - \BaseController::START_HOUR;
        $response = '<div class = "raspored-blocks" style="height:'.
                ($diff*4*self::HEIGHT_15_MIN).'px">';
        for($i = 0; $i < $diff; $i += 2){
            $response .= '<hr style="top:'.(self::HEIGHT_15_MIN*4*$i).'px;"/>';
        }
        foreach ($blocks as $key => $block) {
            if (!is_int($key)) {
                continue;
            }
            $response .= '<div class = "raspored-block" style="background-color:#' . $block['boja']
                    . ';height:' . (self::HEIGHT_15_MIN * $block['span']) . 'px;top: ' .
                    (self::HEIGHT_15_MIN * $block['offset']) . 'px;">' . $block['rezervacija'] . '<br/>' . $block['extra'] .
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
    public static function DayHeading($dayNumber, $formatedDate) {
        $response = '<div class = "raspored-heading">';
        $response .= self::$dani[$dayNumber];
        $response .= '<br/><small>'.$formatedDate.'</small>';
        $response .= '</div>';
        return $response;
    }

    /**
     * 
     * @param \Ucionica $ucionica
     * @return string
     */
    public static function UcionicaHeading($ucionica) {
        $response = '<div class = "raspored-heading">';
        $response .= $ucionica->link();
        $response .= '</div>';
        return $response;
    }

    /**
     * Gets all reservations in specified week for specified user.
     * The key is the day number in week, and the value is an array of
     * arrays with offset, span, rezervacija, boja and instruktor each. 
     * @param int $instruktor User
     * @param int $week ISO week number
     * @param int $year Year
     * @return array
     */
    private static function RezervacijeForUserInWeek($instruktor, $week, $year) {
        $time = new \DateTime();
        $time->setTime(0, 0);
        $time->setISODate($year, $week);
        $min = $time->format('Y-m-d H:i:s');
        $max = $time->modify('+1 week')->format('Y-m-d H:i:s');
        $rezervacije = \Rezervacija::with('mjera', 'predmet', 'ucionica')
                ->where('instruktor_id', '=', $instruktor->id)
                ->whereBetween('pocetak_rada', array($min, $max))
                ->get();

        $data = array();
        $time->setISODate($year, $week, 0);
        for ($i = 0; $i < 7; $i++) {
            $data[$i + 1] = array('formatedDate' => $time->modify('+1 day')->format('d.m.Y'));
        }

        foreach ($rezervacije as $r) {
            $pocetak = strtotime($r->pocetak_rada);
            $kraj = strtotime($r->kraj_rada());
            $key = date('N', $pocetak);
            $data[$key][] = array(
                'offset' => (int) (((date('H', $pocetak) - \BaseController::START_HOUR) * 60 + date('i', $pocetak)) / 15),
                'span' => (int) (($kraj - $pocetak) / 60 / 15),
                'rezervacija' => $r->link(),
                'extra' => $r->ucionica->link(),
                'boja' => $instruktor->boja
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
        $widthPercent = 100/8;
        $response = '<div class = "raspored">';
        $response .= self::HoursColumn($widthPercent/2);
        foreach (self::RezervacijeForUserInWeek(\User::find($user_id), $week, $year) as $dayNumber => $blocks) {
            $response .= '<div class = "raspored-column"  style="width:'.$widthPercent.'%;">';
            $response .= self::DayHeading($dayNumber, $blocks['formatedDate']);
            $response .= self::Blocks2HTML($blocks);
            $response .= '</div>';
            if ($dayNumber == 5) {
                $response .= self::HoursColumn($widthPercent / 2);
            }
        }
        $response .= '</div>';
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
            $kraj = strtotime($r->kraj_rada());
            $key = date('N', $pocetak);
            $data[$key][] = array(
                'offset' => (int) (((date('H', $pocetak) - \BaseController::START_HOUR) * 60 + date('i', $pocetak)) / 15),
                'span' => (int) (($kraj - $pocetak) / 60 / 15),
                'rezervacija' => $r->link(),
                'extra' => $r->instruktor->link(),
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
        $widthPercent = 100/8;
        $response = '<div class = "raspored">';
        $response .= self::HoursColumn($widthPercent/2);
        foreach (self::RezervacijeForUcionicaInWeek($ucionicaId, $week, $year) as $dayNumber => $blocks) {
            $response .= '<div class = "raspored-column" style="width:'.$widthPercent.'%;">';
            $response .= self::DayHeading($dayNumber, $blocks['formatedDate']);
            $response .= self::Blocks2HTML($blocks);
            $response .= '</div>';
            if ($dayNumber == 5) {
                $response .= self::HoursColumn($widthPercent / 2);
            }
        }
        $response .= '</div>';
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

        $ucionice = \Ucionica::all();
        $data = array();
        foreach ($ucionice as $ucionica) {
            $data[$ucionica->id] = array('ucionica' => $ucionica);
        }

        foreach ($rezervacije as $r) {
            $pocetak = strtotime($r->pocetak_rada);
            $kraj = strtotime($r->kraj_rada());
            $key = $r->ucionica_id;
            $data[$key][] = array(
                'offset' => (int) (((date('H', $pocetak) - \BaseController::START_HOUR) * 60 + date('i', $pocetak)) / 15),
                'span' => (int) (($kraj - $pocetak) / 60 / 15),
                'rezervacija' => $r->link(),
                'extra' => $r->instruktor->link(),
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
        $widthPercent = 100/(\Ucionica::count()+1);
        $response = '<div class = "raspored">';
        $dto = new \DateTime();
        $dto->setISODate($year, $week, $day);
        $response .= '<p>'.self::$dani[$day].', '.$dto->format('d.m.Y').'</p>';
        $response .= self::HoursColumn($widthPercent/2);
        foreach (self::RezervacijeForDay($day, $week, $year) as $blocks) {
            $response .= '<div class = "raspored-column"  style="width:'.$widthPercent.'%">';
            $response .= self::UcionicaHeading($blocks['ucionica']);
            $response .= self::Blocks2HTML($blocks);
            $response .= '</div>';
        }
        $response .= self::HoursColumn($widthPercent/2);
        $response .= '</div>';
        return $response;
    }

}
