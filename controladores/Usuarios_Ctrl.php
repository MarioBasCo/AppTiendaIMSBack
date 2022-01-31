<?php
class Usuarios_Ctrl{
  public $M_Usuario = null;
  
  public function __construct()
  {
    $this->M_Usuario = new M_Usuario(); 
  }
  
  public function login($f3) {
    $usuario = $f3->get('POST.correo');
    $clave = $f3->get('POST.clave');
  
    $msg = "";
    $id = 0;
    $data = array();
    $this->M_Usuario->load(['usr_correo = ? && usr_clave = ?', $usuario, $clave]);
    if ($this->M_Usuario->loaded() > 0) {
      $msg = "Usuario encontrado";
      $data = $this->M_Usuario->cast();
      $id = $this->M_Usuario->get('id_usuario');
    } else{
      $id = 0;
      $msg = "Credenciales Incorrectas";
    }
    // Codificar json
    echo json_encode([
      'mensaje' => $msg,
      'id' => $id,
      'info' => [
        'data' => $data
      ]
    ]);
  }

  public function newUsuario ($f3){
    $mensaje = "";
    $id = 0;
    $user = new M_Usuario();
    $user->load(['usr_correo=?', $f3->get('POST.correo')]);
    if($user->loaded()> 0){
      $mensaje="Existe un usuario registrado con el mismo correo electrónico";
    }else{
      //insertar un  nuevo registro
      $this->M_Usuario->set('id_perfil',$f3->get('POST.id_perfil'));
      $this->M_Usuario->set('nombre',$f3->get('POST.nombre'));
      $this->M_Usuario->set('apellido',$f3->get('POST.apellido'));
      $this->M_Usuario->set('usr_correo',$f3->get('POST.correo'));
      $this->M_Usuario->set('usr_clave',$f3->get('POST.clave'));
      $this->M_Usuario->set('telefono',$f3->get('POST.telefono'));
      $this->M_Usuario->set('direccion',$f3->get('POST.direccion'));

      $this->M_Usuario->save();//se registra en la BD
      $id = $this->M_Usuario->get('id_usuario');
      $mensaje = "Usuario creado con exito";
    }

    echo json_encode([
      'mensaje'=>$mensaje,
      'id'=>$id
    ]);
  }
}