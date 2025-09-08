<div class="flex-column wrap">
    <strong><?= $this->display_name ?></strong>
    <div class="flex-row">

        <?php
        if (count($this->layouts) > 1) {
            ?>
            <!-- <label for="layout">Layout :</label> -->
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
        
        <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" placeholder="Titre">
        <input type="text" name="slogan" value="<?= $data['slogan'] ?? '' ?>" placeholder="Slogan">    
    </div>
    
    <div class="block-field" data-name="background">
        <label for="image">Background</label>
        <!-- <input type="text" hidden class="hero-image" name="image" value="" placeholder="image"> -->
        <button type="button" class="button select-media">Choisir une image</button>

        <div class="preview-container"></div>
    </div>
</div>
