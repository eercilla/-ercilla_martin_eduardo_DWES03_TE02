<?php

    class Juego{

        private int $id;
        private string $título;
        private string $compania;
        private string $sistema;
        private string $genero;

        // Constructor con parámetros por defecto para poder utilizar constructores vacíos
        function __construct($id = 0, $título = "Desconocido", $compania = "Desconocida", $sistema = "Desconocido", $genero ="Desconocido"){
            
            $this->id = $id;
            $this->título = $título;
            $this->compania = $compania;
            $this->sistema = $sistema;
            $this->genero = $genero;

        }


        function getAllJuego(){

            // Cargamos el JSON de la entidad requerida
            $juegos = file_get_contents(__DIR__ .'/../../data/juegos.json', true);

            $jArray = json_decode($juegos, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }
            http_response_code(200);
            echo json_encode(["status" => "Success", "message" => "Recurso obtenido con exito", "code" => 200, "data" => $jArray], JSON_PRETTY_PRINT)."\n";

        }


        function getJuegoById($id){

            $juegos = file_get_contents(__DIR__ .'/../../data/juegos.json', true);
            // JSON a array
            $jArray = json_decode($juegos, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }
           

            $encontrado = Helpers::obtenerElementoPorId($id, $jArray);

            if ($encontrado){
                http_response_code(200);
                echo json_encode(["status" => "Success", "message" => "Juego encontrado con exito", "code" => 200, "data" => $encontrado], JSON_PRETTY_PRINT)."\n";
            } else{

                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Juego no encontrado", "code" => 404], JSON_PRETTY_PRINT)."\n";
            }
            
        }



        function createJuego($data){

            $juegos = file_get_contents(__DIR__ .'/../../data/juegos.json', true);

            // JSON a array
            $jArray = json_decode($juegos, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }

            // Obtener el ID del último elemento del array
            $ultimoJuego = end($jArray);
            $nuevoId = $ultimoJuego['id']+1;

            // Id en la primera posición del nuevo juego
            $dataId = ['id' => $nuevoId] + $data;



            array_push($jArray, $dataId);
            
            http_response_code(200);
            echo json_encode(["status" => "Success", "message" =>  "Juego creado con exito", "code" => 200, "data" => $dataId], JSON_PRETTY_PRINT)."\n";

            // Array a JSON
            $juegosActualizados = json_encode($jArray, JSON_PRETTY_PRINT);
            // Guarda el JSON actualizado en un archivo
            if (file_put_contents(__DIR__ .'/../../data/juegos.json', $juegosActualizados)===false){
                http_response_code(500);
                echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
            };

        }

        function updateJuego($id, $data){


            $juegos = file_get_contents(__DIR__ .'/../../data/juegos.json', true);

            // JSON a array
            $jArray = json_decode($juegos, true);
            
            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }

            $encontrado = false;

            // Para cada juego del array de juegos (& indica que se actualice también en el array original)
            foreach ($jArray as &$juego) {
                if ($juego['id'] == $id) {

                    // Combina la información contenida originalmente con la info añadida y/o actualizada 
                    $juego= array_merge($juego, $data);

                    // Existe el elemento con id igual al requerido
                    $encontrado = true;
                    // Finaliza el bucle si encuentra el elemento
                    break;
                    }
                }

            // Si el elemento con id igual al requerido existe
            if($encontrado){
            // Codificar el array actualizado de nuevo a JSON
            $juegosActualizados = json_encode($jArray, JSON_PRETTY_PRINT);
            
            // Guarda el JSON actualizado en un archivo

            if (file_put_contents(__DIR__ .'/../../data/juegos.json', $juegosActualizados)===false){
                http_response_code(500);
                echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
            };

            http_response_code(200);
            
            echo json_encode(["status" => "Success", "message" => "Juego con ID: ".$id." actualizado con exito", "code" => 204], JSON_PRETTY_PRINT)."\n";

        } else{

                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Juego no encontrado", "code" => 404], JSON_PRETTY_PRINT);
            }



        }

        function deleteJuego($id){

            $juegos = file_get_contents(__DIR__ .'/../../data/juegos.json', true);

            // JSON a array
            $jArray = json_decode($juegos, true);

            // Si los datos del fichero no son válidos
            if ($jArray === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos de entrada no validos", "code" => 400], JSON_PRETTY_PRINT);
                exit();
            }

            $encontrado = false;

            // Buscar y eliminar el juego con el ID especificado de la forma clave-valor
            foreach ($jArray as $key => $juego) {
                if ($juego['id'] == $id) {

                    // echo "Juego".json_encode($juego, JSON_PRETTY_PRINT)."\n";

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
                $juegosActualizados = json_encode($jArray, JSON_PRETTY_PRINT);

                // Guarda el JSON actualizado en un archivo
                if (file_put_contents(__DIR__ .'/../../data/juegos.json', $juegosActualizados)===false){
                    http_response_code(500);
                    echo json_encode(["error" => "Error al guardar los datos en la base de datos", "code" => 500], JSON_PRETTY_PRINT);
                };

                http_response_code(200);

                echo json_encode(["status" => "Success", "message" =>  "Juego con ID: ".$id." borrado con exito", "code" => 204], JSON_PRETTY_PRINT);
                
            } else{
                // Hace que el cliente muestre por pantalla el error requerido en lugar de terminar con éxito
                http_response_code(404);

                // Muestra el JSON (array codificado en forma de JSON) de error
                echo json_encode(["error" => "Juego no encontrado", "code" => 404], JSON_PRETTY_PRINT);
            }
        }
    }

?>