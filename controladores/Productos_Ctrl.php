<?php
class Productos_Ctrl{
    public $M_Producto = null;
    public function __construct()
    {
        $this->M_Producto = new M_Producto();
    }

    // Listar
    public function listar($f3) {
        $resultado = $this->M_Producto->find();
        $items = array();
        foreach($resultado as $producto) {
            $items[] = $producto->cast();
        }
        // Codificar objeto json
        echo json_encode([
            'mensaje' => count($items) > 0 ? 'Consulta con éxito' : 'No existen datos para mostrar',
            'total' => count($items),
            'info' => [
                'items' => $items
            ]
        ]);
    }

    
    // Listar por ID
    public function listarxID($f3) {
        $id = $f3->get('PARAMS.id');
        $resultado = $this->M_Producto->find('idCategoria='.$id);
        $items = array();
        
        foreach($resultado as $producto) {
            $items[] = $producto->cast();
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

    // Listar por nombre
    public function buscarXNombre($f3) {
        $nombre = $f3->get('PARAMS.nombre');
        $items= $f3->DB->exec('SELECT pro.*, cat.nombreCat 
        FROM tb_productos pro 
        LEFT JOIN tb_categoria cat ON pro.idCategoria = cat.idCategoria
        WHERE pro.nombre_producto like "%'.$nombre.'%"');

        // Codificar el json
        echo json_encode([
            'mensaje' => count($items) > 0 ? 'Consulta con éxito' : 'No existen datos para mostrar',
            'total' => count($items),
            'info' => [
                'items' => $items
            ]
        ]);
    }
    

    // Añadir
    public function newProducto($f3) { 
        $id = 0;
        $msg = "";
        $producto = new M_Productos();
        $producto->load(['PROD_CODIGO = ?', $f3->get('POST.codigo')]);
        if($producto->loaded() > 0) {
            $msg = "Código de producto ya registrado";
        }else{
            // Setear el resto de campos
                                    // BD                 Nuevo Parámetro
            $this->M_Producto->set('CAT_ID', $f3->get('POST.idCategoria'));   // ¿Cómo validar por id de categoría? Ejemplo: Se ingresa una categoría que no existe
            $this->M_Producto->set('PROD_CODIGO', $f3->get('POST.codigo'));
            $this->M_Producto->set('PROD_NOMBRE', $f3->get('POST.nombre'));
            $this->M_Producto->set('PROD_STOCK', $f3->get('POST.stock'));
            $this->M_Producto->set('PROD_PRECIO', $f3->get('POST.precio'));
            $this->M_Producto->set('PROD_IMAGEN', $f3->get('POST.imagen'));
            $this->M_Producto->set('PROD_ESTADO', $f3->get('POST.estado'));
            // Grabar información
            $this->M_Producto->save();
            $id = $this->M_Producto->get('PROD_ID');
            $msg = "Producto creado";
        }
        // Codificar json
        echo json_encode([
            'mensaje' => $msg,
            'info' => [
                'id' => $id
            ]
        ]);
    }

    
    // Actualizar o Modificar
    public function updateProducto($f3) {
        $msg = "";
        $info = array();
        $parametro_id = $f3->get('PARAMS.id');
        $this->M_Producto->load(['PROD_ID = ?', $parametro_id]);
        if($this->M_Producto->loaded() > 0) {
            $producto = new M_Productos();
            $producto->load(['PROD_ID = ? OR PROD_CODIGO = ?', $parametro_id, $f3->get('POST.codigo')]);
            if($producto->loaded() > 0 && $producto->loaded() < 2) {
                // Setear el resto de campos
                                    // BD                 Nuevo Parámetro
                $this->M_Producto->set('CAT_ID', $f3->get('POST.idCategoria'));   // ¿Cómo validar por id de categoría? Ejemplo: Se ingresa una categoría que no existe
                $this->M_Producto->set('PROD_CODIGO', $f3->get('POST.codigo'));
                $this->M_Producto->set('PROD_NOMBRE', $f3->get('POST.nombre'));
                $this->M_Producto->set('PROD_STOCK', $f3->get('POST.stock'));
                $this->M_Producto->set('PROD_PRECIO', $f3->get('POST.precio'));
                $this->M_Producto->set('PROD_IMAGEN', $f3->get('POST.imagen'));
                $this->M_Producto->set('PROD_ESTADO', $f3->get('POST.estado'));
                // Grabar información
                $this->M_Producto->save();
                $info['id'] = $this->M_Producto->get('PROD_ID');
                $msg = "Producto modificado";
            }else{
                $msg = "El producto ya existe";
                $info['id'] = 0;  
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
    

    // Fin espacio para programar funciones
    
} // Fin de la llase