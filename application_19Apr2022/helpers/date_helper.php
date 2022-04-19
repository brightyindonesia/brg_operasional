<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function hari($day) {
        $hari = $day;
  
        switch ($hari) {
            case "Sun":
                $hari = "Minggu";
                break;
            case "Mon":
                $hari = "Senin";
                break;
            case "Tue":
                $hari = "Selasa";
                break;
            case "Wed":
                $hari = "Rabu";
                break;
            case "Thu":
                $hari = "Kamis";
                break;
            case "Fri":
                $hari = "Jum'at";
                break;
            case "Sat":
                $hari = "Sabtu";
                break;
			case 'Sunday':
				$hari = 'Minggu';
				break;
			case 'Monday':
				$hari = 'Senin';
				break;
			case 'Tuesday':
				$hari = 'Selasa';
				break;
			case 'Wednesday':
				$hari = 'Rabu';
				break;
			case 'Thursday':
				$hari = 'Kamis';
				break;
			case 'Friday':
				$hari = 'Jum\'at';
				break;
			case 'Saturday':
				$hari = 'Sabtu';
				break;
			default:
				$hari = 'Tidak ada';
				break;
        }
        return $hari;
    }

function standard_date_format($str) {
    preg_match_all('/(\d{1,2}) (\w+) (\d{4})/', $str, $matches);
    foreach ( $matches[1] as $day   ) { $days  [] = $day;   }
    foreach ( $matches[2] as $month ) { $months[] = $month; }
    foreach ( $matches[3] as $year  ) { $years [] = $year;  }

    $all_months_en = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $all_months_ina = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

    for ($i = sizeof ($days) - 1; $i >= 0; $i--) {
        $month     = array_search ($months[$i], $all_months) + 1;
        $month     = strlen ($month) < 2 ? '0'.$month : $month; 
        $results[] = $years[$i] . '-' . $month . '-' . $days[$i];
    }
    return  $results;
}