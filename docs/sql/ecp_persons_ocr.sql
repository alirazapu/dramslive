-- ============================================================
-- ecp_persons_ocr.sql
--
-- Schema for the local OCR cache that mirrors the text content of
-- the image columns on ecp.ecp_persons (remote, host 192.168.0.156).
-- Both tables live in the LOCAL aiesplus database (aka the 'default'
-- connection) so all DRAMS Databank ECP searches can hit the local
-- DB first and only round-trip to the remote ecp database when the
-- caller needs the original image / additional columns.
--
-- Run once per environment:
--   mysql -u <user> -p aiesplus              < docs/sql/ecp_persons_ocr.sql
--   mysql -u <user> -p aiesplusbk22032026   < docs/sql/ecp_persons_ocr.sql
-- ============================================================

-- ------------------------------------------------------------
-- 1. The cache table: one row per ecp_persons row, keyed by the
--    remote ecp_persons.id (logical foreign key — cross-database,
--    so no enforced REFERENCES clause).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecp_persons_ocr` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- Logical FK to ecp.ecp_persons.id. Not enforced (different
    -- physical server). UNIQUE so the daemon can UPSERT cleanly.
    `ecp_person_id`   BIGINT UNSIGNED NOT NULL,

    -- Denormalised CNIC from ecp_persons.cnic. Lets us filter by
    -- CNIC against the local table without joining the remote.
    `cnic`            VARCHAR(32) NULL,

    -- The OCR'd text fields. NULL = not yet processed; '' = OCR
    -- ran but produced no recognisable text. Use TEXT (not
    -- VARCHAR) because Urdu text expands to multi-byte sequences
    -- and addresses can be long.
    `name_text`       TEXT NULL COMMENT 'OCR of ecp_persons.name_image_base64',
    `father_text`     TEXT NULL COMMENT 'OCR of ecp_persons.father_image_base64',
    `address_text`    TEXT NULL COMMENT 'OCR of ecp_persons.address_image_base64',

    -- SHA256 of the concatenated source images. Lets the daemon
    -- detect when the upstream ecp_persons row's images change
    -- (re-OCR needed). 64 hex chars.
    `source_hash`     CHAR(64) NULL,

    -- Provenance: which engine produced the text + what languages
    -- were configured. Helpful when re-processing with a better
    -- engine — query WHERE ocr_engine = 'tesseract' to find rows
    -- worth re-running with gvision, etc.
    `ocr_engine`      VARCHAR(32) NULL COMMENT 'tesseract | gvision | ...',
    `ocr_lang`        VARCHAR(32) NULL COMMENT 'e.g. eng or eng+urd',

    -- Outcome: ok = at least one of the three text fields is
    -- non-empty. partial = some images yielded text, others
    -- returned empty. empty = none of the source images yielded
    -- text. error = the daemon hit an exception OCRing this row.
    `ocr_status`      ENUM('ok','partial','empty','error')
                      NOT NULL DEFAULT 'ok',
    `error_message`   TEXT NULL,

    `ocr_at`          DATETIME NOT NULL,
    `updated_at`      TIMESTAMP NOT NULL
                      DEFAULT CURRENT_TIMESTAMP
                      ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_ecp_person_id` (`ecp_person_id`),
    KEY `idx_cnic`     (`cnic`),
    KEY `idx_ocr_at`   (`ocr_at`),
    KEY `idx_status`   (`ocr_status`),

    -- Full-text indexes — primary justification for caching
    -- locally. address LIKE '%foo%' on the remote is a full
    -- table scan; MATCH AGAINST here is microseconds.
    FULLTEXT KEY `ft_name`    (`name_text`),
    FULLTEXT KEY `ft_father`  (`father_text`),
    FULLTEXT KEY `ft_address` (`address_text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Local OCR cache for ecp.ecp_persons image fields';


-- ------------------------------------------------------------
-- 2. Daemon run-state / heartbeat table. One row per daemon
--    run. Used for:
--      - "is a daemon currently running?" (status='running')
--      - throughput observability (processed_total, errors_total)
--      - resume-point recovery if a run crashed mid-batch
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecp_ocr_runs` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `started_at`            DATETIME NOT NULL,
    `ended_at`              DATETIME NULL,
    `host`                  VARCHAR(255) NULL COMMENT 'gethostname()',
    `pid`                   INT NULL COMMENT 'PHP process PID',
    `status`                ENUM('running','done','crashed','stopped')
                            NOT NULL DEFAULT 'running',
    `mode`                  VARCHAR(16) NULL COMMENT 'oneshot | daemon',
    `engine`                VARCHAR(32) NULL,
    `lang`                  VARCHAR(32) NULL,
    `last_processed_ecp_id` BIGINT UNSIGNED NULL,
    `processed_total`       INT NOT NULL DEFAULT 0,
    `errors_total`          INT NOT NULL DEFAULT 0,
    `notes`                 TEXT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_status`     (`status`),
    KEY `idx_started_at` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Heartbeat / observability for the ECP OCR daemon';


-- ------------------------------------------------------------
-- Optional: seed an initial empty heartbeat so the dashboard
-- has something to show before the first run finishes.
-- ------------------------------------------------------------
-- INSERT INTO `ecp_ocr_runs` (started_at, host, status, mode, notes)
-- VALUES (NOW(), 'manual-seed', 'done', 'oneshot', 'schema bootstrap');
