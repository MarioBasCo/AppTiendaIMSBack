<?php
class M_Valoracion extends \DB\SQL\Mapper{
    public function __construct()
    {
        parent::__construct(\Base::instance()->get('DB'),'tb_valoraciones');
    }
}