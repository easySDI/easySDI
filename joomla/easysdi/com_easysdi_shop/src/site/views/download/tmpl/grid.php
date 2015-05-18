<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
?>
<?php if ($this->item) : ?>
    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=download.download'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
        <div class="download-confirm front-end-edit">
            <h1><?php
                echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_GRID_TITLE');
                ;
                ?></h1>
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="sdi-map-grid-selection" >
                            <?php
                            echo $this->mapscript;
                            ?>
                        </div>
                    </div>
                    <div class="span12">
                        <div class="sdi-map-feature-selection"> 
                            <div class="sdi-map-feature-selection-name"> 
                                <label><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_GRID_SELECTION_NAME'); ?> : </label>
                                <span></span>
                            </div>
                            <div class="sdi-map-feature-selection-description"> 
                                <label><?php echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_GRID_SELECTION_DESCRIPTION'); ?> : </label>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="span6 offset3 well">
                                <br/>
                                <label class="checkbox">
                                    <input type="checkbox" id="termsofuse" > <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_I_ACCEPT') ?> <a href="<?php echo $this->paramsarray['termsofuse']; ?>" target="_blank"><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_TERMS') ?></a> <?php echo JText::_('COM_EASYSDI_SHOP_BASKET_CONFIRM_OF_USE') ?>
                                </label>
                                <br/>
                                <br/>
                                <button type="submit" id="saveSubmit" name="saveSubmit" disabled="disabled" class="btn btn btn-primary btn-block btn-large"><b><?php echo 'download'; ?></b></button>
                            </div>
                        </div>
                    </div><!--/span-->
                </div><!--/row-->
            </div>
        </div>
        <script>
            js = jQuery.noConflict();
            js(document).ready(function() {
                js('#termsofuse').change(enableSave);
            });
            function enableSave() {
                if (js('#termsofuse').is(':checked') == true && js('#url').val() != '' )
                    js('#saveSubmit').removeAttr('disabled', 'disabled');
                else
                    js('#saveSubmit').attr('disabled', 'disabled');
            }


            var perimeterLayer, selectControl, selectLayer;

            Ext.onReady(function() {
                app.on("ready", function() {
                    perimeterLayer = new OpenLayers.Layer.WMS("perimeterLayer",
                            "<?php echo $this->item->perimeter->wmsservice->resourceurl; ?>",
                            {layers: "<?php echo $this->item->perimeter->layername; ?>",
                                transparent: true});
                    selectControl = new OpenLayers.Control.GetFeature({
                        protocol: new OpenLayers.Protocol.WFS({
                            version: "1.0.0",
                            url: "<?php echo $this->item->perimeter->wfsservice->resourceurl; ?>",
                            srsName: app.mapPanel.map.projection,
                            featureType: "<?php echo $this->item->perimeter->featuretypename; ?>",
                            featureNS: "<?php echo $this->item->perimeter->namespace; ?>",
                            geometryName: "<?php echo $this->item->perimeter->featuretypefieldgeometry; ?>"
                        }),
                        box: false,
                        toggleKey: "ctrlKey"
                    });
                    app.mapPanel.map.addLayer(perimeterLayer);
                    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});

                    app.mapPanel.map.addLayer(selectLayer);
                    selectControl.events.register("featureselected", this, listenerFeatureSelected);
                    app.mapPanel.map.addControl(selectControl);
                    selectControl.activate();
                }
                );
            });
            var listenerFeatureSelected = function(e) {
                selectLayer.removeAllFeatures();
                selectLayer.addFeatures([e.feature]);
                js('#url').val(e.feature.attributes.<?php echo $this->item->perimeter->featuretypefieldresource; ?>);
                js('.sdi-map-feature-selection-name span').text(e.feature.attributes.<?php echo $this->item->perimeter->featuretypefieldname; ?>);
                js('.sdi-map-feature-selection-description span').text(e.feature.attributes.<?php echo $this->item->perimeter->featuretypefielddescription; ?>);
                enableSave();
            }
            ;
        </script>
        <input type = "hidden" name = "task" value = "download.download" />
        <input type = "hidden" name = "option" value = "com_easysdi_shop" />
        <input type = "hidden" name = "id" value = "<?php echo $this->item->id; ?>" />
        <input type = "hidden" name = "url" id="url" value = "" />
        <?php echo JHtml::_('form.token'); ?>
    </form>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_DOWNLOAD_UNAVAILABLE');
endif;
?>
