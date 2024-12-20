<?php

class Router{
    protected $routes = array();
    protected $params = array();

    public function add($route, $params){
        $this->routes[$route] = $params; 
    }

    // Devuelve el array de rutas
    public function getRoutes(){
        return $this->routes;
    }
    
    // Devuelve los parámetros en uso del objeto Router
    function getParams (){
        return $this->params;
    }

    function matchRoute($url){

        // Recorre cada ruta guardada de modo que la route sea la clave y params el valor
        foreach($this->routes as $route=>$params){

            // Reemplazamos el {id} de las rutas almacenadas por cualquier número del 0-9
            // y le añadimos \ a las / para poder crear el pattern para comparar
            $pattern = str_replace(['{id}','/'],['([0-9]+)','\/'], $route);

            // Creamos el pattern con su inicio y final
            $pattern = '/^'.$pattern.'$/';

            // Si la URL introducida cumple con el patrón establecido
            if(preg_match($pattern, $url['path'])){

                // Los parámetros en uso será los que correspondan al path de la URL
                $this->params = $params;
                return true;
            }
        }
        return false;
    }


}


?>