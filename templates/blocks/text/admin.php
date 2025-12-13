<div class="flex-column wrap full-width" data-order="">
    <strong><?= $this->display_name ?></strong>

    <?php
    if (count($this->layouts) > 1) {
        ?>
        <select name="layout" id="">
            <?php
            if ($this->layouts) {
                foreach ($this->layouts as $layout) {
                    ?> <option value="<?= esc_attr($layout) ?>" id=""><?= esc_attr($layout) ?></option> <?php
                }
            }
            ?>
        </select>
        <?php
    }
    ?>

    <input type="text" name="custom_css" value="<?= $data['custom_css'] ?? ''?>" placeholder="Custom CSS">
        
    <b>Titre</b>
    <textarea class="wysiwyg-h2" name="title" rows="1"></textarea>

    <b>Contenu</b>
    <textarea class="wysiwyg" name="content" rows="8"></textarea>
</div>