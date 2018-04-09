<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

class OgcFilters {

    const OPERATOR_OR = 'Or';
    const OPERATOR_AND = 'And';
    const OPERATOR_NOT = 'Not';

    /** @var DOMDocument */
    private $dom;

    /** @var string */
    private $ogcUri = 'http://www.opengis.net/ogc';

    /** @var string */
    private $ogcPrefix = 'ogc';
    
    /** @var string */
    private $gmlUri = 'http://www.opengis.net/gml';
    
    /** @var string */
    private $gmlPrefix = 'gml';
            
    function __construct(DOMDocument $dom) {
        $this->dom = $dom;
    }

    /**
     * 
     * @param string $propertyName
     * @param string $literal
     * @return DOMElement
     */
    public function getIsLike($propertyName, $literal) {
        $propertyIsLike = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':PropertyIsLike');
        $propertyIsLike->setAttribute('wildCard', '%');
        $propertyIsLike->setAttribute('singleChar', '_');
        $propertyIsLike->setAttribute('escapeChar', '\\');

        $propertyIsLike->appendChild($this->getProtertyName($propertyName));
        $propertyIsLike->appendChild($this->getLiteral('%' . $literal . '%'));

        return $propertyIsLike;
    }

    /**
     * 
     * @param string $propertyName
     * @param string $literal
     * @return DOMElement
     */
    public function getIsEqualTo($propertyName, $literal) {
        $propertyIsEqualTo = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':PropertyIsEqualTo');

        $propertyIsEqualTo->appendChild($this->getProtertyName($propertyName));
        $propertyIsEqualTo->appendChild($this->getLiteral($literal));

        return $propertyIsEqualTo;
    }

    /**
     * 
     * @param string $propertyName
     * @param string $literal
     * @return DOMElement
     */
    public function getIsLessOrEqual($propertyName, $literal) {
        $propertyIsLessThanOrEqualTo = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':PropertyIsLessThanOrEqualTo');

        $propertyIsLessThanOrEqualTo->appendChild($this->getProtertyName($propertyName));
        $propertyIsLessThanOrEqualTo->appendChild($this->getLiteral($literal));

        return $propertyIsLessThanOrEqualTo;
    }

    /**
     * 
     * @param string $propertyName
     * @param string $literal
     * @return DOMElement
     */
    public function getIsGreatherOrEqual($propertyName, $literal) {
        $propertyIsGreaterThanOrEqualTo = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':PropertyIsGreaterThanOrEqualTo');

        $propertyIsGreaterThanOrEqualTo->appendChild($this->getProtertyName($propertyName));
        $propertyIsGreaterThanOrEqualTo->appendChild($this->getLiteral($literal));

        return $propertyIsGreaterThanOrEqualTo;
    }

    /**
     * 
     * @param string $propertyName
     * @param string $lowerBoundary
     * @param string $upperBoundary
     * @return DOMElement
     */
    public function getIsBetween($propertyName, $lowerBoundary, $upperBoundary) {
        $propertyIsBetween = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':PropertyIsBetween');

        $lowerBoundaryNode = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':LowerBoundary');
        $upperBoundaryNode = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':UpperBoundary');

        $lowerBoundaryNode->appendChild($this->getLiteral($lowerBoundary));
        $upperBoundaryNode->appendChild($this->getLiteral($upperBoundary));

        $propertyIsBetween->appendChild($this->getProtertyName($propertyName));
        $propertyIsBetween->appendChild($lowerBoundaryNode);
        $propertyIsBetween->appendChild($upperBoundaryNode);

        return $propertyIsBetween;
    }

    public function getBBox($minX, $minY, $maxX, $maxY) {
        $bbox = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':BBOX');
        $propertyName = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':PropertyName', 'iso:BoundingBox');
        $envelope = $this->dom->createElementNS($this->gmlUri,  $this->gmlPrefix.':Envelope');
        $lowerCorner = $this->dom->createElementNS($this->gmlUri, $this->gmlPrefix.':lowerCorner',$minX.' '.$minY);
        $upperCorner = $this->dom->createElementNS($this->gmlUri, $this->gmlPrefix.':upperCorner',$maxX.' '.$maxY);
        
        $envelope->appendChild($lowerCorner);
        $envelope->appendChild($upperCorner);
        
        $bbox->appendChild($propertyName);
        $bbox->appendChild($envelope);
        
        return $bbox;
    }

    /**
     * 
     * @return DOMElement
     */
    public function getOperator($type) {
        return $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':' . $type);
    }

    /**
     * 
     * @param string $propertyName
     * @return DOMElement
     */
    private function getProtertyName($propertyName) {
        $propertyNameNode = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':PropertyName', $propertyName);

        return $propertyNameNode;
    }

    /**
     * 
     * @param string $literal
     * @return DOMElement
     */
    private function getLiteral($literal) {
        $literalNode = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':Literal', $literal);

        return $literalNode;
    }

}

?>
