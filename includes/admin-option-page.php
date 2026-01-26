<?php
// Récupérer les options déjà sauvegardées
$options = get_option('obwp_options', []);

$color = $options['color'] ?? '';
$langs = $options['languages'] ?? 1;
$available_langs = obwp_get_available_langs();

?>
<div class="wrap">
    <h1>Options du thème</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('obwp_options_group'); // sécurité et nonce

        foreach ($available_langs as $lang) {
            echo $lang . '<br>';
        }
        ?>
        
        <div class="obwp-option-lang-cont">
            <span class="obwp-btn">add language</span>

            <?php
            foreach ($available_langs as $index => $lang) {
                ?>
                <div class="lang-input-cont">
                    <input type="text" class="option-item" name="obwp_options[available_langs][<?= $index ?>]" value="<?php echo esc_attr($available_langs[$index] ?? ''); ?>">
                </div>
                <?php
            }
            ?>
            
            <div class="lang-input-template" hidden>
                <div class="lang-input-cont">
                    <input type="text" class="" name="" value="">
                </div>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>
<?php
