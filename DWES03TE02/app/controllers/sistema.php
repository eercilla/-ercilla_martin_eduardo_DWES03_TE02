<?php

    class Sistema{

        private int $id;
        private string $nombre;
        private int $lanzamiento;

        // Constructor con parámetros por defecto para poder utilizar constructores vacíos
        function __construct($id = 0, $nombre = "Desconocido", $lanzamiento = 0){
            
            $this->id = $id;
            $this->nombre = $nombre;
            $this->lanzamiento = $lanzamiento;

        }


        function getAllSistema(){

            // Cargamos el JSON de la entidad requerida
            $sistemas = file_get_contents(__DIR__ .'/../../data/sistemas.json', true);

            $jArray = json_decode($sistemas, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }
            http_response_code(200);
            echo json_encode(["status" => "Success", "message" => "Recurso obtenido con exito", "code" => 200, "data" => $jArray], JSON_PRETTY_PRINT)."\n";



        }


        function getSistemaById($id){

            $sistemas = file_get_contents(__DIR__ .'/../../data/sistemas.json', true);
            // JSON a array
            $jArray = json_decode($sistemas, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }
            
            $encontrado = Helpers::obtenerElementoPorId($id, $jArray);

            if ($encontrado){
                echo json_encode(["status" => "Success", "message" => "Sistema encontrado con exito", "code" => 200, "data" => $encontrado], JSON_PRETTY_PRINT)."\n";
            } else{

                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Sistema no encontrado", "code" => 404], JSON_PRETTY_PRINT)."\n";
            }
            
        }



        function createSistema($data){

            $sistemas = file_get_contents(__DIR__ .'/../../data/sistemas.json', true);

            // JSON a array
            $jArray = json_decode($sistemas, true);

            // Obtener el ID del último elemento del array
            $ultimoSistema = end($jArray);
            $nuevoId = $ultimoSistema['id']+1;

            // Id en la primera posición del nuevo sistema
            $dataId = ['id' => $nuevoId] + $data;



            array_push($jArray, $dataId);

            http_response_code(200);
            echo json_encode(["status" => "Success", "message" => "Sistema creado con exito", "code" => 200, "data" => $dataId], JSON_PRETTY_PRINT)."\n";

            // Array a JSON
            $sistemasActualizados = json_encode($jArray, JSON_PRETTY_PRINT);
            // Guarda el JSON actualizado en un archivo
            if (file_put_contents(__DIR__ .'/../../data/sistemas.json', $sistemasActualizados)===false){
                http_response_code(500);
                echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
            };  
        }

        function updateSistema($id, $data){


            $sistemas = file_get_contents(__DIR__ .'/../../data/sistemas.json', true);

            // JSON a array
            $jArray = json_decode($sistemas, true);

            $encontrado = false;

            // Para cada sistema del array de sistemas (& indica que se actualice también en el array original)
            foreach ($jArray as &$sistema) {
                if ($sistema['id'] == $id) {

                    // Combina la información contenida originalmente con la info añadida y/o actualizada 
                    $sistema= array_merge($sistema, $data);

                    // Existe el elemento con id igual al requerido
                    $encontrado = true;
                    // Finaliza el bucle si encuentra el elemento
                    break;
                    }
                }

            // Si el elemento con id igual al requerido existe
            if($encontrado){
            // Codificar el array actualizado de nuevo a JSON
            $sistemasActualizados = json_encode($jArray, JSON_PRETTY_PRINT);
            // Guarda el JSON actualizado en un archivo
            if (file_put_contents(__DIR__ .'/../../data/sistemas.json', $sistemasActualizados)===false){
                http_response_code(500);
                echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
            };            
            http_response_code(200);
            
            echo json_encode(["status" => "Success", "message" => "Sistema con ID: ".$id." actualizado con exito", "code" => 204], JSON_PRETTY_PRINT)."\n";
            } else{

                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Sistema no encontrado", "code" => 404], JSON_PRETTY_PRINT);
            }



        }

        function deleteSistema($id){

            $sistemas = file_get_contents(__DIR__ .'/../../data/sistemas.json', true);

            // JSON a array
            $jArray = json_decode($sistemas, true);

            $encontrado = false;

            // Buscar y eliminar el sistema con el ID especificado de la forma clave-valor
            foreach ($jArray as $key => $sistema) {
                if ($sistema['id'] == $id) {

                    // Elimina el elemento en la posición clave
                    unset($jArray[$key]);
                    
                    // Existe el elemento con id igual al requerido
                    $encontrado = true;

                    // Finaliza el bucle si encuentra el elemento
                    break;
                    }
                }
            // Si el elemento con id igual al requerido existe
            if ($encontrado) {
                // Hace que los índices del array sean consecutivos (suprimiendo los huecos)
                $jArray = array_values($jArray);
                // Array a JSON
                $sistemasActualizados = json_encode($jArray, JSON_PRETTY_PRINT);

                // Guarda el JSON actualizado en un archivo
                if (file_put_contents(__DIR__ .'/../../data/sistemas.json', $sistemasActualizados)===false){
                    http_response_code(500);
                    echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
                };
                http_response_code(200);

                echo json_encode(["status" => "Success", "message" => "Sistema con ID: ".$id." borrado con exito", "code" => 204], JSON_PRETTY_PRINT);
                
            } else{
                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Sistema no encontrado", "code" => 404], JSON_PRETTY_PRINT);
            }
        }
    }

?>