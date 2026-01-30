<?php

abstract class Block {
    public string $type;
    public string $display_name;
    public array $fields;
    public array $layouts;
    public array $values;
    public int $display_order;
    public string $id;
    public string $html;
    protected string $block_path;

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
        
        // Déterminer le bon chemin (enfant prioritaire)
        $this->block_path = $this->resolveBlockPath();
    }

    // Résout le chemin du bloc (enfant > parent)
    protected function resolveBlockPath(): string {
        if (is_child_theme()) {
            $child_path = get_stylesheet_directory() . '/templates/blocks/' . $this->type;
            if (is_dir($child_path)) {
                return $child_path;
            }
        }
        
        return get_template_directory() . '/templates/blocks/' . $this->type;
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
        $data = array_merge($this->values, $this->normalizeData());
    }

    // Display block on site
    abstract public function renderFrontend($values);

    // Utilise $this->block_path au lieu de __DIR__
    public function getHTML() {
        ob_start();
        include $this->block_path . DIRECTORY_SEPARATOR . 'admin.php';
        return ob_get_clean();
    }

    public function generate_block_ID() {
        $id = uniqid();
        $this->id = 'block-' . $this->type . '-' . $id;
    }
    
    // Helper statique pour résoudre le chemin avant l'instanciation
    protected static function resolveBlockPathStatic($block_type): string {
        if (is_child_theme()) {
            $child_path = get_stylesheet_directory() . '/templates/blocks/' . $block_type;
            if (is_dir($child_path)) {
                return $child_path;
            }
        }
        
        return get_template_directory() . '/templates/blocks/' . $block_type;
    }
}