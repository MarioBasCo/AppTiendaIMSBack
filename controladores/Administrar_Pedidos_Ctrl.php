<?php
class Administrar_Pedidos_Ctrl {
    public $M_Carrito = null;
  
    public function __construct()
    {
        $this->M_Carrito = new M_Carrito(); 
    }

    public function getPedidos($f3){
        $id = $f3->get('PARAMS.id');
        $cadenaSQL = "SELECT c.*, pg.tipoPago, dc.cantidad, p.idCategoria, 
        CONCAT(u.nombre, ' ', u.apellido) AS cliente,
        u.telefono, cat.nombreCat, p.id_producto, p.nombre_producto, 
        p.foto_producto, p.precio, e.detalle_estado
        FROM tb_carritodecompra c
        LEFT JOIN tb_usuarios u ON c.id_usr=u.id_usuario
        LEFT JOIN tb_detallecarritocompras dc ON c.idCarritoCompra = dc.idCarritoCompra
        LEFT JOIN tb_estadocarrito e ON c.id_estado = e.id_estado
        LEFT JOIN tb_pago pg ON c.idPago = pg.idPago
        LEFT JOIN tb_productos p ON p.id_producto = dc.id_producto
        LEFT JOIN tb_categoria cat ON p.idCategoria = cat.idCategoria
        WHERE e.id_estado=".$id;

        $result = $f3->DB->exec($cadenaSQL);
        $data = array();

        if(count($result) > 0){        
            foreach($result as $item) {
                $encontrado = false;

                for ($i = 0; $i <= count($data); $i++) {
                    if( intval($data[$i]->codigo) == intval($item['idCarritoCompra'])){
                        $encontrado = true;
                        break;
                    }
                }

                if($encontrado == false) {
                    $cliente = ['id_usr' => $item['id_usr'], 'nombre' => $item['cliente'], 'telefono' => $item['telefono']];
                    $objCliente = (object) $cliente;

                    $lugar = ['referencia' => $item['direccion'], 'longitud' => $item['longitud'], 'latitud' => $item['latitud']];
                    $objLugar = (object) $lugar;
                                    
                    //separamos del array de la consulta los productos
                    $productos = array();
                    foreach($result as $pro) {
                        if($item['idCarritoCompra'] == $pro['idCarritoCompra']){
                            unset($pro['idCarritoCompra']);
                            unset($pro['id_usr']);
                            unset($pro['cliente']);
                            unset($pro['telefono']);
                            unset($pro['fecha_compra']);
                            unset($pro['total_carrito']);
                            unset($pro['idPago']);
                            unset($pro['tipoPago']);
                            unset($pro['id_estado']);
                            unset($pro['detalle_estado']);
                            unset($pro['direccion']);
                            unset($pro['longitud']);
                            unset($pro['latitud']);
                            $productos[] = $pro;
                        }
                    }

                    $info = [
                        'codigo' => $item['idCarritoCompra'], 
                        'datos_cliente' => $objCliente, 
                        'fecha_compra' => $item['fecha_compra'],
                        'direccion'=> $objLugar,
                        'detalle'=> $productos,
                        'metodo_pago' => $item['tipoPago'],
                        'total_carrito' => $item['total_carrito'],
                        'estado_pedido' => $item['detalle_estado']
                    ]; 
                    $objPed = (object) $info;
                    array_push($data, $objPed);
                }
            }
            

            echo json_encode([
                'status'=>true,
                'mensaje'=>'Datos encontrados',
                'data'=>$data
            ]);
        } else {
            echo json_encode([
                'status'=>false,
                'mensaje'=>'No se encontraron datos'
            ]);
        }
    }


    public function getDetallePedxID($f3) {
        $id = $f3->get('PARAMS.id');
        $cadenaSQL= "SELECT p.id_producto, det.cantidad, p.nombre_producto, p.precio,
        (p.precio*det.cantidad) AS total
        FROM `tb_detallecarritocompras` det 
        INNER JOIN tb_carritodecompra c ON det.idCarritoCompra = c.idCarritoCompra
        INNER JOIN tb_productos p ON p.id_producto = det.id_producto
        WHERE c.idCarritoCompra =".$id;

        $result = $f3->DB->exec($cadenaSQL);

        echo json_encode([
            'status' => count($result) > 0 ? true : false,
            'mensaje' => count($result) > 0 ? 'Consulta con éxito' : 'No existen datos para mostrar',
            'data' => $result
          ]);
    }
}