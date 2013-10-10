<?php

/**
 * Description of SdiResourcetype
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class SdiResourcetype {
    /**
     *
     * @var int 
     */
    public $id;
    /**
     *
     * @var string 
     */
    public $name;
    /**
     *
     * @var string 
     */
    public $fragment;
    
    /**
     *
     * @var SdiNamespace 
     */
    private $namespace;
    
    function __construct($id, $name, $fragment = '', SdiNamespace $namespace = null) {
        $this->id = $id;
        $this->name = $name;
        $this->fragment = $fragment;
        $this->namespace = $namespace;
    }
    
    public function getNamespace() {
        return $this->namespace;
    }

    public function setNamespace(SdiNamespace $namespace) {
        $this->namespace = $namespace;
        return $this;
    }



    
}

?>
