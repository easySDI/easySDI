<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiClass.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiAttribute.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiNamespace.php';

/**
 * Description of Relation
 *
 * @author Administrator
 */
class SdiRelation {

    // Enum type
    public static $CLASS = 1;
    public static $ATTRIBUT = 2;
    public static $RELATIONTYPE = 3;
    // Enum renderType
    public static $TEXTAREA = 1;
    public static $CHECKBOX = 2;
    public static $RADIOBUTTON = 3;
    public static $LIST = 4;
    public static $TEXTBOX = 5;
    public static $DATE = 6;
    public static $DATETIME = 7;

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
    public $childtype_id;

    /**
     *
     * @var string 
     */
    public $guid;

    /**
     *
     * @var int 
     */
    public $lowerbound;

    /**
     *
     * @var int 
     */
    public $upperbound;

    /**
     *
     * @var int 
     */
    public $rendertype;

    /**
     *
     * @var int Default 0 
     */
    public $level = 0;

    /**
     *
     * @var int 
     */
    private $index = 0;

    /**
     *
     * @var int 
     */
    public $occurance = 0;

    /**
     *
     * @var int 
     */
    public $ordering = 0;

    /**
     *
     * @var boolean 
     */
    public $isEmpty = false;

    /**
     *
     * @var string 
     */
    public $serializedXpath;

    /**
     *
     * @var SdiClass 
     */
    private $parent;

    /**
     *
     * @var SdiClass 
     */
    private $class_child;

    /**
     *
     * @var SdiAttribute 
     */
    private $attribut_child;

    /**
     *
     * @var SdiNamespace 
     */
    private $namespace;

    /**
     *
     * @var SdiResourcetype 
     */
    private $resoucetype;

    /**
     *
     * @var array 
     */
    private $xpath = array();

    /**
     * 
     * @param int $id ID of the relation
     * @param string $name Name of the relation
     * @param int $type Type of the relation. Look Enum type. 
     * @param string $guid Guid of the relation
     * @param int $rendertype Render type in case of "to attribute" relation
     * @param int $lowerbound Lowerbound of the relation
     * @param int $upperbound Upperbound of the relation
     * @param SdiClass $parent Parent Class for the relation
     * @param SdiClass $class_child Child Class for the relation
     * @param SdiAttribute $attribut_child Attribute child for the relation
     * @param SdiNamespace $namespace Namespace of the relation
     */
    function __construct($id, $name, $childtype_id, $guid = '', $rendertype = 0, $lowerbound = 1, $upperbound = 1, SdiClass $parent = NULL, SdiClass $class_child = NULL, SdiAttribute $attribut_child = NULL, SdiNamespace $namespace = NULL) {
        $this->id = $id;
        $this->name = $name;
        $this->childtype_id = $childtype_id;
        $this->guid = $guid;
        $this->lowerbound = $lowerbound;
        $this->upperbound = $upperbound;
        $this->rendertype = $rendertype;
        $this->parent = $parent;
        $this->class_child = $class_child;
        $this->attribut_child = $attribut_child;
        $this->namespace = $namespace;
    }

    /**
     * @return SdiClass
     */
    public function getParent() {
        return $this->parent;
    }

    public function setParent(SdiClass $parent) {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return SdiClass
     */
    public function getClass_child() {
        return $this->class_child;
    }

    public function setClass_child(SdiClass $class_child) {
        $this->class_child = $class_child;
        return $this;
    }

    /**
     * @return SdiAttribute
     */
    public function getAttribut_child() {
        return $this->attribut_child;
    }

    public function setAttribut_child(SdiAttribute $attribut_child) {
        $this->attribut_child = $attribut_child;
        return $this;
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

    public function setXpath(array $xpath) {
        $this->xpath = $xpath;
    }

    /**
     * 
     * @return SdiRelation[]
     */
    public function getXpath() {
        return $this->xpath;
    }

    /**
     * 
     * @return int
     */
    public function getIndex() {
        return $this->index;
    }

    public function setIndex($index) {
        $this->index = $index;
        return $this;
    }

    /**
     * 
     * @return SdiResourcetype
     */
    public function getResoucetype() {
        return $this->resoucetype;
    }

    public function setResoucetype(SdiResourcetype $resoucetype) {
        $this->resoucetype = $resoucetype;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getSerializedXpath($withIndex = true) {
        if ($withIndex) {
            return $this->serializedXpath;
        } else {
            $serializedPathWithoutIndex = '';
            $ids = preg_split('/_/', $this->serializedXpath);
            for ($i = 0; $i < count($ids); $i++) {
                if ($i < count($ids) - 1) {
                    $serializedPathWithoutIndex .= $ids[$i] . '_';
                } else {
                    $last = preg_split('/-/', $ids[$i]);
                    $serializedPathWithoutIndex .= $last[0];
                }
            }
            
            return $serializedPathWithoutIndex;
        }
    }

    public function setSerializedXpath($serializedXpath) {
        $this->serializedXpath = $serializedXpath;
        return $this;
    }
    
    public function replaceLastPath(SdiRelation $rel){
        $size = count($this->xpath);
        
        unset($this->xpath[$size-1]);
        
        $this->xpath[$size-1] = $rel;
       
    }

}

?>
