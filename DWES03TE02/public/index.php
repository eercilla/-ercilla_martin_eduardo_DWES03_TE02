<?php

    require __DIR__.'/../core/router.php';
    require __DIR__.'/../app/controllers/juego.php';
    require __DIR__.'/../utils/helpers.php';
    require __DIR__.'/../app/controllers/compania.php';
    require __DIR__.'/../app/controllers/sistema.php';

    $url = $_SERVER['QUERY_STRING'];
    // echo "URL= ".$url."<br>";

    // Creamos un objeto de la clase Router
    $router = new Router();


    // Inicializamos el router con la clase estática inicializarRouter de la clase Helpers
    Helpers::inicializarRouter($router);



    // Creamos un array que almacena los campos de la URL
    $urlParams = explode('/', $url);

    $urlArray = array(
        'HTTP' => $_SERVER['REQUEST_METHOD'],
        'path' => $url,
        'controller' => '',
        'action' => '',
        'params' => ''
    );

    // Si campo 2 no viene vacío
    if(!empty($urlParams[2])){
        $urlArray['controller']=ucwords($urlParams[2]);

        // Si campo 3 no viene vacío
        if(!empty($urlParams[3])){
            $urlArray['action']=$urlParams[3];

            // Si campo 4 no viene vacío
            if(!empty($urlParams[4])){
                $urlArray['params']=$urlParams[4];}
        }else{
            $urlArray['action']='index';
        }

    }else{ // Si todos los campos vienen vacíos


        $urlArray['controller']='Home';
        $urlArray['action']='index';
    }

    // echo "<pre>";

    // print_r($urlArray)."<br>";

    // echo "</pre>\n\n";

    // Comprobamos que la ruta introducida corresponde con alguna guardada en nuestro router
    if($router->matchRoute($urlArray)){

        // Obtenemos el método 
        $method = $_SERVER['REQUEST_METHOD'];
        $params = [];

        // Si método elegido == 'GET'
        if ($method==='GET'){
            
            // Comprobamos que la acción de la URL y el método elegido sean la misma
            // Si es diferente finaliza la petición
            if($urlArray['action']!=="get"){
                http_response_code(404);
                echo json_encode(["error" => "Endpoint no disponible para el method GET","description" => "Page Not Found", "code" => 404], JSON_PRETTY_PRINT)."\n";
                exit;
            }
            // Si existe, adquiere el valor, sino null
            $params[]=intval($urlArray['params']) ?? null;
            
        } elseif($method==='POST'){ // Si método elegido == 'POST'

            if($urlArray['action']!=="create"){
                http_response_code(404);
                echo json_encode(["error" => "Endpoint no disponible para el method POST","description" => "Page Not Found", "code" => 404], JSON_PRETTY_PRINT)."\n";
                exit;
            }

            // Obtiene el JSON introducido en Body.
            $json = file_get_contents('php://input');
            $params[] = json_decode($json,true);

        } elseif($method==='PUT'){ // Si método elegido == 'PUT'

            if($urlArray['action']!=="update"){
                http_response_code(404);
                echo json_encode(["error" => "Endpoint no disponible para el method PUT","description" => "Page Not Found", "code" => 404], JSON_PRETTY_PRINT)."\n";
                exit;
            }

            $id=intval($urlArray['params']) ?? null;
            $json = file_get_contents('php://input');
            $params[] = $id;
            $params[] = json_decode($json,true);
            
        } elseif($method==='DELETE'){ // Si método elegido == 'DELETE'

            if($urlArray['action']!=="delete"){
                http_response_code(404);
                echo json_encode(["error" => "Endpoint no disponible para el method DELETE","description" => "Page Not Found", "code" => 404], JSON_PRETTY_PRINT)."\n";
                exit;
            }

            $params[]=intval($urlArray['params']) ?? null;
        }

        $controller = $router->getParams()['controller'];
        $action =  $router->getParams()['action'];

        // Controller será una instancia de la entidad de la clase contenida
        $controller = new $controller();

        // Si la clase contiene el método pasado
        if(method_exists($controller, $action)){

            // Ejecuta el método de la clase contenida en controller con los parámetros pasados (de tenerlos)
            $resp = call_user_func_array([$controller,$action], $params);
        }else{
            echo "El método no existe";
        }
    } else{
        // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
        http_response_code(404);
        echo json_encode(["error" => "Endpoint no disponible","description" => "Page Not Found", "code" => 404], JSON_PRETTY_PRINT)."\n";
    }

?>