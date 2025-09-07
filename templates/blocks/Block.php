<?php

abstract class Block {
    public string $type;    // 'hero', 'text', 'carrousel'...
    public array $fields;   // fields for each type

    public function __construct($type, $fields = [], $values = []) {
        $this->type = $type;
        $this->fields = $fields;
        $this->fields = $values;
    }

    // Display block in admin
    abstract public function renderAdmin();

    // Display block on site
    abstract public function renderFrontend($values);

    // Block assets (CSS/JS)
    public function enqueueAssets() {
        // complete later
    }
}