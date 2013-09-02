<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<script>
    function updateBasketContent() {
        if (request.readyState == 4) {
            var JSONtext = request.responseText;

            if (JSONtext == "[]") {
                return;
            }

            var JSONobject = JSON.parse(JSONtext, function(key, value) {
                if (key && typeof key === 'string' && key == 'ERROR') {
                    alert(value);
                    return;
                }
                if (value && typeof value === 'string') {
                    jQuery('sdi-basket-content-display').text(value);
                    return;
                }
            });

        }
    }
</script>
<div id="sdi-basket-content" class="sdi-basket-content">
    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=basket', false); ?>"><div id="sdi-basket-content-display">
<?php echo $basketcontent; ?></div></a>
</div>