<?php
$processing_alias= JRequest::getVar('processing',null);
$processing= Easysdi_processingHelper::getProcess($processing_alias);
$processing_parameters=json_decode($processing->parameters);
?>

<script>
    jQuery(function(){

        jQuery("select[data-toggleif]").each(function(){
            var obj=jQuery(this);

            var change=function(){
                var target=obj.data('toggleif');
                target=jQuery(target);
                var selected=obj.find(':selected').val();
                target.each(function(){
                    if (jQuery(this).hasClass('if_'+selected)) {
                        jQuery(this).show();
                    } else {
                        jQuery(this).hide();
                    }
                })
            };
            change();
            obj.change(change);
        });

    })
</script>

<h2>Commande: <?php echo $processing->name; ?></h2>
<p><?php echo $processing->description; ?></p>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_processing'); ?>" method="post">

    <input type="hidden" name='processing' value="<?php echo $processing->id; ?>">
    <div class="form-group">
        <label for="name">Nom</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="desc">Description</label>
        <textarea class="form-control" id="desc" name="desc"></textarea>
    </div>


    <fieldset id ="fieldset_download">
        <legend>Accès aux données</legend>
        <div id="div_download">
            <div class="form-group">
                <label for="fileaccess_type">Type d'accès</label>
                <select name="fileaccess_type" id="fileaccess_type" data-toggleif="#fieldset_download .toggleif">
                    <option value="upload">Upload</option>
                    <option value="url">URL (http,ftp)</option>
                </select>
            </div>

            <div class='if_upload toggleif'>
                <div class="form-group">
                    <label for="upload">Fichier</label>
                    <input id="upload" name="upload" type="file">
                </div>
            </div>

            <div class='if_url toggleif'>
                <div class="form-group">
                    <label for="url">URL du fichier</label>
                    <input id="url" name="url" type="text">
                </div>

                <div class="form-group">
                    <label for="login">Login (facultatif)</label>
                    <input id="login" name="login" type="text">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe (facultatif)</label>
                    <input id="password" name="password" type="text">
                </div>
            </div>

        </div>
    </fieldset>

    <?php

    if (null !== $processing_parameters) {
        ?>
        <fieldset id ="fieldset_params">
            <legend>Paramètres du traitement</legend>
            <?php
            foreach ($processing_parameters as $param) {
                ?>
                <div class="form-group">
                    <?php echo Easysdi_processingParamsHelper::label($param); ?>
                    <?php echo Easysdi_processingParamsHelper::input($param); ?>
                </div>
                <?php
            }
            ?>
        </fieldset>
        <?php
    }
    ?>
    <br>
    <button class="btn btn-primary" type="submit">Submit</button>

</form>
