<?php
class Calificacion_Ctrl{
    public $M_Valoracion = null;
  
    public function __construct()
    {
        $this->M_Valoracion = new M_Valoracion(); 
    }

    public function listar($f3) {
        $resultado = $this->M_Valoracion->find();
        $items = array();
        foreach($resultado as $valoracion) {
            $items[] = $valoracion->cast();
        }
        // Codificar objeto json
        echo json_encode([
            'mensaje' => count($items) > 0 ? 'Consulta con éxito' : 'No existen datos para mostrar',
            'total' => count($items),
            'info' => [
                'data' => $items
            ]
        ]);
    }

    public function listarxID($f3) {
        $id = $f3->get('PARAMS.id');
        $resultado = $this->M_Valoracion->find('id_usr='.$id);
        $items = array();
    
        foreach($resultado as $valoracion) {
            $items[] = $valoracion->cast();
        }

        // Codificar el json
        echo json_encode([
            'mensaje' => count($items) > 0 ? 'Consulta con éxito' : 'No existen datos para mostrar',
            'status' => count($items),
            'info' => [
                'data' => $items
            ]
        ]);
    }

    public function newValoracion ($f3){
        $mensaje = "";
        $id = 0;
        $user = new M_Valoracion();
        $user->load(['id_usr=?', $f3->get('POST.id_usr')]);
        
        if($user->loaded()> 0) {
            $id = $f3->get('POST.id_usr');
            $this->M_Valoracion->load(['id_usr = ?', $id]);
            if($this->M_Valoracion->loaded() > 0) {
                $this->M_Valoracion->set('puntos', $f3->get('POST.puntos'));
                $this->M_Valoracion->set('comentario', $f3->get('POST.comentario'));
                $this->M_Valoracion->set('estado', $f3->get('POST.estado'));

                $this->M_Valoracion->save();
                $mensaje = "Se actualizó su calificación";
            }
        } else {
            //insertar un  nuevo registro
            $this->M_Valoracion->set('id_usr',$f3->get('POST.id_usr'));
            $this->M_Valoracion->set('puntos',$f3->get('POST.puntos'));
            $this->M_Valoracion->set('comentario',$f3->get('POST.comentario'));
            $this->M_Valoracion->set('estado',$f3->get('POST.estado'));

            $this->M_Valoracion->save();//se registra en la BD
            $id = $this->M_Valoracion->get('id_usr');
            $mensaje = "Se guardo su calificación con éxito";
        }
    
        echo json_encode([
            'mensaje'=>$mensaje,
            'id'=>$id
        ]);
    }
}