<?php
$options        = get_option('obwp_options', []);
$available_langs = obwp_get_available_langs();
$page_transitions = $options['page_transitions'] ?? false;
?>

<div class="wrap">
    <h1>⚙️ Options OBWP</h1>

    <form method="post" action="options.php">
        <?php settings_fields('obwp_options_group'); ?>

        <!-- ================================
             LANGUES
        ================================ -->
        <div class="obwp-option-section">
            <h2 class="obwp-option-section-title">🌐 Langues</h2>
            <p class="description">Langues disponibles sur ce site. La première est la langue par défaut.</p>

            <div class="obwp-option-lang-cont">
                <?php foreach ($available_langs as $index => $lang) : ?>
                    <div class="lang-input-cont">
                        <input
                            type="text"
                            class="option-item regular-text"
                            name="obwp_options[available_langs][<?= $index ?>]"
                            value="<?= esc_attr($lang) ?>"
                            placeholder="ex: fr"
                        >
                    </div>
                <?php endforeach; ?>

                <div class="lang-input-template" hidden>
                    <div class="lang-input-cont">
                        <input type="text" class="" name="" value="" placeholder="ex: en">
                    </div>
                </div>

                <button type="button" class="button obwp-btn">+ Ajouter une langue</button>
            </div>
        </div>

        <!-- ================================
             TRANSITIONS
        ================================ -->
        <div class="obwp-option-section">
            <h2 class="obwp-option-section-title">✨ Transitions de page</h2>
            <p class="description">Active un effet de fondu lors de la navigation entre les pages.</p>

            <label class="obwp-checkbox-label">
                <input
                    type="checkbox"
                    name="obwp_options[page_transitions]"
                    value="1"
                    <?php checked(1, $page_transitions) ?>
                >
                <span>Activer les transitions de page</span>
            </label>
        </div>

        <?php submit_button('Enregistrer les options'); ?>
    </form>
</div>

<style>
.obwp-option-section {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    max-width: 600px;
}
.obwp-option-section-title {
    margin-top: 0;
    font-size: 1rem;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 0.5rem;
    margin-bottom: 0.75rem;
}
.obwp-option-lang-cont {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
}
.lang-input-cont input {
    width: 200px;
}
.obwp-checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}
</style>