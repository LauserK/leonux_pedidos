<?php
//header('Content-Type: application/json');

function zero_fill ($valor, $long = 0)
{
    return str_pad($valor, $long, '0', STR_PAD_LEFT);
}

try {
	//Creamos la conexión PDO por medio de una instancia de su clase
	$cnn = new PDO("mysql:host=localhost;dbname=amal","root","");
	$cnn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$cnn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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
		else if($servicio == "get_cliente"){
			//Servicio para buscar cliente
			$rif = "";
			if(isset($_POST['ci_rif'])){
				$rif = $_POST["ci_rif"];
			}

			$tipo = "";
			if(isset($_POST['radio_tipo'])){
				$tipo = $_POST["radio_tipo"];
			}

			$respuesta = $cnn->prepare("SELECT auto, codigo, razon_social, ci_rif FROM clientes WHERE ci_rif = '".$rif."'");

			$respuesta->execute();

			$count=$respuesta->rowCount();			

			// verificamos si no existe el cliente
			if ($count <= 0){
				$data = [ 'mensaje' => 'No existe el cliente!', 'error' => true];
				echo json_encode($data);
			} else {			
				$data = [ 'mensaje' => 'cliente encontrado!', 'error' => false, 'data' => ['nombre' => '', 'codigo' => '', 'auto' => '', 'ci_rif' => '']];
				//si existe el usuario respondemos con los datos
				while($row = $respuesta->fetch(PDO::FETCH_OBJ)){					
					//Llenamos la data en el array
	    			$data['data']['nombre'] = $row->razon_social;
	    			$data['data']['codigo'] = $row->codigo;
	    			$data['data']['auto'] = $row->auto;
	    			$data['data']['ci_rif'] = $row->ci_rif;	    	
				}				
				echo json_encode($data);
			}		
			
		} 
		else if ($servicio == "create_cliente"){
			//Servicio para buscar cliente
			
			$rif = "";
			if(isset($_POST['ci_rif'])){
				$rif = $_POST["ci_rif"];
			}

			$razon_social = "";
			if(isset($_POST['razon_social'])){
				$razon_social = $_POST["razon_social"];
			}

			$direccion = "";
			if(isset($_POST['direccion'])){
				$direccion = $_POST["direccion"];
			}

			$telefono = "";
			if(isset($_POST['telefono'])){
				$telefono = $_POST["telefono"];
			}

			$contribuyente = "No Contribuyente";
			if(isset($_POST['contribuyente'])){
				$contribuyente = $_POST["contribuyente"];
			}

			if ($rif == "" || $razon_social == "" || $direccion == "" || $telefono == "" || $contribuyente == ""){
				$data = [ 'mensaje' => 'Algun campo esta vacio!', 'error' => true];
				echo json_encode($data);
			} else {

				$cnn->beginTransaction();
				$auto = 0;

				$respuesta = $cnn->prepare("SELECT a_clientes FROM sistema_contadores limit 1");

				$respuesta->execute();

				while($row = $respuesta->fetch(PDO::FETCH_OBJ)){					
					//Llenamos la data en el array			
		    		$auto = $row->a_clientes;	    				   
				}	

				$nuevo = $auto+1;

				$query = "INSERT INTO clientes (auto, codigo, nombre, ci_rif, razon_social, auto_grupo, dir_fiscal, dir_despacho, contacto, telefono, email, website, pais, denominacion_fiscal, auto_estado, auto_zona, codigo_postal, retencion_iva, retencion_islr, auto_vendedor, tarifa, descuento, recargo, estatus_credito, dias_credito, limite_credito, doc_pendientes, estatus_morosidad, estatus_lunes, estatus_martes, estatus_miercoles, estatus_jueves, estatus_viernes, estatus_sabado, estatus_domingo, auto_cobrador, fecha_alta, fecha_baja, fecha_ult_venta, fecha_ult_pago, anticipos, debitos, creditos, saldo, disponible, memo, aviso, estatus, cuenta, iban, swit, auto_agencia, dir_banco, auto_codigo_cobrar, auto_codigo_ingresos, auto_codigo_anticipos, categoria, descuento_pronto_pago, importe_ult_pago, importe_ult_venta, telefono2, fax, celular, abc, fecha_clasificacion, monto_clasificacion) VALUES ('".zero_fill($auto+1, 10)."', '".$rif."', '".$razon_social."', '".$rif."', '".$razon_social."', '0000000001', '".$direccion."', '', '', '".$telefono."', '', '', '', 'No Contribuyente', '0000000001', '0000000001', '', '0.00', '0.00', '0000000001', '', '0.00', '0.00', '', '0', '0.00', '0', '', '', '', '', '', '', '', '', '0000000001', '2020-01-01', '2000-01-01', '2000-01-01', '2000-01-01', '0.00', '0.00', '0.00', '0.00', '0.00', '', '', '', '', '', '', '0000000001', '', '0000000001', '0000000001', '0000000001', 'Eventual', '0.00', '0.00', '0.00', '', '', '', 'C', '2020-01-01', '0.00')";
				
				$respuesta = $cnn->prepare($query);
				/*
				$respuesta->bindValue(':auto', zero_fill($auto+1, 10));
				$respuesta->bindValue(':documento', $rif);
				$respuesta->bindValue(':nombre', $razon_social);
				$respuesta->bindValue(':direccion', $direccion);
				$respuesta->bindValue(':telefono', $telefono);
				*/
				$respuesta->execute();
				$count=$respuesta->rowCount();

				if ($count > 0){
					$query = "UPDATE sistema_contadores SET a_clientes = '". $nuevo ."' WHERE 1";
					$respuesta= $cnn->prepare($query);
					$respuesta->execute();
				}				

				try {
					$cnn->commit();
					$data = [ 'mensaje' => 'Cliente creado!', 'error' => false];
					echo json_encode($data);					
				} catch(PDOException $e) {
            		$cnn->rollBack();    
            		$data = [ 'mensaje' => 'Ocurrio un error, por favor vuelta a intentar o comuniquese con el administrador', 'error' => true];
					echo json_encode($data);        		
        		}	
			}

			
		}
	}


} catch (Exception $e) {

	echo $e->getMessage();
	
}
?>