
  <?php 
       
       $request = $_SERVER['REQUEST_URI'];
       
       switch($request){
            case  '/Devis/'. '':
              require __DIR__ .'/pages/login.php';
            break;

            case '/Devis/login':
              require __DIR__ .'/pages/login.php';
            break;

            case '/Devis/home':
              require __DIR__ .'/pages/home.php';
            break;
            
            default:
            header('HTTP/1.0 404 not found');
            require  __DIR__ .'/pages/error404.php';
          break;
       }

      

      
