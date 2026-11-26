-- Who sent what from the shared Mailbox (see Helpers_Mailbox::log_sent).
CREATE TABLE IF NOT EXISTS `mailbox_sent_log` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED NOT NULL,
    `username`    VARCHAR(100) NOT NULL DEFAULT '',
    `to_emails`   TEXT NOT NULL,
    `cc_emails`   TEXT NOT NULL,
    `subject`     VARCHAR(500) NOT NULL DEFAULT '',
    `attachments` TEXT NOT NULL,
    `status`      ENUM('sent','failed') NOT NULL,
    `error`       VARCHAR(500) NOT NULL DEFAULT '',
    `ip_address`  VARCHAR(45) NOT NULL DEFAULT '',
    `sent_at`     DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
