<?php
class Detalle_Carrito_Ctrl{
    public $M_Detalle_Carrito = null;
    
    public function __construct()
    {
      $this->M_Detalle_Carrito = new M_Detalle_Carrito(); 
    }

    public function crearDetalle($f3) {
        $id = 0;
        $msg = "";
        
        $this->M_Detalle_Carrito->set('idCarritoCompra', $f3->get('POST.idCarritoCompra'));  
        $this->M_Detalle_Carrito->set('cantidad', $f3->get('POST.cantidad'));
        $this->M_Detalle_Carrito->set('id_producto', $f3->get('POST.id_producto'));

        $this->M_Detalle_Carrito->save();
        $id = $this->M_Detalle_Carrito->get('idCarritoCompra');
        $msg = "Detalle Carrito creado con éxito";
    
        echo json_encode([
            'mensaje' => $msg,
            'info' => [
                'id' => $id
            ]
        ]);
    }
}