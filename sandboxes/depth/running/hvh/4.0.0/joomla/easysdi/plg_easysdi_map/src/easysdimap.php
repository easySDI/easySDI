<?php

/**
 * @version     4.0.0
 * @package     plg_easysdi_content
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

/**
 * 
 *
 * @package     plg_easysdi_map
 * @subpackage  
 * @since       4.0.0
 */
class plgContentEasysdimap extends JPlugin {

    /**
     * 
     * @param object $subject
     * @param array $config
     */
    public function __construct($subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * @param   string	The context of the content being passed to the plugin.
     * @param   object	The article object.  Note $article->text is also available
     * @param   object	The article params
     * @param   integer  The 'page' number
     *
     * @return  void
     * @since   1.6
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0) {
        $canProceed = $context == 'com_easysdi_map';
        if (!$canProceed) {
            return;
        }

        //Load admin language file
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);

        $document = JFactory::getDocument();
        if (JDEBUG) {
            //Load unminify files
            $document->addScript('administrator/components/com_easysdi_core/libraries/ext/adapter/ext/ext-base-debug.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/ux/ext/RowExpander.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/geoext/lib/GeoExt.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/ux/geoext/PrintPreview.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/gxp/script/loader.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/js/sdi.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/widgets/ScaleOverlay.js');
        } else {
            $document->addScript('administrator/components/com_easysdi_core/libraries/ext/adapter/ext/ext-base.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/ux/ext/RowExpander.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/geoext/lib/geoext.min.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/ux/geoext/PrintPreview.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/gxp/script/gxp.min.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/js/sdi.min.js');
            $document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/widgets/ScaleOverlay.js');
        }
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/ext/resources/css/ext-all.css');
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/ext/resources/css/xtheme-gray.css');
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/openlayers/theme/default/style.css');
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/geoext/resources/css/popup.css');
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/geoext/resources/css/layerlegend.css');
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/geoext/resources/css/gxtheme-gray.css');
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/ux/geoext/resources/css/printpreview.css');
        $document->addStyleSheet(JURI::base() . 'administrator/components/com_easysdi_core/libraries/gxp/theme/all.css');

        $document->addStyleSheet(JURI::base() . 'components/com_easysdi_map/views/map/tmpl/easysdi.css');

        $files = glob('administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/*.{js}', GLOB_BRACE);
        foreach ($files as $file) {
            $document->addScript($file);
        }

        $output = '<div id="sdimapcontainer" class="cls-sdimapcontainer"></div>
            <script>';
        $output .= '
            var app;
            var loadingMask;
            Ext.Container.prototype.bufferResize = false;
            Ext.onReady(function(){
                loadingMask = new Ext.LoadMask(Ext.getBody(), {
                msg:"';
        $output .= JText::_('COM_EASYSDI_MAP_MAP_LOAD_MESSAGE');
        $output .= '"
            });
            loadingMask.show();
            var height = Ext.get("sdimapcontainer").getHeight();
            if(!height)  height = Ext.get("sdimapcontainer").getWidth() * 1/2;
            var width = Ext.get("sdimapcontainer").getWidth();
            OpenLayers.ImgPath = "administrator/components/com_easysdi_core/libraries/openlayers/img/";
            GeoExt.Lang.set("';
        $output .= $lang->getTag();
        $output .= '");
            app = new gxp.Viewer(' . $params . ');
                
            app.on("ready", function (){
                loadingMask.hide();
            });

 SdiScaleLineParams= { 
                
   bottomInUnits :"' . $row->bottomInUnits . '",
                bottomOutUnits :"' . $row->bottomOutUnits . '",
                topInUnits :"' . $row->topInUnits . '",
                topOutUnits :"' . $row->topOutUnits . '"
}; 
            Ext.QuickTips.init();
            Ext.apply(Ext.QuickTips.getQuickTip(), {
                maxWidth: 1000
            });
            Ext.EventManager.onWindowResize(function() {
                app.portal.setWidth(Ext.get("sdimapcontainer").getWidth());
                app.portal.setHeight(Ext.get("sdimapcontainer").getWidth() * 1/2);
            });
    	});';
        $output .= '</script>';

        $row->text = html_entity_decode($output);
        return true;
    }

}
