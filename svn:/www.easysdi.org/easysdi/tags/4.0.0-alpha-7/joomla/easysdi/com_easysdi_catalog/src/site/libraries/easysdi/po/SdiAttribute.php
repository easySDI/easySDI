<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SdiAttribute
 *
 * @author Administrator
 */
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiNamespace.php';

class SdiAttribute {

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
     * @var guid 
     */
    public $guid;

    /**
     *
     * @var string 
     */
    public $type_iso;

    /**
     *
     * @var string 
     */
    public $codelist;
    /**
     *
     * @var string 
     */
    public $pattern;
    /**
     *
     * @var int 
     */
    public $length = 0;
    /**
     *
     * @var boolean 
     */
    public $issystem = false;
    /**
     *
     * @var string 
     */
    public $value;

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
     *
     * @var SdiNamespace 
     */
    private $listeNamespace;

    /**
     * 
     * @param int $id
     * @param string $name
     * @param string $guid
     * @param string $value
     * @param SdiNamespace $namespace
     */
    function __construct($id, $name, $guid = '', $value = '', SdiNamespace $namespace = null, SdiStereotype $stereotype = null, $type_iso = '', $codelist = '', SdiNamespace $listeNamespace = null) {
        $this->id = $id;
        $this->name = $name;
        $this->guid = $guid;
        $this->value = $value;
        $this->namespace = $namespace;
        $this->stereotype = $stereotype;
        $this->type_iso = $type_iso;
        $this->codelist = $codelist;
        $this->listeNamespace = $listeNamespace;
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
        return $this;
    }
    
    /**
     * 
     * @return SdiNamespace
     */
    public function getListeNamespace() {
        return $this->listeNamespace;
    }

    public function setListeNamespace(SdiNamespace $listeNamespace) {
        $this->listeNamespace = $listeNamespace;
        return $this;
    }



}

?>
