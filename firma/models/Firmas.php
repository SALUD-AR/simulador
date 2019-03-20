<?php

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Firmas extends \Phalcon\Mvc\Model

{
 
    /**
     *
     * @var integer
     */
    public $id_firma;
 
    /**
     *
     * @var string
     */
    public $documento;

     /**
     *
     * @var string
     */
    public $documento_enviado;
 
    /**
     *
     * @var string
     */
    public $metadata;
 
    /**
     *
     * @var string
     */
    public $status;
 
    /**
     *
     * @var integer
     */
    public $id_usuario;
 
    /**
     *
     * @var date
     */
    public $fecha_firma;
 
    /**
     *
     * @var string
     */
    public $location;

    /**
     *
     * @var string
     */
    public $msg;
 
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("firma");
    }
 
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id_firma' => 'id_firma', 
            'documento' => 'documento',
			'documento_enviado' => 'documento_enviado',
            'metadata' => 'metadata', 
            'status' => 'status',
            'id_usuario' => 'id_usuario',
            'fecha_firma' => 'fecha_firma',
            'location' => 'location',
            'msg' => 'msg',
			'nrodoc' => 'nrodoc',
			'nombres' => 'nombres',
			'apellido' => 'apellido',
			'sexo' => 'sexo',
			'diagnostico' => 'diagnostico',
			'evolucion' => 'evolucion'
        );
    }

}