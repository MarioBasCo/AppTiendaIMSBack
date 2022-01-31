<?php
class Categorias_Ctrl{
  public $M_Categoria = null;
  
  public function __construct()
  {
    $this->M_Categoria = new M_Categoria(); 
  }

  public function listar($f3) {
    $resultado = $this->M_Categoria->find();
    $items = array();
    foreach($resultado as $categoria) {
      $items[] = $categoria->cast();
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
}