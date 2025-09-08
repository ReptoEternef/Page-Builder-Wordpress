<?php

abstract class Block {
    public string $type;
    public array $fields;
    public array $values;
    public int $display_order;

    public function __construct($type, $fields = [], $values = [], $display_order = 0) {
        $this->type = $type;
        $this->fields = $fields;
        $this->values = $values;
        $this->display_order = $display_order;
    }

    protected function normalizeData(): array {
        $data = [];
        foreach ($this->fields as $field) {
            $data[$field] = $this->values[$field] ?? '';
        }
        return $data;
    }

    public function setValues($values) {
        $this->values = $values;
        return $this;
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