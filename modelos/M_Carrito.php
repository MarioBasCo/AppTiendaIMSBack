<?php
class M_Carrito extends \DB\SQL\Mapper{
    public function __construct()
    {
        parent::__construct(\Base::instance()->get('DB'),'tb_carritodecompra');
    }
}