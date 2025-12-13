<?php

abstract class Block {
    public string $type;
    public string $display_name;
    public array $fields;
    public array $layouts;
    public array $values;
    public int $display_order;
    public string $id;

    public function __construct(
        $type,
        $display_name,
        $fields = [],
        $layouts = [],
        $values = [],
        $display_order = 0,
        $id = ''
        ) {
        $this->type = $type;
        $this->display_name = $display_name;
        $this->fields = $fields;
        $this->layouts = $layouts;
        $this->values = is_array($values) ? $values : (array) $values;
        $this->display_order = $display_order;
        $this->id = $id;
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
/*         $this->setValues($values ?: $this->values);
        $data = $this->normalizeData(); */
        $data = array_merge($this->values, $this->normalizeData());
    }

    // Display block on site
    abstract public function renderFrontend($values);

    public function getHTML() {
        ob_start();
        include __DIR__ . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR . 'admin.php';
        return ob_get_clean();
    }

    public function generate_block_ID() {
        $id = uniqid();
        $this->id = 'block-' . $this->type . '-' . $id;
    }

}