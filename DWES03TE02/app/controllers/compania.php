<?php

    class Compania{

        private int $id;
        private string $nombre;
        private int $fundacion;
        private string $pais;

        // Constructor con parámetros por defecto para poder utilizar constructores vacíos
        function __construct($id = 0, $nombre = "Desconocida", $fundacion = 0, $pais = "Desconocido"){
            
            $this->id = $id;
            $this->nombre = $nombre;
            $this->fundacion = $fundacion;
            $this->pais = $pais;

        }


        function getAllCompania(){

            // Cargamos el JSON de la entidad requerida
            $companias = file_get_contents(__DIR__ .'/../../data/companias.json', true);

            $jArray = json_decode($companias, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }
            http_response_code(200);
            echo json_encode(["status" => "Success", "message" => "Recurso obtenido con exito", "code" => 200, "data" => $jArray], JSON_PRETTY_PRINT)."\n";



        }


        function getCompaniaById($id){

            $companias = file_get_contents(__DIR__ .'/../../data/companias.json', true);
            // JSON a array
            $jArray = json_decode($companias, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }

            $encontrado = Helpers::obtenerElementoPorId($id, $jArray);

            if ($encontrado){
                echo json_encode(["status" => "Success", "message" => "Compania encontrada con exito", "code" => 200, "data" => $encontrado], JSON_PRETTY_PRINT)."\n";
            } else{

                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Compania no encontrada", "code" => 404], JSON_PRETTY_PRINT)."\n";
            }
            
        }



        function createCompania($data){

            $companias = file_get_contents(__DIR__ .'/../../data/companias.json', true);

            // JSON a array
            $jArray = json_decode($companias, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }

            // Obtener el ID del último elemento del array
            $ultimoCompania = end($jArray);
            $nuevoId = $ultimoCompania['id']+1;

            // Id en la primera posición de la nueva compañía
            $dataId = ['id' => $nuevoId] + $data;



            array_push($jArray, $dataId);

            http_response_code(200);
            echo json_encode(["status" => "Success", "message" => "Compania creada con exito", "code" => 200, "data" => $dataId], JSON_PRETTY_PRINT)."\n";

            // Array a JSON
            $companiasActualizados = json_encode($jArray, JSON_PRETTY_PRINT);
            // Guarda el JSON actualizado en un archivo
            if (file_put_contents(__DIR__ .'/../../data/companias.json', $companiasActualizados)===false){
                http_response_code(500);
                echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
            };

        }

        function updateCompania($id, $data){


            $companias = file_get_contents(__DIR__ .'/../../data/companias.json', true);

            // JSON a array
            $jArray = json_decode($companias, true);

            $encontrado = false;

            // Para cada compania del array de companias (& indica que se actualice también en el array original)
            foreach ($jArray as &$compania) {
                if ($compania['id'] == $id) {

                    // Combina la información contenida originalmente con la info añadida y/o actualizada 
                    $compania= array_merge($compania, $data);

                    // Existe el elemento con id igual al requerido
                    $encontrado = true;
                    // Finaliza el bucle si encuentra el elemento
                    break;
                    }
                }

            // Si el elemento con id igual al requerido existe
            if($encontrado){
            // Codificar el array actualizado de nuevo a JSON
            $companiasActualizados = json_encode($jArray, JSON_PRETTY_PRINT);
            // Guarda el JSON actualizado en un archivo
            if (file_put_contents(__DIR__ .'/../../data/companias.json', $companiasActualizados)===false){
                http_response_code(500);
                echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
            };

            http_response_code(200);
            
            echo json_encode(["status" => "Success", "message" => "Compania con ID: ".$id." actualizada con exito", "code" => 204], JSON_PRETTY_PRINT)."\n";
            } else{

                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Compania no encontrada", "code" => 404], JSON_PRETTY_PRINT);
            }



        }

        function deleteCompania($id){

            $companias = file_get_contents(__DIR__ .'/../../data/companias.json', true);

            // JSON a array
            $jArray = json_decode($companias, true);

            $encontrado = false;

            // Buscar y eliminar el compania con el ID especificado de la forma clave-valor
            foreach ($jArray as $key => $compania) {
                if ($compania['id'] == $id) {

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
                $companiasActualizadas = json_encode($jArray, JSON_PRETTY_PRINT);

                // Guarda el JSON actualizado en un archivo
                if (file_put_contents(__DIR__ .'/../../data/companias.json', $companiasActualizadas)===false){
                    http_response_code(500);
                    echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
                };
                http_response_code(200);

                echo json_encode(["status" => "Success", "message" => "Compania con ID: ".$id." borrado con exito", "code" => 204], JSON_PRETTY_PRINT);
                
            } else{
                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Compania no encontrada", "code" => 404], JSON_PRETTY_PRINT);
            }
        }
    }

?>