<?php
namespace App;

interface HolidayFinderContextInterface {
    public function getHolidays(int $year ): array;
}

class HollidaysFinder implements HolidayFinderContextInterface {

    private string $country;

    public function __construct(string $country) {
        $this->setCountry($country);
    }

    public function setCountry(string $country): void {
        $this->country = $country;
    }

    public function getCountry(): string {
        return $this->country;
    }

    public function getHolidays(int $year ): array {
        switch ( $this->getCountry()) {
            case 'fr':
                return $this->FrHolidays($year);
            default:
                return $this->FrHolidays($year);
        }
    }

    public function FrHolidays(int $year){
        $easterDate = easter_date($year);
        $easterDay = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear = date('Y', $easterDate);
        $holidays = array(
            date("Y-m-d",mktime(0, 0, 0, 1, 1, $year)),// 1er janvier
            date("Y-m-d",mktime(0, 0, 0, 5, 1, $year)),// Fete du travail
            date("Y-m-d",mktime(0, 0, 0, 5, 8, $year)),// Victoire des allies
            date("Y-m-d",mktime(0, 0, 0, 7, 14, $year)),// Fete nationale
            date("Y-m-d",mktime(0, 0, 0, 8, 15, $year)),// Assomption
            date("Y-m-d",mktime(0, 0, 0, 11, 1, $year)),// Toussaint
            date("Y-m-d",mktime(0, 0, 0, 11, 11, $year)),// Armistice
            date("Y-m-d",mktime(0, 0, 0, 12, 25, $year)),// Noel
            date("Y-m-d",mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear)),// Lundi de paques
            date("Y-m-d",mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)),// Ascension
        );
        sort($holidays);
        return $holidays;
    }

    public function getDifference($startDate,$endDate){
           $startYear = substr($startDate, 0, 4);
           $endYear   = substr($endDate, 0, 4);
           $holidays  = array();

           for ($iYear = $startYear; $iYear <= $endYear; $iYear++){
               $holidays = array_merge( $holidays, $this->getHolidays($iYear));
           }
          
           $nb_days = round((strtotime($endDate) - strtotime($startDate))/(60*60*24));
           
           for ($i = strtotime($startDate); $i < strtotime($endDate); $i += 86400){
               $iDayNum = date('N',$i); 
               if (in_array(date('Y-m-d', $i), $holidays) OR $iDayNum == 6 OR $iDayNum == 7) 
                   $nb_days -= 1;
           }
           return (integer) $nb_days;
       }
   

}