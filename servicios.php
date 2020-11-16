<?php
//header('Content-Type: application/json');
try {
	//Creamos la conexión PDO por medio de una instancia de su clase
	$cnn = new PDO("mysql:host=localhost;dbname=amal","root","");

	$servicio = "";

	if(isset($_GET['s'])){
		if($_GET['s'] != ""){
			$servicio = $_GET['s'];
		}

		if($servicio == ""){

		}
		// Servicio para obtener dato de usuarios
		else if($servicio == "get_usuarios"){
			$respuesta = $cnn->prepare("SELECT auto, codigo FROM usuarios");

			//Ejecutamos la consulta
			$respuesta->execute();

			//Creamos un array donde almacenaremos la data obtenida
			$usuarios = [];

			//Recorremos la data obtenida
			foreach($respuesta as $res){
				//Llenamos la data en el array
	    		$usuarios[]=$res;
			}

			//Hacemos una impresion del array en formato JSON.
			echo json_encode($usuarios);
		} 
		// servicio de login
		else if($servicio == "login"){
			$codigo = "";
			if(isset($_POST['usuario'])){
				$codigo = $_POST["usuario"];
			}

			$clave = "";
			if(isset($_POST['clave'])){
				$clave = $_POST["clave"];
			}

			$respuesta = $cnn->prepare("SELECT auto, codigo, nombre FROM usuarios WHERE auto = '".$codigo."' AND clave = '".$clave."'");

			$respuesta->execute();
			$count=$respuesta->rowCount();

			// verificamos si no existe el usuario
			if ($count <= 0){
				$data = [ 'mensaje' => 'No existe el usuario!', 'error' => true];
				echo json_encode($data);
			} else {
				$data = [ 'mensaje' => 'usuario encontrado!', 'error' => false, 'data' => ['nombre' => '', 'codigo' => '', 'auto' => '']];
				//si existe el usuario respondemos con los datos
				while($row = $respuesta->fetch(PDO::FETCH_OBJ)){
					//Llenamos la data en el array
	    			$data['data']['nombre'] = $row->nombre;
	    			$data['data']['codigo'] = $row->codigo;
	    			$data['data']['auto'] = $row->auto;	    	
				}
				echo json_encode($data);
			}
		}








	}


} catch (Exception $e) {

	echo $e->getMessage();
	
}
?>