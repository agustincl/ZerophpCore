<?php
namespace acl\ZerophpCore;

class Router
{
    static public function route($url, $routes)
    {
        $router = self::readRouter(explode("/",$url)[1], $routes);
        $request = self::parseUrl($url, $router);
        
        return $request;
    }
    
    static public function readRouter($url, $routes)
    {
        if(isset($routes[$url]))
            return $routes[$url];
        else
            return array('module'=>'Application',
                    'controller'=>'Error'
                );
//         else return;
    }
    
    /**
     * Get request from url
     *
     * @param string $url
     * @return array
     *
     * $array = array('controller','action','params'=>array())
     */
    static public function parseUrl($url, $router)
    {
        
//         echo "<pre>router: ";
//         print_r($router);
//         echo "</pre>";
        
        
        /**
         /README.md                  -> README.md
         /                           -> controller=default, action=default
         /kaka                       -> 404
         /users                      -> controller=users, action = default
         /users/insert               -> controller=users, action = insert
         /users/update/id/8          -> controller=users, action = update, params = array(id=>8)
         /users/kaka                 -> 404
         /users/update/id            -> 400
         /users/update/id/8/param    -> 400
         /users/update/kaka/kaka     -> controller=users, action = update, params = array(kaka=>kaka)
         */
    
    
    
        $controllerPath = $_SERVER['DOCUMENT_ROOT'].'/../modules/'.$router['module'].
        '/src/'.$router['module'].
        '/Controller/';
        // Explode $URL
        $arrayEntrada = explode("/", $url);
        $largoArrayEntrada = count($arrayEntrada);
    
        if(isset($router['controller']))
            $arrayEntrada[1] = $router['controller'];
    
    
        // Verify exists controller file
        if (file_exists($controllerPath.$arrayEntrada[1].'.php'))
        {
            //Save the controller name
            $arraySalida['controller'] = $arrayEntrada[1];
            $arraySalida['action'] = 'indexAction';
    
            // If exists action name save it, else default
            if (isset($arrayEntrada[2]))
            {
                // Verificar si la action existe
                //pendiente de localizar función que busque en un array.
                $acciones = array('insert','update','delete','select');
                
                $methods = get_class_methods($router['module']."\\Controller\\".$router['controller']);
                
//                 echo "<pre>";
//                 print_r($methods);
//                 echo "</pre>";
//                 die;
                
                $arraySalida['action'] = $arrayEntrada[2].'Action';
                //                     if (!array_search($arrayEntrada[2], $acciones))
                if(!in_array($arrayEntrada[2], $acciones))
                //if (!array_search($arrayEntrada[2], $acciones))
                {
                    //header("HTTP/1.0 404 Not Found");
                    $arraySalida = array('controller'=>'error', 'action'=>'Error404Action');
                    //break;
                }
    
                 
    
                //if the number of elements is even and greater than 2,
                //then save the params in an array.
                if ($largoArrayEntrada > 3 && ($largoArrayEntrada % 2) != 0)
                {
                    $params = array();
                    for ($i = 3; $i <= $largoArrayEntrada-2; $i=$i+2)
                    {
                        $params[$arrayEntrada[$i]] = $arrayEntrada[$i+1];
                    }
                    $arraySalida['params'] = $params;
                }
                else if($largoArrayEntrada > 3){
                    $arraySalida = array('controller'=>'error', 'action'=>'Error400Action');
                }
            }
            //                 else if() {
            //                     $arraySalida = array('controller'=>'error', 'action'=>'400s');
            //                 }
        }
        else if($arrayEntrada[1]!='')
        {
            $arraySalida = array('controller'=>'error', 'action'=>'Error404Action');
        }
        else
        {
            $arraySalida = array('controller'=>'index', 'action'=>'indexAction');
        }
        
        $array = array_merge($router, $arraySalida);
        
//         echo "<pre>request: ";
//         print_r($array);
//         echo "</pre>";
    
        return $array;
    }

}
