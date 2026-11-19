<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/02/18
 * Time: 15:33
 */

namespace AcMarche\Volontariat\InterfaceDef;

interface Uploadable
{
    /**
     * @return string
     */
    public function getPath();

    public function getId();
}
