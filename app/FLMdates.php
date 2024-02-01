<?php


function getHolidays($year = null)
    {
        if ($year === null)
        {
            $year = intval(strftime('%Y'));
        }
        $easterDate = easter_date($year);
        $easterDay = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear = date('Y', $easterDate);
        $holidays = array(
            // Jours feries fixes
            date("Y-m-d",mktime(0, 0, 0, 1, 1, $year)),// 1er janvier
            date("Y-m-d",mktime(0, 0, 0, 5, 1, $year)),// Fete du travail
            date("Y-m-d",mktime(0, 0, 0, 5, 8, $year)),// Victoire des allies
            date("Y-m-d",mktime(0, 0, 0, 7, 14, $year)),// Fete nationale
            date("Y-m-d",mktime(0, 0, 0, 8, 15, $year)),// Assomption
            date("Y-m-d",mktime(0, 0, 0, 11, 1, $year)),// Toussaint
            date("Y-m-d",mktime(0, 0, 0, 11, 11, $year)),// Armistice
            date("Y-m-d",mktime(0, 0, 0, 12, 25, $year)),// Noel
            // Jour feries qui dependent de paques
            date("Y-m-d",mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear)),// Lundi de paques
            date("Y-m-d",mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)),// Ascension
          //date("Y-m-d",mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)), // Pentecote n'est plus un jour de congé  :-(
        );
        sort($holidays);
        return $holidays;
    }

    //The function returns the no. of business days between two dates and it skeeps the holidays
    function getWorkingDays($startDate,$endDate)
    {
        //Example: difference entre 2 dates
        //  echo getWorkingDays("2006-12-22","2007-01-06")
        // => will return 8
        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $startYear = substr($startDate, 0, 4);
        $endYear   = substr($endDate, 0, 4);
        $holidays  = array(); // Si l'interval demande chevauche plusieurs annees on doit avoir les jours feries de toutes ces annees
        for ($iYear = $startYear; $iYear <= $endYear; $iYear++)
        {
            $holidays = array_merge( $holidays, getHolidays($iYear));
        }
       
        $nb_days = round((strtotime($endDate) - strtotime($startDate))/(60*60*24));
        for ($i = strtotime($startDate); $i < strtotime($endDate); $i += 86400)
        {
            $iDayNum = date('N',$i); // Numero du jour de la semaine, de 1 pour lundi a 7 pour dimanche
            if (in_array(date('Y-m-d', $i), $holidays) OR $iDayNum == 6 OR $iDayNum == 7) // Si c'est ferie ou samedi ou dimanche, on soustrait le nombre de secondes dans une journee.
                $nb_days -= 1;
        }
        return (integer) $nb_days;
    }

    //The function returns the working day after startdate and it skeeps the holidays
    function NextWorkingDays($startDate)
    {
        $startYear = substr($startDate, 0, 4);
        $endYear   = $startYear + 1;
        $holidays  = array(); // Si l'interval demande chevauche plusieurs annees on doit avoir les jours feries de toutes ces annees
        for ($iYear = $startYear; $iYear <= $endYear; $iYear++)
        {
            $holidays = array_merge( $holidays, getHolidays($iYear));
        }
        $startTimeStamp = strtotime($startDate)+86400; // lendemain de la date de depart
        $endTimeStamp   = $startTimeStamp + 86400*5; // il est impossible d'avoir 5 jours de suite de WE ou jour feriés
        for ($i = $startTimeStamp; $i < $endTimeStamp; $i += 86400)
        {
            $iDayNum = date('N',$i); // Numero du jour de la semaine, de 1 pour lundi a 7 pour dimanche
            if (!in_array(date('Y-m-d', $i), $holidays) AND $iDayNum <> 6 AND $iDayNum <> 7) // pas ferie ni samedi ni dimanche
                break;
        }
        return date('Y-m-d', $i);
    }

