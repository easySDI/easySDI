<?php

/**
 * Map sdi_namespace table
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class SdiNamespace {

    /**
     *
     * @var int 
     */
    public $id;
    /**
     *
     * @var string 
     */
    public $prefix;
    /**
     *
     * @var string 
     */
    public $uri;

    /**
     * 
     * @param int $id Id for the namespace
     * @param string $prefix Prefix ex: gmd, gco
     * @param string $uri Unique URI for the namespace ex: http://www.isotc211.org/2005/gmd
     */
    function __construct($id, $prefix, $uri) {
        $this->id = $id;
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

}

?>
