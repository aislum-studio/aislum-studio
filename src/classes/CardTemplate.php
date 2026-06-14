<?php
/**
 * CardTemplate Class
 *
 * Manages the built-in business card templates.
 * Templates are stored in the card_templates table and loaded
 * as structured PHP arrays (decoded from content_json).
 *
 * Content JSON shape
 * ------------------
 * {
 *   "background": { "color": "#ffffff", "image": null },
 *   "accent":     { "color": "#667eea" },
 *   "texts": [
 *     {
 *       "id":        "name",
 *       "label":     "Full name",
 *       "content":   "Your Name",
 *       "font":      "Inter",
 *       "size":      22,
 *       "color":     "#1a1a2e",
 *       "bold":      true,
 *       "italic":    false,
 *       "align":     "left",
 *       "x":         8,    // % of card width
 *       "y":         18,   // % of card height
 *       "max_width": 84    // % of card width
 *     },
 *     ...
 *   ],
 *   "logo":   { "x": 72, "y": 8,  "width": 20, "height": 20, "visible": false },
 *   "qr":     { "x": 72, "y": 55, "size": 22,  "data": "",   "visible": false },
 *   "shapes": [
 *     { "id": "accent_bar", "type": "rect", "x": 0, "y": 0,
 *       "width": 3, "height": 100, "color": "#667eea" }
 *   ]
 * }
 *
 * All x/y/width/height values are percentages of the card canvas.
 * This makes templates resolution-independent across all card sizes.
 */

require_once __DIR__ . '/../../config/card_sizes.php';

class CardTemplate {
    private static $db = null;

    private static function init() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
    }

    // ------------------------------------------------------------------
    // Read
    // ------------------------------------------------------------------

    /**
     * Return all active templates, ordered by sort_order.
     * content_json is decoded to an array.
     *
     * @return array[]
     */
    public static function getAll() {
        self::init();

        $rows = self::$db->fetchAll(
            'SELECT * FROM card_templates WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        );

        return array_map([self::class, 'decode'], $rows);
    }

    /**
     * Return a single template by ID.
     *
     * @param  int        $id
     * @return array|null
     */
    public static function getById($id) {
        self::init();

        $row = self::$db->fetchOne(
            'SELECT * FROM card_templates WHERE id = ? AND is_active = 1',
            [(int)$id]
        );

        return $row ? self::decode($row) : null;
    }

    /**
     * Return lightweight list (id, name, style_tag, thumbnail_path,
     * default_size_key, description) — no content_json — for grid display.
     *
     * @return array[]
     */
    public static function getList() {
        self::init();

        return self::$db->fetchAll(
            'SELECT id, name, style_tag, description, thumbnail_path, default_size_key
             FROM card_templates
             WHERE is_active = 1
             ORDER BY sort_order ASC, id ASC'
        );
    }

    // ------------------------------------------------------------------
    // Write (admin / seeder use)
    // ------------------------------------------------------------------

    /**
     * Insert a new template. Returns the new ID.
     *
     * @param  array $data  Keys: name, style_tag, description, thumbnail_path,
     *                            default_size_key, content (PHP array), sort_order
     * @return int
     */
    public static function create($data) {
        self::init();
        self::validateContent($data['content'] ?? []);

        return self::$db->insert('card_templates', [
            'name'             => $data['name'],
            'style_tag'        => $data['style_tag']        ?? 'classic',
            'description'      => $data['description']      ?? '',
            'thumbnail_path'   => $data['thumbnail_path']   ?? null,
            'default_size_key' => $data['default_size_key'] ?? 'standard_us',
            'content_json'     => json_encode($data['content'], JSON_UNESCAPED_UNICODE),
            'sort_order'       => $data['sort_order']       ?? 0,
            'is_active'        => 1,
        ]);
    }

    /**
     * Update an existing template's content.
     *
     * @param  int   $id
     * @param  array $data  Same keys as create(), all optional.
     * @return bool
     */
    public static function update($id, $data) {
        self::init();

        $fields = [];
        if (isset($data['name']))             $fields['name']             = $data['name'];
        if (isset($data['style_tag']))        $fields['style_tag']        = $data['style_tag'];
        if (isset($data['description']))      $fields['description']      = $data['description'];
        if (isset($data['thumbnail_path']))   $fields['thumbnail_path']   = $data['thumbnail_path'];
        if (isset($data['default_size_key'])) $fields['default_size_key'] = $data['default_size_key'];
        if (isset($data['sort_order']))       $fields['sort_order']       = (int)$data['sort_order'];
        if (isset($data['content'])) {
            self::validateContent($data['content']);
            $fields['content_json'] = json_encode($data['content'], JSON_UNESCAPED_UNICODE);
        }

        if (empty($fields)) return false;

        return self::$db->update('card_templates', $fields, 'id = ?', [(int)$id]) > 0;
    }

    /**
     * Soft-delete a template (sets is_active = 0).
     *
     * @param  int  $id
     * @return bool
     */
    public static function deactivate($id) {
        self::init();
        return self::$db->update('card_templates', ['is_active' => 0], 'id = ?', [(int)$id]) > 0;
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Decode a raw DB row: parse content_json and attach size config.
     */
    private static function decode($row) {
        $row['content']  = json_decode($row['content_json'], true) ?? [];
        $row['size']     = getCardSize($row['default_size_key']);
        return $row;
    }

    /**
     * Validate the minimum required keys in a content array.
     * Throws InvalidArgumentException on failure so callers know early.
     */
    private static function validateContent($content) {
        $required = ['background', 'texts', 'shapes'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $content)) {
                throw new InvalidArgumentException(
                    "Card template content missing required key: '$key'"
                );
            }
        }
        if (!is_array($content['texts'])) {
            throw new InvalidArgumentException("content.texts must be an array");
        }
    }

    /**
     * Return the canonical blank/empty content structure.
     * Useful as a starting point for new templates or blank designs.
     *
     * @param  string $bgColor     Background color hex
     * @param  string $accentColor Accent color hex
     * @return array
     */
    public static function blankContent($bgColor = '#ffffff', $accentColor = '#667eea') {
        return [
            'background' => ['color' => $bgColor, 'image' => null],
            'accent'     => ['color' => $accentColor],
            'texts'      => [
                self::textElement('name',     'Full name',    'Your Name',        22, '#1a1a2e', true,  false, 'left', 8, 18, 84),
                self::textElement('title',    'Job title',    'Job Title',        13, '#667eea', false, false, 'left', 8, 38, 84),
                self::textElement('company',  'Company',      'Company Name',     12, '#444444', false, false, 'left', 8, 52, 84),
                self::textElement('phone',    'Phone',        '+1 555 000 0000',  11, '#555555', false, false, 'left', 8, 66, 50),
                self::textElement('email',    'Email',        'you@example.com',  11, '#555555', false, false, 'left', 8, 76, 84),
                self::textElement('website',  'Website',      'www.example.com',  11, '#555555', false, false, 'left', 8, 86, 84),
                self::textElement('address',  'Address',      '',                 10, '#777777', false, false, 'left', 8, 94, 84),
            ],
            'logo'   => ['x' => 72, 'y' => 8,  'width' => 20, 'height' => 20, 'visible' => false, 'path' => null],
            'qr'     => ['x' => 72, 'y' => 55, 'size'  => 22, 'data'   => '', 'visible' => false, 'type' => 'url'],
            'shapes' => [],
        ];
    }

    /**
     * Build a text element array.
     * All x/y/max_width values are percentages of card dimensions.
     */
    public static function textElement(
        $id, $label, $content,
        $size, $color,
        $bold, $italic, $align,
        $x, $y, $maxWidth
    ) {
        return compact('id', 'label', 'content', 'size', 'color', 'bold', 'italic', 'align', 'x', 'y')
             + ['font' => 'Inter', 'max_width' => $maxWidth, 'visible' => true];
    }
}
