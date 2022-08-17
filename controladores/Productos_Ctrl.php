<?php
class Productos_Ctrl{
    public $M_Producto = null;
    public $M_Categoria = null;

    public function __construct()
    {
        $this->M_Producto = new M_Producto();
        $this->M_Categoria = new M_Categoria(); 
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
        $f3->set('UPLOADS','imagenes/');
        $id = 0;
        $msg = "";
        
        $img = $f3->get('POST.avatar');
        $formFieldName = "avatar";
        $overwrite = false; // establecido en verdadero, para sobrescribir un archivo existente; Predeterminado: falso
        $slug = true; // cambiar el nombre del archivo a una versión compatible con el sistema de archivos
        $web = \Web::instance();
        $files = $web->receive(function($img,  $formFieldName){
                if($file['size'] > (2 * 1024 * 1024)) // si es mayor a 2 MB
                    return false; // este archivo no es válido, devolver false omitirá moverlo

                // todo salió bien, hurra!
                return true; // permite que el archivo se mueva del directorio php tmp a su directorio de carga definido
            },
            $overwrite,
            $slug
        );

        $nombre_img = key($files);
        $operacion = $files[$nombre_img];
        $img_value = substr($nombre_img, 8);
        
        if($operacion == true){
            $this->M_Producto->set('idCategoria', $f3->get('POST.idCategoria'));   
            $this->M_Producto->set('cantidad', $f3->get('POST.cantidad'));
            $this->M_Producto->set('descripcion', $f3->get('POST.descripcion'));
            $this->M_Producto->set('nombre_producto', $f3->get('POST.nombre_producto'));
            $this->M_Producto->set('precio', $f3->get('POST.precio'));
            $this->M_Producto->set('foto_producto', $img_value);
        
            $this->M_Producto->save();
            $id = $this->M_Producto->get('id_producto');
            $msg = "Producto guardao con éxito"; 
        } else{
            $msg = "No se pudo guardar la información del Producto";
        }
        
        // Codificar json
        echo json_encode([
            'status' => $operacion,
            'mensaje' => $msg,
            'info' => $id
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