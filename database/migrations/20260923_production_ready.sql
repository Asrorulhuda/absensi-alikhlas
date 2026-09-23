-- Production schema additions for the PHP 8.3 / NodeMCU release.
-- Safe to execute repeatedly on MySQL 8+ and MariaDB 10.3+.

-- MySQL does not support ADD COLUMN IF NOT EXISTS consistently across the
-- hosting versions in use, so perform the existence check explicitly.
CREATE TABLE IF NOT EXISTS `wa_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `siswa_uid` varchar(40) DEFAULT NULL,
  `siswa_nama` varchar(100) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `tipe` varchar(10) DEFAULT NULL,
  `target` varchar(20) DEFAULT NULL,
  `guru_nama` varchar(100) DEFAULT '',
  `phone` varchar(30) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `response` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_uid` (`siswa_uid`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @add_guru_nama = IF(
  EXISTS(
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'wa_logs'
      AND COLUMN_NAME = 'guru_nama'
  ),
  'DO 0',
  'ALTER TABLE `wa_logs` ADD COLUMN `guru_nama` varchar(100) DEFAULT '''' AFTER `target`'
);
PREPARE migration_statement FROM @add_guru_nama;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

CREATE TABLE IF NOT EXISTS `wa_queue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phone` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `siswa_uid` varchar(40) DEFAULT '',
  `siswa_nama` varchar(100) DEFAULT '',
  `kelas` varchar(50) DEFAULT '',
  `tipe` varchar(50) DEFAULT '',
  `target` varchar(50) DEFAULT '',
  `guru_nama` varchar(100) DEFAULT '',
  `status` varchar(20) DEFAULT 'PENDING',
  `attempts` int DEFAULT 0,
  `response` text DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wa_notification`
  (`cnfg_id`, `cnfg_token`, `cnfg_sender`, `cnfg_intro`, `cnfg_status`,
   `cnfg_kbm`, `cnfg_eskul`, `cnfg_kegiatan`, `cnfg_intro_kbm`,
   `cnfg_intro_eskul`, `cnfg_intro_kegiatan`, `cnfg_template_izin`,
   `cnfg_template_sakit`, `cnfg_template_guru_izin`, `cnfg_no_kepsek`)
SELECT 1, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', ''
WHERE NOT EXISTS (SELECT 1 FROM `wa_notification` WHERE `cnfg_id` = 1);
