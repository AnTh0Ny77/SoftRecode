<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use \GuzzleHttp\Client;
use \GuzzleHttp\ClientException;
use App\Tables\User;

class SeoController extends BasicController {

    public static function home(){
        self::init();
        self::security();
        
        $domaine = 'recode.fr';
        $periode =  '23/01 au 22/04 2023';

        $array_principal = [ 'Tablette Zebra' , 'PDA Zebra' , 'PDA DATALOGIC' , 'PDA Honeywell' ,  
        'Terminaux mobile Zebra' ,  'Terminaux mobile Motorola' ,  'Terminaux mobile Datalogic' , 
        'Terminaux mobile Psion' , 'Lecteur code barres Zebra' , 'Lecteur code barres Motorola' ,
        'Terminal embarqué Zebra' , 'Imprimante etiquette Intermec' ];

        if($_SESSION['user']->user__facture_acces < 15 ) { header('location: noAccess'); }

        $dataSea = self::lire_csv('sea.csv');
        $dataSeoPrincipal = self::csvToTable('seo.csv' ,  $array_principal);
        $totauxSeo = self::getClicsImpressionsPosition('seo.csv' ,   $array_principal);

        return self::$twig->render(
            'seo.html.twig',[
                'user' => $_SESSION['user'] , 
                'domaine' => $domaine ,
                'seaData' => $dataSea ,
                'periode' => $periode , 
                'seoPrincipal' => $dataSeoPrincipal , 
                'totauxSeo' => $totauxSeo
            ]
        );
    }


    public static function csvToTable($csvFilePath, $validQueries) {
        $csvFile = fopen($csvFilePath, 'r');
        $headers = fgetcsv($csvFile); // Récupère la première ligne (les titres de colonnes)
        $headers = array_map(function($header) {
            return $header === "Requêtes les plus fréquentes" ? "rqt" : $header;
        }, $headers);
        $table = array();
        $validQueriesData = array_fill_keys($validQueries, '');
    
        while ($row = fgetcsv($csvFile)) {
            // Vérifie si la requête est valide en ignorant la casse
            $query = strtolower($row[0]);
            // if (!in_array($query, array_map('strtolower', $validQueries))) {
            //     continue; // Ignore cette ligne
            // }
            $rowData = array();
            foreach ($headers as $i => $header) {
                if ($header === "rqt") {
                    $rowData[$header] = $row[0];
                } else if ($header !== "CTR") {
                    $rowData[$header] = $row[$i];
                }
            }
            unset($validQueriesData[$query]); // Supprime la requête valide de la liste
            $table[] = $rowData;
        }
    
        fclose($csvFile);
        foreach ($validQueriesData as $query => $emptyValue) {
            $rowData = array("rqt" => strtolower($query));
            foreach (array_slice($headers, 1) as $header) { // Ignorer la colonne "rqt"
                $rowData[$header] = 0;
            }
            $table[] = $rowData;
        }
       
        return $table;
    }   

    public static function getClicsImpressionsPosition($csvFilePath, $validQueries) {
        $csvFile = fopen($csvFilePath, 'r');
        $headers = fgetcsv($csvFile); // Récupère la première ligne (les titres de colonnes)
        $clics = 0;
        $impressions = 0;
        $positionSum = 0;
        $rowCount = 0;
        while ($row = fgetcsv($csvFile)) {
            $query = strtolower($row[0]);
            if (!in_array($query, array_map('strtolower', $validQueries))) {
                $clics += $row[1];
                $impressions += $row[2];
                $positionSum += floatval(str_replace(',', '.', $row[4]));
                $rowCount++;
            }
        }
        fclose($csvFile);
        $positionAvg = $rowCount > 0 ? $positionSum / $rowCount : 0;
        $positionAvg = number_format($positionAvg, 2);
        return array("clics" => $clics, "impressions" => $impressions, "positionAvg" => $positionAvg);
    }

    public static function prepareSqlQuery($file_path) {
        $query = "INSERT INTO table_name (mat__cl__id, mat__type, mat__marque, mat__model, mat__sn, mat__kw_tg, mat__actif) VALUES ";
        $handle = fopen($file_path, "r");
        if ($handle) {
            $first_line = true;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($first_line) {
                    $first_line = false;
                    continue;
                }
                $query .= "('35412', 'PDA',";
                $columns = array();
                for ($i = 0; $i < count($data); $i++) {
                    if ($i == 2 || $i == 3 || $i == 5 || $i == 11 || $i == 16) {
                        if ($data[$i] === '') {
                            $columns[] = "NULL";
                        } else {
                            $columns[] = "'" . str_replace("'", "\'", $data[$i]) . "'";
                        }
                    }
                }
                $query .= implode(",", $columns) . ",'GNO', '1'),";
            }
            fclose($handle);
            $query = rtrim($query, ",");
            $query .= ";";
            return $query;
        }
    }

    public static function lire_csv($nom_fichier){
        $tableau = array();
        if (($fichier = fopen($nom_fichier, "r")) !== FALSE) {
            $ligne = 0; // compteur de ligne
            while (($donnees = fgetcsv($fichier, 1000, ",")) !== FALSE) {
                if ($ligne != 0) { // sauter la première ligne
                    // Sélectionne les colonnes 0, 5, 6 et 7
                    $donnees_selectionnees = array($donnees[0], $donnees[5], $donnees[6], $donnees[7]);
                    $tableau[] = $donnees_selectionnees;
                }
                $ligne++;
            }
            fclose($fichier);
        }
        return $tableau;
    }

}