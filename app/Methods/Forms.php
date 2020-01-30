<?php

namespace App\Methods;

 class Forms {

    public static function societeValidate($forms){
            if (!empty($forms['societe']) && !empty($forms['cp']) &&  !empty($forms['ville'])){
            return array(
                "societe"=>$forms['societe'],
                'adr1'=> $forms['adr1'],
                'adr2'=> $forms["adr2"],
                'cp'=>  $forms["cp"],
                'ville'=> $forms['ville']    
            );}
            else {
                return false ;
                header('location : nouveauDevis');
            }
    }

    public static function contactValidate($forms){
        if (!empty($forms['fonctionContact'])){

            if (!empty($forms['mailContact']) && !filter_var($forms['mailContact'],FILTER_VALIDATE_EMAIL )) {
                return false ;
                header('location : nouveauDevis');
            }
            elseif (empty( $forms['civiliteContact']) && empty( $forms['nomContact']) && empty( $forms['telContact'])  
            && empty( $forms['faxContact'])  && empty( $forms['maiContact']) )  {
                return 206;
            }
            else{
            return array(
            'fonctionContact'=> $forms['fonctionContact'],
            'civiliteContact'=> $forms['civiliteContact'],
            'nomContact'=> $forms['nomContact'],
            'prenomContact'=> $forms['prenomContact'],
            'telContact'=> $forms['telContact'],
            'faxContact'=> $forms['faxContact'],
            'mailContact'=> $forms['mailContact']
            );
        }}
        else {
            return false ;
            header('location : nouveauDevis');
        }
    }

}