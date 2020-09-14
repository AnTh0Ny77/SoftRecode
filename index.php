<?php

   /*    8888b.  88""Yb 888888 .dP"Y8 .dP"Y8    db     dP""b8 888888     8888b.  888888     88""Yb    db     dP""b8 888888 .dP"Y8 
  dPYb    8I  Yb 88__dP 88__   `Ybo." `Ybo."   dPYb   dP   `" 88__        8I  Yb 88__       88__dP   dPYb   dP   `" 88__   `Ybo." 
 dP__Yb   8I  dY 88"Yb  88""   o.`Y8b o.`Y8b  dP__Yb  Yb  "88 88""        8I  dY 88""       88"""   dP__Yb  Yb  "88 88""   o.`Y8b 
dP""""Yb 8888Y"  88  Yb 888888 8bodP' 8bodP' dP""""Yb  YboodP 888888     8888Y"  888888     88     dP""""Yb  YboodP 888888 8bodP*/ 

  $request = $_SERVER['REQUEST_URI'];

  switch($request)
  {

/*    d8 888888 88b 88 88   88 
88b  d88 88__   88Yb88 88   88 
88YbdP88 88""   88 Y88 Y8   8P 
88 YY 88 888888 88  Y8 `Ybod*/ 
    case '/SoftRecode/'.'':
        require __DIR__ .'/pages/login.php'; break;
    case '/SoftRecode/login':
      require __DIR__ .'/pages/login.php'; break;
   
    case '/SoftRecode/unlog';
      require __DIR__ .'/pages/utilities/unlog.php'; break;
    case '/SoftRecode/noAccess';
      require __DIR__ .'/pages/noAccess.php'; break;
      case '/SoftRecode/test';
      require __DIR__ .'/pages/test.php'; break;

/*   88 .dP"Y8 888888 88""Yb 
88   88 `Ybo." 88__   88__dP 
Y8   8P o.`Y8b 88""   88"Yb  
`YbodP' 8bodP' 888888 88  Y*/ 
    case '/SoftRecode/utilisateurs';
      require __DIR__ .'/pages/user.php'; break;
    case '/SoftRecode/User';
      require __DIR__ .'/pages/user.php'; break;
    case '/SoftRecode/U_UserUpdate';
      require __DIR__ .'/pages/utilities/U_UserUpdate.php'; break;
    case '/SoftRecode/UserModif';
      require __DIR__ .'/pages/UserModif.php'; break;
    case '/SoftRecode/UserCreat';
      require __DIR__ .'/pages/UserCreat.php'; break;


       /*    88""Yb 888888 88  dP""b8 88     888888 
  dPYb   88__dP   88   88 dP   `" 88     88__   
 dP__Yb  88"Yb    88   88 Yb      88  .o 88""   
dP""""Yb 88  Yb   88   88  YboodP 88ood8 88888*/ 
    case '/SoftRecode/catalogue';
    require __DIR__ .'/pages/ArtCataloguePN.php';break;
  case '/SoftRecode/ArtCataloguePN';
    require __DIR__ .'/pages/ArtCataloguePN.php';break;
  case '/SoftRecode/ArtCatalogueModele';
    require __DIR__ .'/pages/ArtCatalogueModele.php';break;
  case '/SoftRecode/ArtCreation';
    require __DIR__ .'/pages/ArtCreat.php';break;
  case '/SoftRecode/U_ArtUpdate';
    require __DIR__ .'/pages/utilities/U_ArtUpdate.php'; break;



    case '/SoftRecode/dashboard';
      require __DIR__ .'/pages/dashboard.php';break;

    case '/SoftRecode/nouveauDevis';
      require __DIR__ .'/pages/nouveauDevis.php';break;

    case '/SoftRecode/mesDevis';
      require __DIR__ .'/pages/mesDevis.php'; break;

    case '/SoftRecode/pdf';
      require __DIR__ .'/pages/utilities/pdf.php'; break;

    case '/SoftRecode/voirDevis';
      require __DIR__ .'/pages/utilities/viewPdf.php'; break;

    case '/SoftRecode/AjaxSociete';
      require __DIR__ .'/pages/ajax/ajaxClient.php'; break;

    case '/SoftRecode/AjaxVisio';
      require __DIR__ .'/pages/ajax/AjaxVisioPDF.php'; break;

    case '/SoftRecode/AjaxFT';
      require __DIR__ .'/pages/ajax/AjaxVisionFT.php'; break;
    
    case '/SoftRecode/AjaxTransport';
    require __DIR__ .'/pages/ajax/ajaxVisionTransport.php'; break;

    case '/SoftRecode/AjaxLigneFT';
    require __DIR__ .'/pages/ajax/AjaxLigneFT.php'; break;

    case '/SoftRecode/transport';
    require __DIR__ .'/pages/transport.php'; break;

    case '/SoftRecode/AjaxDevis';
      require __DIR__ .'/pages/ajax/ajaxDevis.php'; break;
    
    case '/SoftRecode/AjaxDevisFacture';
      require __DIR__ .'/pages/ajax/ajaxDevisFacture.php'; break;

    case '/SoftRecode/commande';
      require __DIR__ .'/pages/commandValid.php'; break;

    case '/SoftRecode/createNew';
      require __DIR__ .'/pages/ajax/ajaxCreate.php'; break;

    case '/SoftRecode/AjaxStatDevis';
      require __DIR__ .'/pages/ajax/ajaxChartsDevis.php'; break;

    case '/SoftRecode/createClient';
      require __DIR__ .'/pages/ajax/ajaxCreateClient.php'; break;

    case '/SoftRecode/tableContact';
      require __DIR__ .'/pages/ajax/ajaxTableContact.php'; break;

    case '/SoftRecode/choixContact';
      require __DIR__ .'/pages/ajax/ajaxcontactChoix.php'; break;

    case '/SoftRecode/createContact';
      require __DIR__ .'/pages/ajax/ajaxcreateContact.php'; break;

    case '/SoftRecode/choixLivraison';
      require __DIR__ .'/pages/ajax/ajaxChoixLivraison.php'; break;


    case '/SoftRecode/pdfTravail';
      require __DIR__ .'/pages/utilities/pdfTravail.php'; break;

    case '/SoftRecode/pdfFacture';
      require __DIR__ .'/pages/utilities/pdfFacture.php'; break;


    case '/SoftRecode/pdfBL';
      require __DIR__ .'/pages/utilities/pdfBL.php'; break;

    case '/SoftRecode/facture';
      require __DIR__ .'/pages/facture.php'; break;

    case '/SoftRecode/export';
      require __DIR__ .'/pages/export.php'; break;

    case '/SoftRecode/factureVisio';
      require __DIR__ .'/pages/ajax/ajaxVisioFacture.php'; break;

      case '/SoftRecode/AvoirVisio';
      require __DIR__ .'/pages/ajax/ajaxVisoAvoir.php'; break;

    case '/SoftRecode/printFt';
      require __DIR__ .'/pages/utilities/printFT.php'; break;

    case '/SoftRecode/printBl';
      require __DIR__ .'/pages/utilities/printBL.php'; break;


    case '/SoftRecode/printFTC';
      require __DIR__ .'/pages/utilities/printFTC.php'; break;

    case '/SoftRecode/AjaxSaisie';
      require __DIR__ .'/pages/ajax/ajaxSaisie.php'; break;
    
    case '/SoftRecode/AjaxClientContact';
      require __DIR__ .'/pages/ajax/ajaxClientContact.php'; break;

    case '/SoftRecode/AjaxPn';
      require __DIR__ .'/pages/ajax/ajaxPn.php'; break;

    case '/SoftRecode/TicketVisu';
      require __DIR__ .'/pages/TicketVisu.php'; break;

      case '/SoftRecode/ficheTravail';
      require __DIR__ .'/pages/ficheT.php'; break;

    case '/SoftRecode/avoir';
      require __DIR__ .'/pages/avoir.php'; break;

      case '/SoftRecode/fichesEnCours';
      require __DIR__ .'/pages/fichesEnCours.php'; break;

      case '/SoftRecode/font';
      require __DIR__ .'/vendor/tecnickcom/tcpdf/fonts/convertfont.php'; break;


  /*88   dP"Yb    dP88  
 dP 88  dP   Yb  dP 88  
d888888 Yb   dP d888888 
    88   YbodP      88     */  
      default:
        header('HTTP/1.0 404 not found');
        require  __DIR__ .'/pages/error404.php';
        break;
  }

?>
