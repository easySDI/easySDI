<?php

/**
 * Description of SdiStereotype
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class SdiStereotype {
    /**
     *
     * @var int 
     */
    public $id;
    /**
     *
     * @var string 
     */
    public $value;
    /**
     *
     * @var string 
     */
    public $isocode;
    /**
     *
     * @var string 
     */
    public $defaultpattern;
    /**
     *
     * @var SdiNamespace 
     */
    private $namespace;
    
    function __construct($id, $value, $isocode, $defaultpattern = '', SdiNamespace $namespace = null) {
        $this->id = $id;
        $this->value = $value;
        $this->isocode = $isocode;
        $this->defaultpattern = $defaultpattern;
        $this->namespace = $namespace;
    }

    /**
     * 
     * @return SdiNamespace
     */
    public function getNamespace() {
        return $this->namespace;
    }

    public function setNamespace(SdiNamespace $namespace) {
        $this->namespace = $namespace;
        return $this;
    }

}

?>
