-- =====================================================
-- Migration: Dashboard layout JSON (user override / usergroup default)
-- Date: 2026-09-01
-- MySQL 5.7. Idempotent. Apply by hand (CI3 migrations is not the flow).
-- Additive: new table + UPDATE_DASHBOARD_LAYOUT. No DROP/ALTER of existing columns.
-- =====================================================

CREATE TABLE IF NOT EXISTS `dashboard_layout` (
  `dashboard_layout_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `usergroup_id` int(11) DEFAULT NULL,
  `layout_json` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `date_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dashboard_layout_id`),
  UNIQUE KEY `uk_dashboard_layout_user` (`user_id`),
  UNIQUE KEY `uk_dashboard_layout_group` (`usergroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_DASHBOARD_LAYOUT', 'Change dashboard layout', 'dashboard', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_DASHBOARD_LAYOUT'
);

INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ug.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup` ug
INNER JOIN `permisions` p ON p.`permision_name` = 'UPDATE_DASHBOARD_LAYOUT'
WHERE ug.`usergroup_id` IN (1, 2)
  AND NOT EXISTS (
    SELECT 1
    FROM `usergroup_permisions` ugp
    WHERE ugp.`usergroup_id` = ug.`usergroup_id`
      AND ugp.`permision_id` = p.`permisions_id`
  );
