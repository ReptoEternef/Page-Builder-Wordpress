<div class="flex-column wrap full-width" data-order="">
    <strong><?= $this->display_name ?></strong>

    <?php
    if (count($this->layouts) > 1) {
        ?>
        <label for="layout">Layout :</label>
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

    <input type="text" class="full-width" name="title" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
    <textarea class="full-width" rows="4" name="content" placeholder="content"><?= $data['content'] ?? '' ?></textarea>
</div>