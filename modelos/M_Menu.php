<?php
class M_Menu extends \DB\SQL\Mapper{
    public function __construct()
    {
        parent::__construct(\Base::instance()->get('DB'),'tb_menu');
    }
}