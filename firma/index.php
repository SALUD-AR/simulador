<?php

require_once "config.php";

use Phalcon\Http\Response;
use Phalcon\Http\Request;

//registramos el directorio models
$loader = new \Phalcon\Loader();
$loader->registerDirs(array(
    'models'
))->register();

//creamos la instancia de la factoria
$di = new \Phalcon\DI\FactoryDefault();

//creamos la conexión con la base de datos
$di->set('db', function()
{
    return new \Phalcon\Db\Adapter\Pdo\Postgresql(array(
        //"adapter" => $db_type,
        "host" => "localhost",
        "username" => "postgres",
        "password" => "password",
        "dbname" => "simulador"
    ));
});

//instanciamos
$app = new \Phalcon\Mvc\Micro($di);

$app->get('/', function() 
{
    echo "<h1>Simulador de codigos - Firma Digital (api)</h1>";
});

//añadimos un nuevo
$app->post('/callback', function() use ($app) 
{
    //obtenemos el json que se ha enviado 

    $postData = $app->request->getJsonRawBody();
    
    $log = json_encode($postData);
    settype($log,"string");
    
    write_log("Recibiendo Firma","Firma");
    write_log($log,"Firma");
        
    $id_firma = $postData->metadata->Firma;

    foreach ($postData as $key => $value) {
        //echo $key;
        if ($key=="documento"){
            $documento="$value";
			write_log($documento,"Firma");
        } else if($key=="metadata"){
            $metadata=$value;
        } else if ($key=="status"){
            //$status=$value;
        }else {

        }
    }
    
    $status = Firmas::findFirst("id_firma = $id_firma");
    	    
    $registros= "Registros encontrados ".count($status);
    write_log("$registros","Firma");
    
	//creamos una respuesta
    $response = new Phalcon\Http\Response();
	
	if ($status==false){
        // vacio
        $response->setJsonContent(array('estado' => 'ERROR No Firma', 'data' => $postData));		
        write_log("Error No Firma","Firma");
    } else {
        write_log("$status->documento","Firma");
        $status->documento = "$documento";
        $status->status = json_encode($postData->status);
        $status->save();

    	//enviamos los errores
		$errors = array();
		foreach ($status->getMessages() as $message) {
			$errors[] = $message->getMessage();
		}

		if ($errors){
            $response->setJsonContent(array('estado' => 'ERROR', 'messages' => $errors));
            write_log("Error","Firma");
		} else {
            $response->setJsonContent(array('estado' => 'OK', 'data' => $postData));    
            write_log("OK","Firma");
		}
    }

    return $response;
});

//actualizamos
$app->put('/callback/{id:[0-9]+}', function($id) use($app) 
{
    //obtenemos el json que se ha enviado 
    $postData = $app->request->getJsonRawBody();
 
    //creamos la consulta con phql
    $phql = "UPDATE firma SET ...";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id_firma' => $id,
        'documento' => $postData->documento,
        'metadata' => json_encode($postData->metadata),
        'status' => json_encode($postData->status),
        'id_usuario' => 0,
        //'fecha_firma' => current_timestamp,
        //'location' => ' ',
        'msg' => $postData->msg
    ));
 
    //creamos una respuesta
    $response = new Phalcon\Http\Response();
 
    //comprobamos si la actualización se ha llevado a cabo correctamente
    if ($status->success() == true) 
    {
        $response->setJsonContent(array('estado' => 'OK'));
    } 
    else 
    {
        //en otro caso cambiamos el estado http por un 500
        $response->setStatusCode(500, "Internal Error");
 
        $errors = array();
        foreach ($status->getMessages() as $message) 
        {
            $errors[] = $message->getMessage();
        }
        $response->setJsonContent(array('estado' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

//eliminamos por su id
$app->delete('/callback/{id:[0-9]+}', function($id) use ($app) 
{
    //creamos la consulta con phql
    $phql = "DELETE FROM firma WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ));
 
    //creamos una respuesta
    $response = new Phalcon\Http\Response();
 
    //comprobamos si la eliminación se ha llevado a cabo correctamente
    if ($status->success() == true) 
    {
        $response->setJsonContent(array('estado' => 'OK'));
    } 
    else 
    {
        ////en otro caso cambiamos el estado http por un 500
        $response->setStatusCode(500, "Internal Error");
 
        $errors = array();
 
        //mostramos los errores
        foreach ($status->getMessages() as $message) 
        {
            $errors[] = $message->getMessage();
        }
        $response->setJsonContent(array('estado' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

$app->notFound(function() use ($app) 
{
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'La url solicitada no existe!';
});

$app->handle();

function write_log($cadena,$tipo)
{
	$arch = fopen(realpath( '.' )."/logs/firma_".date("Y-m-d").".log", "a+"); 

	fwrite($arch, "[".date("Y-m-d H:i:s.u")." ".$_SERVER['REMOTE_ADDR']." ".
                   $_SERVER['HTTP_X_FORWARDED_FOR']." - $tipo ] ".$cadena."\n");
	fclose($arch);
}

?>