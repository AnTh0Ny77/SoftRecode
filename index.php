
  <?php 
       
       $request = $_SERVER['REQUEST_URI'];
      
      
       switch($request){
            case  '/DevisRecode/'. '':
              require __DIR__ .'/pages/login.php';
            break;

            case '/DevisRecode/login':
              require __DIR__ .'/pages/login.php';
            break;

            case '/DevisRecode/home':
              require __DIR__ .'/pages/home.php';
            break;
            
            default:
            header('HTTP/1.0 404 not found');
            require  __DIR__ .'/pages/error404.php';
          break;
       }

      

      
