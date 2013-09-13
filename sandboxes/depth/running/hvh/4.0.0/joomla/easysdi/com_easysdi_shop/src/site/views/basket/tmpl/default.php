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
JText::script('COM_EASYSDI_SHOP_BASKET_CONFIRM_REMOVE_ITEM');
$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_shop/views/basket/tmpl/basket.js');
?>
<?php if ($this->item->extractions) : ?>
    <script>
        var request;
        var current_id;

        function removeFromBasket(id) {
            current_id = id;
            jQuery('#modal-dialog-remove-body-text').text(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_CONFIRM_REMOVE_ITEM'));
            jQuery('#modal-dialog-remove').modal('show');
        }

        function actionRemove() {
            initRequest();
            var query = "index.php?option=com_easysdi_shop&task=removeFromBasket&id=" + current_id;
            request.onreadystatechange = reloadBasketContent;
            request.open("GET", query, true);
            request.send(null);
        }

        function reloadBasketContent() {
            if (request.readyState == 4) {
                updateBasketContent();
                jQuery('#' + current_id).remove();
                current_id = null;
            }
        }
    </script>





    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=basket'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

        <div class="basket-edit front-end-edit">
            <h1><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_TITLE'); ?></h1>

            <div class="well">
                <div class="row-fluid">
                    <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_EXTRACTION_NAME'); ?></h3>
                    <table class="table table-striped">

                        <tfoot>
                        </tfoot>
                        <tbody>
                            <?php foreach ($this->item->extractions as $extraction) : ?>
                                <tr id="<?php echo $extraction->id; ?>">
                                    <td>
                                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.edit&id=' . (int) $extraction->resource); ?>"><?php echo $extraction->name; ?></a>
                                        <div class="small"><?php echo $extraction->organism; ?></div>
                                        <div class="accordion" id="accordion_<?php echo $extraction->id; ?>_properties">
                                            <div class="accordion-group">
                                                <div class="accordion-heading">
                                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_<?php echo $extraction->id; ?>_properties" href="#<?php echo $extraction->id; ?>_collapse">
                                                        <?php echo JText::_("COM_EASYSDI_SHOP_BASKET_EXTRACTION_PROPERTIES"); ?>
                                                    </a>
                                                </div>
                                                <div id="<?php echo $extraction->id; ?>_collapse" class="accordion-body collapse">
                                                    <div class="accordion-inner">
                                                        <?php
                                                        foreach ($extraction->properties as $property):
                                                            ?>
                                                            <div class="small"><?php echo $property->name; ?> : 
                                                                <?php
                                                                foreach ($property->values as $value) :
                                                                    if (!empty($value->name)) :
                                                                        echo $value->name;
                                                                    else :
                                                                        echo $value->value;
                                                                    endif;
                                                                    echo', ';
                                                                endforeach;
                                                                ?>
                                                            </div>
                                                            <?php
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-danger btn-mini pull-right" onClick="removeFromBasket(<?php echo $extraction->id; ?>);
                return false;"><i class="icon-white icon-remove"></i></a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row-fluid" >
                    <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER'); ?></h3>
                    <div class="map-recap span6" >

                    </div>
                    <div class="span5" >
                        <a href="#modal-perimeter" class="btn btn-success" style="margin-bottom: 10px;" data-toggle="modal" >
                            <i class="icon-white icon-location"></i>
                            <span id="defineOrderBtn"> Define order perimeter</span></a>
                    </div>

                </div>

                <div class="row-fluid" >
                    <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_THIRD_PARTY'); ?></h3>
                </div>

                <div class="row-fluid " >
                    <div  class="pull-right" >
                        <?php echo $this->getToolbar(); ?>
                    </div>
                    <div class="pull-right">
                        <input type="text" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_BASKET_ORDER_NAME'); ?>">
                    </div>

                </div>
            </div>
        </div>

        <div id="modal-perimeter" style="margin-left:-45%;min-height:500px; width:90%" class="modal show fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Define perimeter</h3>
            </div>
            <div class="modal-body" style="max-height: 500px;">
                <div class="container-fluid" >
                    <div class="row-fluid">
                        <div class="span8">
                            <div  >
                                <?php
                                echo $this->mapscript;
                                ?>
                            </div>
                        </div>

                        <div class="span4">
                            <div class="btn-group" data-toggle="buttons-radio">
                                <?php
                                foreach ($this->item->perimeters as $perimeter):
                                    if ($perimeter->id == 1 ):
                                        if(!$this->item->isrestrictedbyperimeter):
                                        ?>
                                        <a href="#" class="btn" onClick="toggleControl('box');return false;"><i class=" icon-checkbox-unchecked"></i><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_RECTANGLE'); ?></a>
                                        <br>
                                        <br>
                                        <a href="#" class="btn" onClick="toggleControl('polygon');return false;"><i class="icon-chart"></i><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_FREE_PERIMETER_POLYGON'); ?></a>

                                        <br>
                                        <br>                                        
                                        <?php
                                        endif;
                                    elseif ($perimeter->id == 2):
                                        if($this->user->isEasySDI):
                                        ?>
                                        <a href="#" class="btn" onClick="selectMyPerimeter();return false;"><i class="icon-user"></i><?php echo $perimeter->name; ?></a>
                                        <script>
                                            function selectMyPerimeter() {
                                                var gmlLayer = new OpenLayers.Layer.Vector("MyGMLLayer");


                                                var strGML = "<?php echo addslashes ($this->user->gml); ?>";

                                                var gmlOptions = {
                                                    featureType: "feature",
                                                    featureNS: "http://example.com/feature"
                                                };
                                                
                                                var gmlOptionsIn = OpenLayers.Util.extend(
                                                    OpenLayers.Util.extend({}, gmlOptions)
                                                );

                                                var format = new OpenLayers.Format.GML.v3(gmlOptionsIn);

                                                var features = format.read(strGML);

                                                var colors = ['#000000', '#ffffff', '#fefefe'];

                                                for (var x in features) {
                                                    features[x].style = { fillColor: colors[x] };
                                                }


                                                gmlLayer.addFeatures(features);
                                                app.mapPanel.map.addLayer(gmlLayer);
                                                app.mapPanel.map.zoomToExtent(gmlLayer.getDataExtent());
                                            }
                                        </script>
                                        <br>
                                        <br>
                                        <?php
                                        endif;
                                    else:
                                        ?>
                                        <a href="#" class="btn" onClick="selectPerimeter<?php echo $perimeter->id; ?>();return false;"><i class="icon-brush"></i><?php echo $perimeter->name; ?></a>
                                        <script>
                                            function selectPerimeter<?php echo $perimeter->id; ?>() {
                                                clearAll();
                                            
                                                if(app.mapPanel.map.getLayersByName("perimeterLayer").length > 0){
                                                    app.mapPanel.map.removeLayer(perimeterLayer);
                                                }
                                                
                                                perimeterLayer = new OpenLayers.Layer.WMS ("perimeterLayer", 
                                                "<?php echo $perimeter->wmsurl; ?>",
                                                {layers: '<?php echo $perimeter->layername; ?>'})
                                                app.mapPanel.map.addLayer(perimeterLayer);
                                                app.mapPanel.map.setLayerIndex(perimeterLayer, 0); 
                                                

                                                selectControl = new OpenLayers.Control.GetFeature({
                                                    protocol: new OpenLayers.Protocol.WFS({
                                                        version: "1.0.0",
                                                        url:  "<?php echo $perimeter->wfsurl; ?>",
                                                        srsName: "EPSG:900913",
                                                        featureType: "<?php echo $perimeter->featuretypename; ?>",
                                                        featureNS: "<?php echo $perimeter->namespace; ?>",
                                                        geometryName: "<?php echo $perimeter->featuretypefieldgeometry; ?>"
                                                    }),
                                                    box: true,
                                                    hover: true,
                                                    multipleKey: "shiftKey",
                                                    toggleKey: "ctrlKey"
                                                });
                                                
                                                selectControl.events.register("featureselected", this, function(e) {
                                                    selectLayer.addFeatures([e.feature]);
                                                });
                                                selectControl.events.register("featureunselected", this, function(e) {
                                                    selectLayer.removeFeatures([e.feature]);
                                                });
                                                selectControl.events.register("hoverfeature", this, function(e) {
                                                    hover.addFeatures([e.feature]);
                                                });
                                                selectControl.events.register("outfeature", this, function(e) {
                                                    hover.removeFeatures([e.feature]);
                                                });
                                                app.mapPanel.map.addControl(selectControl);
                                                selectControl.activate();
                                                
                                                return false;
                                            }
                                        </script>
                                        <br>
                                        <br>
                                    <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn btn-primary" onclick="newPerim();
            $('#modal-perimeter').modal('hide');">Save perimeter</button>
                </div>
            </div>

            <div id="modal-dialog-remove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_DIALOG_HEADER") ?></h3>
                </div>
                <div class="modal-body">
                    <p><div id="modal-dialog-remove-body-text"></div></p>
                </div>
                <div class="modal-footer">
                    <button onClick="current_id = null;" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_CANCEL") ?></button>
                    <button onClick="actionRemove();" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_REMOVE") ?></button>
                </div>
            </div>
            <script>
                Ext.onReady(function(){
                    app.on("ready", function() {
                        initDraw();
                    });});
            </script>
            </div>
    </form>

    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_BASKET_MESSAGE_EMPTY_BASKET');
endif;
?>
