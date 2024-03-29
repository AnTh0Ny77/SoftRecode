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



if (empty($_SESSION['user']->id_utilisateur)) 
{
    header('location: login');
}
//sinon exécution du programme:
else 
{
    if (!empty($_POST['Ajaxtransport']))
    {



     //recupere le devis:
     $temp =   $Cmd->GetById($_POST['Ajaxtransport']);
     //recupere le createur du devis 
     $user = $User->getByID($temp->devis__user__id);
     //recupere le client: 
     $clientView = $Client->getOne($temp->client__id);
     //si societe de livraison, recupere la societe de livraison:
     $societeLivraison = false ;
     if ($temp->devis__id_client_livraison) 
     {
         $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
     }
     //recupere les lignes du devis:
     $arrayOfDevisLigne = $Cmd->devisLigne($_POST['Ajaxtransport']);
     //cree une variable pour la date de commande du devis
     $date_time = new DateTime( $temp->cmd__date_cmd);
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
 

 <div class="global-wrapper">
 <div class="div bodyFrame mt-4" style="width: 100%;" >
         <div class="head-wrapper div">


             <div class="div" style="text-align: left;  width: 50%;" >
             <div class="div">
             <img  style=" width:55mm;" src="public\img\recodeDevis.png"/>
             </div>
             <div class="div" style=" margin-top: 5%" >
             <p>Edité le : <?php echo $formated_date ?> à : <?php echo $h . 'h ' . $m ?></p>
             Vendeur: <strong><?php echo  $user->nom . ' '. $user->prenom ?> </strong> 
             <?php
             if (!empty($user->postefix)) {
               echo ' (Tél: '. $user->postefix .')<br>';
             } 
             if ($temp->cmd__date_envoi) 
             {
                 $date = date_create( $temp->cmd__date_envoi);
                 $date = date_format($date ,'d/m/Y');
                echo "<small>Date d'envoi prévue:</small> ".$date."";
             }
             ?></td>
            
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
            <form method="POST" action="pdfTravail" target="_blank">
                <input type="hidden" value="<?php echo $_POST['AjaxFT'] ?>" name="travailFiche">
                <button class="btn btn-link " href="pdfTravail"><i class='fas fa-file-pdf'></i> voir le pdf</button>
            </form>
        </div>
       </div>
      </div>
    </div>







    <div class="div" style="display: flex; justify-content: center; margin-top: 10%;">
        <table CELLSPACING=0 style="width: 95%;" class="table">
            
                <tr style=" margin-top : 50px; background-color: #dedede; vertical-align: top;" >
                    <td style="width: 5%; background-color: white; border-style: none;" ></td>
                    <td style="width: 13%; text-align: left;">Prestation</td>
                    <td style="width: 50%; text-align: left">Désignation Client</td>
                    <td style="text-align: right; width: 12%"><strong>CMD</strong></td>
                </tr>
           
                <?php
                $comand = $temp;
                foreach ($arrayOfDevisLigne as $item) {
                    if(intval($item->cmdl__garantie_option) > intval($item->devl__mois_garantie)) 
                    {
                    $temp = $item->cmdl__garantie_option ;
                    } 
                    else 
                    { 
                    $temp = intval($item->devl__mois_garantie);
                    }
                    
                    switch ($item->groupe_famille) {

                        case 'CDB':
                            echo "<tr style='font-size: 95%;'>
                            <td style='border-style: none; '> <button class='clickFT btn btn-success mt-2' value='".$item->devl__id."' '><i class='fas fa-barcode'></i></button></td>
                            <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #e6ffe6;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois </td>
                            <td style='border-bottom: 1px #ccc solid; background-color: #e6ffe6;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br>" .$item->devl__note_interne ." </td>
                            <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #e6ffe6; '><strong> "  . $item->devl_quantite. " </strong> </td>
                            </tr>";
                            break;
                        
                        case 'CONSO':
                            echo "<tr style='font-size: 95%;'>
                            <td style='border-style: none; '> <button class='clickFT btn btn-warning mt-2' value='".$item->devl__id."' '><i class='fas fa-sync-alt'></i></button></td>
                            <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #fff5f0;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois </td>
                            <td style='border-bottom: 1px #ccc solid; background-color: #fff5f0;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                            <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #fff5f0; '><strong> "  . $item->devl_quantite. " </strong> </td>
                            </tr>";
                            break;
                        
                        case 'ILM':
                            echo "<tr style='font-size: 95%;'>
                            <td style='border-style: none; '> <button class='clickFT btn btn-info mt-2' value='".$item->devl__id."' '><i class='fas fa-shredder'></i></button></td>
                            <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #e8ffff;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois </td>
                            <td style='border-bottom: 1px #ccc solid; background-color: #e8ffff;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                            <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #e8ffff; '><strong> "  . $item->devl_quantite. " </strong> </td>
                            </tr>";
                        break;

                        case 'IT':
                            echo "<tr style='font-size: 95%;'>
                            <td style='border-style: none; '> <button class='clickFT btn btn-danger mt-2 ' value='".$item->devl__id."''><i class='fas fa-print'></i></button></td>
                            <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #ffeded;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois </td>
                            <td style='border-bottom: 1px #ccc solid; background-color: #ffeded;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                            <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #ffeded; '><strong> "  . $item->devl_quantite. " </strong> </td>
                            </tr>";
                        break;

                        case 'MICRO':
                            echo "<tr style='font-size: 95%;'>
                            <td style='border-style: none; '> <button class='clickFT btn btn-primary mt-2 ' value='".$item->devl__id."'  ' ><i class='fas fa-computer-classic'></i></button></td>
                            <td style='border-bottom: 1px #ccc solid; text-align:left; background-color: #ebffff;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois </td>
                            <td style='border-bottom: 1px #ccc solid; background-color: #ebffff;'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                            <td style='border-bottom: 1px #ccc solid;  text-align: right; background-color: #ebffff; '><strong> "  . $item->devl_quantite. " </strong> </td>
                            </tr>";
                        break;
                        
                        default:
                            echo "<tr style='font-size: 95%;'>
                            <td style='border-style: none; '> <button class='clickFT btn btn-secondary mt-2' value='".$item->devl__id."' '><i class='fas fa-clipboard-list'></i></button></td>
                            <td style='border-bottom: 1px #ccc solid; text-align:left;'>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois </td>
                            <td style='border-bottom: 1px #ccc solid'><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br> " .$item->devl__note_interne ." </td>
                            <td style='border-bottom: 1px #ccc solid;  text-align: right '><strong> "  . $item->devl_quantite. " </strong> </td>
                            </tr>";
                            break;
                    }
                  
                }
                ?>
        </table> 
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
        echo  json_encode( $e->getMessage());
    }       

}
}