-- =============================================================
-- Aislum Studio — Business Card Maker Schema
-- Extends database/schema.sql  (run after init.php)
-- Compatible with both SQLite and MySQL.
-- =============================================================

-- -------------------------------------------------------------
-- card_templates
-- Built-in (and future user-created) design starting points.
-- content_json stores the full element tree the editor loads.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS card_templates (
    id               INTEGER PRIMARY KEY AUTO_INCREMENT,
    name             VARCHAR(255)  NOT NULL,
    style_tag        VARCHAR(100)  NOT NULL DEFAULT 'classic',
    description      TEXT,
    thumbnail_path   VARCHAR(500),
    default_size_key VARCHAR(50)   NOT NULL DEFAULT 'standard_us',
    content_json     TEXT          NOT NULL,
    sort_order       INTEGER       DEFAULT 0,
    is_active        BOOLEAN       DEFAULT 1,
    created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------------
-- card_designs
-- A user's saved card.  Each design is one face-pair (front +
-- optional back) stored in content_json.  The chosen card size,
-- template origin, and a generated thumbnail are also stored.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS card_designs (
    id               INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id          INTEGER       NOT NULL,
    template_id      INTEGER,
    title            VARCHAR(255)  NOT NULL DEFAULT 'Untitled Card',
    size_key         VARCHAR(50)   NOT NULL DEFAULT 'standard_us',
    content_json     TEXT          NOT NULL,
    thumbnail_path   VARCHAR(500),
    is_deleted       BOOLEAN       DEFAULT 0,
    created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id)          ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES card_templates(id) ON DELETE SET NULL
);

-- -------------------------------------------------------------
-- Indexes
-- -------------------------------------------------------------
CREATE INDEX IF NOT EXISTS idx_card_designs_user_id     ON card_designs(user_id);
CREATE INDEX IF NOT EXISTS idx_card_designs_template_id ON card_designs(template_id);
CREATE INDEX IF NOT EXISTS idx_card_templates_active    ON card_templates(is_active, sort_order);
