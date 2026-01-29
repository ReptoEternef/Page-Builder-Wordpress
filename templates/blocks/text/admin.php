<div class="flex-column wrap full-width" data-order="">
    <strong><?= $this->display_name ?></strong>

    <div class="obwp-options">
        <?php
        obwp_dropdown($this, 'layout');
        obwp_dropdown($this, 'color_context');
        ?>
    </div>

    <input type="text" name="custom_css" value="<?= $data['custom_css'] ?? ''?>" placeholder="Custom CSS">
        
    <b>Titre</b>
    <textarea class="wysiwyg-h2" name="title" rows="1"></textarea>

    <b>Contenu</b>
    <textarea class="wysiwyg" name="content" rows="8"></textarea>
</div>