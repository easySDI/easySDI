<?php
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiNamespace.php';

/**
 * Map the sdi_class table
 *
 * @author Marc Battaglia
 */
class SdiClass {
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
    public $guid;
    /**
     *
     * @var boolean 
     */
    public $isRoot;
    /**
     *
     * @var SdiNamespace 
     */
    private $namespace;
    /**
     *
     * @var SdiStereotype 
     */
    private $stereotype;
    
    /**
     * @param int $id
     * @param string $name
     * @param string $guid
     * @param boolean $isRoot
     */
    function __construct($id, $name, $guid = '', $isRoot = false, SdiNamespace $namespace = null, SdiStereotype $stereotype = null) {
        $this->id = $id;
        $this->name = $name;
        $this->guid = $guid;
        $this->isRoot = $isRoot;
        $this->namespace = $namespace;
        $this->stereotype = $stereotype;
    }

    /**
     * @return SdiNamespace
     */
    public function getNamespace() {
        return $this->namespace;
    }

    public function setNamespace(SdiNamespace $namespace) {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * 
     * @return SdiStereotype
     */
    public function getStereotype() {
        return $this->stereotype;
    }

    public function setStereotype(SdiStereotype $stereotype) {
        $this->stereotype = $stereotype;
    }
    
}

?>
