<?php

namespace App\Model;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use App\Model\AutoStructure\Config as ConfigStructure;
use App\Model\Config;

/**
 * ConfigModel
 *
 * Model class for table config.
 *
 * @see Model
 */
class ConfigModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new ConfigStructure;
        $this->flexible_entity_class = '\App\Model\Config';
    }

     public function get($key, $default = null)
     {
         $row = $this->findByPk(compact('key'));

         return ($row !== null) ? $row->getValue() : $default;
     }
}
