<?php
class Carrito_Ctrl{
    public $M_Carrito = null;
    public $M_Detalle_Carrito = null;

    public function __construct()
    {
        $this->M_Carrito = new M_Carrito(); 
        $this->M_Detalle_Carrito = new M_Detalle_Carrito(); 
    }

    public function listarxID($f3) {
        $id = $f3->get('PARAMS.id');
        $resultado =$this->M_Carrito->find('id_usr='.$id);
        $items = array();
        
        foreach($resultado as $carrito) {
            $items[] = $carrito->cast();
        }

        // Codificar el json
        echo json_encode([
            'mensaje' => count($items) > 0 ? 'Consulta con éxito' : 'No existen datos para mostrar',
            'id' => $id,
            'info' => [
                'items' => $items
            ]
        ]);
    }

    public function crearCarrito($f3) {
        $id = 0;
        $msg = "";
        
        $this->M_Carrito->set('id_usr', $f3->get('POST.id_usr'));  
        $this->M_Carrito->set('idPago', $f3->get('POST.idPago'));
        $this->M_Carrito->set('fecha_compra', $f3->get('POST.fecha_compra'));
        $this->M_Carrito->set('direccion', $f3->get('POST.direccion'));
        $this->M_Carrito->set('longitud', $f3->get('POST.longitud'));
        $this->M_Carrito->set('latitud', $f3->get('POST.latitud'));
        $this->M_Carrito->set('total_carrito', $f3->get('POST.total_carrito'));
        $this->M_Carrito->set('id_estado', $f3->get('POST.id_estado'));

        $this->M_Carrito->save();
        $id = $this->M_Carrito->get('idCarritoCompra');
        
        
        if($id > 0 ){
            $productos = json_decode($f3->get('POST.detalle'));
            foreach ($productos as $pro){
                $this->M_Detalle_Carrito->reset();
                $this->M_Detalle_Carrito->set('idCarritoCompra', $id);  
                $this->M_Detalle_Carrito->set('cantidad', $pro->cantidad);
                $this->M_Detalle_Carrito->set('id_producto', $pro->id_producto);
                $this->M_Detalle_Carrito->save();
                //$this->M_Detalle_Carrito->reset();
            }
    
            echo json_encode([
                'status' => true,
                'mensaje' => "Carrito creado con éxito",
                'info' => [
                    'idCarritoCompra'=> $f3->get('POST.id_usr'),
                    'id_usr'=> $f3->get('POST.id_usr'),  
                    'idPago'=> $f3->get('POST.idPago'),
                    'fecha_compra'=> $f3->get('POST.fecha_compra'),
                    'direccion'=> $f3->get('POST.direccion'),
                    'longitud'=> $f3->get('POST.longitud'),
                    'latitud'=> $f3->get('POST.latitud'),
                    'detalle' => $productos,
                    'total_carrito' => $f3->get('POST.total_carrito'),
                    'id_estado' => $f3->get('POST.id_estado'),
                ]
            ]);
        } else {
            echo json_encode([
                'stuatus' => false,
                'mensaje' => "No se pudo crear su carrito",
                'info' => []
            ]);
        } 
    }

    public function updateEstado($f3) {
        $msg = "";
        $info = array();
        $parametro_id = $f3->get('PARAMS.id');
        $this->M_Carrito->load(['idCarritoCompra = ?', $parametro_id]);
        if($this->M_Carrito->loaded() > 0) {
            $carrito = new M_Carrito();
            $carrito->load(['idCarritoCompra = ?', $parametro_id]);
            if($carrito->loaded() > 0) {
                $this->M_Carrito->set('id_estado', $f3->get('POST.id_estado'));
                // Grabar información
                $this->M_Carrito->save();
                $info['id'] = $this->M_Carrito->get('id_estado');
                $msg = "Estado modificado";
            }
        }else{
            $msg = "No existe producto consultado";
            $info['id'] = 0;
        }
        // Codificar json
        echo json_encode([
            'mensaje' => $msg,
            'info' => $info
        ]);
    }
}
