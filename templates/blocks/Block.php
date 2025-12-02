<?php

abstract class Block {
    public string $type;
    public string $display_name;
    public array $fields;
    public array $layouts;
    public array $values;
    public int $display_order;

    public function __construct(
        $type,
        $display_name,
        $fields = [],
        $layouts = [],
        $values = [],
        $display_order = 0
        ) {
        $this->type = $type;
        $this->display_name = $display_name;
        $this->fields = $fields;
        $this->layouts = $layouts;
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
    public function renderAdmin($values = [])
    {
        $this->setValues($values ?: $this->values);
        $data = $this->normalizeData();

        ?>
        <div class="block-item block-<?= $this->type ?>">
            <?php include __DIR__ . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR . 'admin.php'; ?>
        </div>
        <?php
    }

    // Display block on site
    abstract public function renderFrontend($values);
}