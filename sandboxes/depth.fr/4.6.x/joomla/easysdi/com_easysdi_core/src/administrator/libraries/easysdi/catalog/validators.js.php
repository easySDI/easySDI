<script type="text/javascript">
    /*
     * Javascript validator for stereotype and attribute
     */
    js = jQuery.noConflict();
<?php
$db = JFactory::getDbo();

$query = $db->getQuery(true);

$query->select('*');
$query->from('#__sdi_sys_stereotype s');
$query->where("defaultpattern <> ''");

$db->setQuery($query);
$stereotypes = $db->loadObjectList();

$query = $db->getQuery(true);

$query->select('a.pattern, a.guid, s.defaultpattern');
$query->from('#__sdi_attribute a');
$query->leftJoin('#__sdi_sys_stereotype s ON s.id = a.stereotype_id');
$query->innerJoin('#__sdi_relation r ON a.id = r.attributechild_id');
$query->innerJoin('#__sdi_relation_profile rp ON r.id = rp.relation_id');
$query->where('rp.profile_id = '.(int)$this->item->profile_id);
$query->where('a.state = 1');
$query->where("a.pattern <>''");

$db->setQuery($query);
$attributes = $db->loadObjectList();
?>

    js('document').ready(function () {

<?php foreach ($stereotypes as $stereotype): ?>
            document.formvalidator.setHandler('sdi<?php echo $stereotype->value; ?>', function (value) {
                regex_0 = new RegExp("<?php echo $stereotype->defaultpattern; ?>");
                if (regex_0.test(value)) {
                    return true;
                } else {
                    return false;
                }
            });
<?php endforeach; ?>

<?php foreach ($attributes as $attribute): ?>
            document.formvalidator.setHandler('sdi<?php echo $attribute->guid; ?>', function (value) {
                regex_0 = new RegExp("<?php echo $attribute->pattern; ?>");
    <?php if (!empty($attribute->defaultpattern)): ?>
                    regex_1 = new RegExp("<?php echo $attribute->defaultpattern; ?>");
    <?php endif; ?>
                if (regex_0.test(value) <?php if (!empty($attribute->defaultpattern)) {
        echo '&& regex_1.test(value)';
    } ?>) {
                    return true;
                } else {
                    return false;
                }
            });
<?php endforeach; ?>
    });
</script>