<div class="obwp-block-admin">
    <div class="obwp-block-header">
        <strong class="obwp-block-title"><?= $this->display_name ?></strong>
    </div>

    <div class="obwp-block-body">

        <div class="obwp-content-fields">
            <div class="obwp-field-row">
                <label>Lien de la vidéo</label>
                <input type="text" name="video_link" data-field-trad="notrad" value="<?= $data['video_link'] ?? '' ?>" placeholder="https://youtube.com/watch?v=... ou URL directe .mp4">
            </div>

            <div class="obwp-field-row">
                <div class="obwp-field-col">
                    <label>Largeur</label>
                    <input type="text" name="video_width" data-field-trad="notrad" value="<?= $data['video_width'] ?? '' ?>" placeholder="ex: 100%">
                </div>
                <div class="obwp-field-col">
                    <label>Hauteur</label>
                    <input type="text" name="video_height" data-field-trad="notrad" value="<?= $data['video_height'] ?? '' ?>" placeholder="ex: 500px">
                </div>
            </div>

            <div class="obwp-field-row">
                <label class="obwp-checkbox-label">
                    <input type="checkbox" name="autoplay" data-field-trad="notrad">
                    <span class="prevent-select change-cursor">Autoplay</span>
                </label>
                <label class="obwp-checkbox-label">
                    <input type="checkbox" name="loop" data-field-trad="notrad">
                    <span class="prevent-select change-cursor">Loop</span>
                </label>
            </div>
        </div>

        <?php if (in_array('custom_css', $this->fields)): ?>
        <div class="obwp-advanced-options">
            <input type="text" name="custom_css" data-field-trad="notrad" placeholder="Custom CSS" class="obwp-input-full">
        </div>
        <?php endif; ?>

    </div>
</div>