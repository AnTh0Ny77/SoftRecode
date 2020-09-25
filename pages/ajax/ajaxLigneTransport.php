<?php
require "./vendor/autoload.php";
session_start();
//instanciation des entités nécéssaires au programme:
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);



// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) 
{
    header('location: login');
}
//sinon exécution du programme:
else 
{
if (!empty($_POST['AjaxLignetransport']))
{
    $lignes = $Cmd->devisLigne($_POST['AjaxLignetransport']);
    ob_start();
    $html = "";
    $html .= '<table CELLSPACING=0 style="width: 95%;  margin-top : 30px;" class="table table-striped">';
   

    foreach ($lignes as $item) 
    {
        if ($item->prestaLib != 'Port') 
        {
            $select = '';
            for ($i=0; $i <= intval($item->devl_quantite) ; $i++) 
            { 
                if ($i == intval($item->devl_quantite)) 
                {
                    $select .= '<option value='.$i.' selected>'.$i.'</option>';
                }
                else
                {
                    $select .= '<option value='.$i.'>'.$i.'</option>';
                }
              
            }
            $html .= "<tr style='font-size: 95%;'>
            <td style='border-style: none; '> 
            <select name='linesTransport[".$item->devl__id."]' class='form-control col-8'>
            ".$select."
            </select>

            </td>
            <td style='border-bottom: 1px #ccc solid; text-align:left; '>". $item->prestaLib." <br> " .$item->kw__lib ."</td>
            <td style='border-bottom: 1px #ccc solid; '><strong> ".$item->devl__designation."</td>
            <td style='border-bottom: 1px #ccc solid;  text-align: right;  '><strong> " ;
        }       
    }
   
    $html .= "</table>";
    $array = [];
    array_push($array, $html);
    echo  json_encode($array);
}
else{
    $error = array('error');
    echo json_encode($error , JSON_FORCE_OBJECT);
}
}