<?php
require "./vendor/autoload.php";
use App\Methods\Pdfunctions;
session_start();
//instanciation des entités nécéssaires au programme:
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Client = new \App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);
$Keyword = new \App\Tables\Keyword($Database);
$User = new \App\Tables\User($Database);


// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) 
{
    header('location: login');
}
//sinon exécution du programme:
else 
{
    if (!empty($_POST['AjaxFT']))
    {

        //recupere le devis:
        $temp =   $Cmd->GetById($_POST['AjaxFT']);
        
        //recupere le client: 
        $clientView = $Client->getOne($temp->client__id);
        //recupere le createur du devis 
        $user = $User->getByID($clientView->client__id_vendeur);

        $userCMD = $User->getByID($temp->cmd__user__id_cmd);

        //si societe de livraison, recupere la societe de livraison:
        $societeLivraison = false ;
        if ($temp->devis__id_client_livraison) 
        {
            $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
        }
        //recupere les lignes du devis:
        $arrayOfDevisLigne = $Cmd->devisLigne($_POST['AjaxFT']);
        //cree une variable pour la date de commande du devis
        $date_time = new DateTime( $temp->cmd__date_cmd);
        //date a j+1 : expédition
       
        if (empty($temp->cmd__date_envoi) || $temp->cmd__date_envoi == null ) 
        {
            $datePlusOne =  $Cmd->updateDatePlusOne($_POST['AjaxFT']);
            $temp =   $Cmd->GetById($_POST['AjaxFT']);
            $datePlusOne = new DateTime(  $temp->cmd__date_envoi);
            $datePlusOne = $datePlusOne->format('Y-m-d');
        }
        else 
        { 
            $datePlusOne = new DateTime(  $temp->cmd__date_envoi);
            $datePlusOne = $datePlusOne->format('Y-m-d');
        }
        
       
        //formate la date pour l'utilisateur:
        $formated_date = $date_time->format('d/m/Y');
        //formatte heure minute:
        $h = $date_time->format('H');
        $m = $date_time->format('i');
     
     //a l'interieur de ob_start => contenu html du rendu:    
     ob_start();
     ?>


    <style type="text/css">
    .global-wrapper{min-height: 900px};
        .bodyFrame {font-family:Century Gothic,arial,sans-serif; }
        .div{   font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate;}
        .table{ font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate;}
        strong{ color:#000;}
        .head-wrapper{
            display: flex;
            justify-content: space-around;
            padding-left: 5%;
        }
    </style>
    <?php
   
    ?>

    <div class="global-wrapper">
        <div class="div bodyFrame mt-4" style="width: 100%;" >
         <div class="head-wrapper div">
             <div class="div" style="text-align: left;  width: 50%;" >
                <div class="div">
                    <img  style=" width:55mm;" src="public\img\recodeDevis.png"/>
                </div>
                <div class="div" style=" margin-top: 15%" >
                <p>Commandé le : 
                <?php echo $formated_date ?> à : <?php echo $h . 'h ' . $m ?></p>
                Commercial : 
                <strong>
                    <?php
                        if (!empty($user)) 
                        {
                            echo  $user->nom . ' '. $user->prenom ;
                        } 
                        else 
                        {
                            echo 'Non renseigné';
                        }
                    ?>
                </strong> 
                    <?php
                        if (!empty($user->postefix)) 
                        {
                        echo ' (Tél: '. $user->postefix .')';
                        } 

                        
                    ?>
                     
                
                    <?php
                        if (!empty($userCMD)) 
                        {
                            echo  '<br>Commandé par : <strong>'.$userCMD->nom . ' '. $userCMD->prenom ;
                        } 
                    ?>
                </strong> 
                    <?php
                        if (!empty($userCMD->postefix)) 
                        {
                        echo ' (Tél: '. $userCMD->postefix .')';
                        } 

                        if (!empty($temp->cmd__code_cmd_client)) 
                        {
                            echo '<br> code cmd client: <b>'.  $temp->cmd__code_cmd_client . '</b>';
                        }
                    ?>
             <div class="d-flex">

             <?php
             if ($temp->client__id > 10 ) 
             {
                echo "<form class='my-2 mx-2' action='garantiesFiches' method='POST'>
                <input type='hidden' name='POSTGarantie' value=". $temp->devis__id.">
                <button class='btn btn-warning btn-sm'>Fiche de garantie</button>
                </form>";
             }
                echo "<form class='my-2 mx-2' action='adminFiche' method='POST'>
                <input type='hidden' name='AdminGarantie' value=". $temp->devis__id.">
                <button class='btn btn-primary btn-sm'>Administration</button>
                </form>";
             ?>
             </div>
             
             </td>
            
             </div>
            </div>
         
            <div class="div" style="text-align: left;  width: 45% ;"><h5>Fiche de Travail N°:<strong> <?php echo $temp->devis__id ?></strong><br></h5>
             <?php 
             // si une societe de livraion est présente 
             if ($societeLivraison) 
             {
                //si un contact est présent dans l'adresse de livraison :    
                if ($temp->devis__contact_livraison) 
                {            
                    $contact2 = $Contact->getOne($temp->devis__contact_livraison);
                        echo "<br> <small>Societe : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                        echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                }
                // si pas de contact de livraison :
                else 
                {          
                    echo "<br> <small>Societe :</small><strong><br>";
                    echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                }
             }

            //Si pas de societe de livraison presente:
            else 
            {
                //si un contact est present:
                if ($temp->devis__contact__id) 
                {
                    $contact = $Contact->getOne($temp->devis__contact__id);
                    echo "<small>Societe : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom."</small><strong><br>";
                    echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }
                else
                {
                    echo "<small>Societe : </small><strong><br>";
                    echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }
            }  
           
            ?>
        
        <div class="d-flex flex-column">
            <?php
             $ajaxFT = $_POST['AjaxFT'];
          
               
            //  echo    '<form method="POST" action="pdfTravail" target="_blank">
            //  <input type="hidden" value="'.$ajaxFT.'" name="devisCommande">
            //  <button class="btn btn-link " href="pdfTravail"><i class="fas fa-file-pdf"></i> Fiche de travail</button>
            // </form>';
           
            
            ?>
            
            <?php 
            
               
                $dateForm = "<form class='mt-3 ml-2' method='POST' action='ficheTravail'>
                    <h6>Date d'envoi prévue:</h6>
                    <input type='hidden' value='".$temp->devis__id."' name='dateId'>
                    <input type='date' name='estimDate' value=".$datePlusOne.">
                    <button type='submit' class='btn btn-sm btn-success'><i class='far fa-exchange'></i></button>
                    </form>";
          
              
            
            echo $dateForm;
            ?>
           
            
        </div>

       </div>
      
      </div>
    
       
    </div>

    <div class="div" style="display: flex; justify-content: center; margin-top: 10%;">
        <table CELLSPACING=0 style="width: 95%;" class="table">
            
                <tr style=" margin-top : 50px; background-color: #dedede; vertical-align: top;" >
                    <td style="width: 5%; background-color: white; border-style: none;" ></td>
                    <td style="width: 13%; text-align: left;">Prestation<br>Type<br>Garantie</td>
                    <td style="width: 50%; text-align: left">Ref Tech<br>Désignation Client<br>Complement techniques</td>
                    <td style="text-align: right; width: 12%"><strong>CMD</strong><br>Livr</td>
                </tr>
           
                <?php
                $comand = $temp;
                if ($temp->devis__etat == 'CMD')
                {
                
                    foreach ($arrayOfDevisLigne as $item) {
                        if(intval($item->cmdl__garantie_option) > intval($item->devl__mois_garantie)) 
                        {
                        $temp = $item->cmdl__garantie_option  . ' mois';
                        } 
                        else 
                        { 
                        $temp = intval($item->devl__mois_garantie) . ' mois' ;
                        }
                        if (intval($temp) == 0) 
                        {
                            $temp = '';
                        }
                        switch ($item->groupe_famille) {
    
                            case 'CDB':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-success mt-2' value='".$item->devl__id."' '><i class='fas fa-barcode'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #e6ffe6;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #e6ffe6;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br>" .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #e6ffe6; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                                break;
                            
                            case 'CONSO':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-warning mt-2' value='".$item->devl__id."' '><i class='fas fa-sync-alt'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #fff5f0;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #fff5f0;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #fff5f0; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                                break;
                            
                            case 'ILM':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-info mt-2' value='".$item->devl__id."' '><i class='fas fa-shredder'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #e8ffff;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #e8ffff;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #e8ffff; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                            break;
    
                            case 'IT':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-danger mt-2 ' value='".$item->devl__id."''><i class='fas fa-print'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #ffeded;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #ffeded;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #ffeded; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                            break;
    
                            case 'MICRO':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-primary mt-2 ' value='".$item->devl__id."'  ' ><i class='fas fa-computer-classic'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #ebffff;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #ebffff;'><strong> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #ebffff; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                            break;
                            
                            default:
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-secondary mt-2' value='".$item->devl__id."' '><i class='fas fa-clipboard-list'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid'><strong> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                                break;
                        }
                      
                    }
                   
                }
                else 
                {

                    foreach ($arrayOfDevisLigne as $item) {
                        if(intval($item->cmdl__garantie_option) > intval($item->devl__mois_garantie)) 
                        {
                        $temp = $item->cmdl__garantie_option . ' mois';
                        } 
                        else 
                        { 
                        $temp = intval($item->devl__mois_garantie) . ' mois';
                        }
                        if (intval($temp) == 0) 
                        {
                            $temp = '';
                        }
                        switch ($item->groupe_famille) {
    
                            case 'CDB':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-success mt-2' value='".$item->devl__id."' ' disabled><i class='fas fa-barcode' ></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #e6ffe6;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #e6ffe6;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br>" .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #e6ffe6; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                                break;
                            
                            case 'CONSO':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-warning mt-2' value='".$item->devl__id."' ' disabled><i class='fas fa-sync-alt'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #fff5f0;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #fff5f0;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #fff5f0; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                                break;
                            
                            case 'ILM':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-info mt-2' value='".$item->devl__id."' ' disabled><i class='fas fa-shredder'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #e8ffff;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #e8ffff;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #e8ffff; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                            break;
    
                            case 'IT':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-danger mt-2 ' value='".$item->devl__id."'' disabled><i class='fas fa-print'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #ffeded;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #ffeded;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #ffeded; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                            break;
    
                            case 'MICRO':
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-primary mt-2 ' value='".$item->devl__id."'  ' disabled><i class='fas fa-computer-classic'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #ebffff;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid; background-color: #ebffff;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #ebffff; '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                            break;
                            
                            default:
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFT btn btn-secondary mt-2' value='".$item->devl__id."' ' disabled><i class='fas fa-clipboard-list'></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ."  </td>
                                <td style='border-bottom: 1px #ccc solid'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                                break;
                        }
                      
                    }
                    

                }
                
                
                ?>
        </table> 
     </div>
     <div class="d-flex justify-content-end mr-3">
     <?php
    
            if (!empty($comand->devis__note_interne)) 
            {
                echo $comand->devis__note_interne;
            } else echo '<p><b>pas de commentaire interne</b></p>';    
     ?>
     </div>
    
    
     </div>
     
    

 
    <?php



        //try catch pour l'envoi de la reponse:
        try 
        {
            //recupere tout le contenu HTMl depuis ob_start:
            $content = ob_get_contents();

            //enregistre ou reecris le fichier de vision au nom du user + FT.html       
            $myfile = fopen(__DIR__ .'/'.$_SESSION['user']->log_nec.'FT.html', "w") ;
            $txt = $content;
            fwrite($myfile, $txt);
            fclose($myfile);
            //vide le contenu ob:       
            ob_clean();
            
            //renvoi la data du devis en response JSOn !!! attention renvoi d'un array [0] pour html [1] pour ckeditor
            echo  json_encode($content);
        } 
        // si une exeption est attrapée
        catch (Exception $e) 
        {
            //renvoi l'erreur au format JSOn:
            echo  json_encode( $e->getMessage('fuck'));
        }       
    
    //fin IF check le POST  
    }
 //fin IF check la connexion
 }