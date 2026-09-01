-- =====================================================
-- Migration: Usergroups access (UX matrix + SELECT_* menus)
-- Date: 2026-08-31
-- =====================================================

ALTER TABLE `usergroup`
  MODIFY `user_id` int(11) NOT NULL DEFAULT 0,
  MODIFY `parent_id` int(11) NOT NULL DEFAULT 0;

-- Keep the lowest usergroup_permisions_id per pair, then unique
DELETE `ugp1` FROM `usergroup_permisions` `ugp1`
INNER JOIN `usergroup_permisions` `ugp2`
  ON `ugp1`.`usergroup_id` = `ugp2`.`usergroup_id`
 AND `ugp1`.`permision_id` = `ugp2`.`permision_id`
 AND `ugp1`.`usergroup_permisions_id` > `ugp2`.`usergroup_permisions_id`;

SET @uq_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'usergroup_permisions'
    AND index_name = 'uq_usergroup_permision'
);
SET @uq_sql := IF(
  @uq_exists = 0,
  'ALTER TABLE `usergroup_permisions` ADD UNIQUE KEY `uq_usergroup_permision` (`usergroup_id`, `permision_id`)',
  'SELECT 1'
);
PREPARE stmt FROM @uq_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_USERGROUPS', 'View groups', 'users', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_USERGROUPS'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_USERGROUP', 'Add group', 'users', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_USERGROUP'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_USERGROUP', 'Update group', 'users', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_USERGROUP'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_USERGROUP', 'Delete group', 'users', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_USERGROUP'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_FRAGMENTS', 'View fragments', 'fragments', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_FRAGMENTS'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_GALLERY', 'View gallery', 'gallery', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_GALLERY'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_VIDEOS', 'View videos', 'videos', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_VIDEOS'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_CALENDAR', 'View calendar', 'calendar', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_CALENDAR'
);

INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ug.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup` ug
INNER JOIN `permisions` p ON p.`permision_name` IN (
  'SELECT_USERGROUPS',
  'CREATE_USERGROUP',
  'UPDATE_USERGROUP',
  'DELETE_USERGROUP',
  'SELECT_FRAGMENTS',
  'SELECT_GALLERY',
  'SELECT_VIDEOS',
  'SELECT_CALENDAR',
  'PUBLISH_FORM_CUSTOM'
)
WHERE ug.`usergroup_id` IN (1, 2)
  AND NOT EXISTS (
    SELECT 1
    FROM `usergroup_permisions` ugp
    WHERE ugp.`usergroup_id` = ug.`usergroup_id`
      AND ugp.`permision_id` = p.`permisions_id`
  );
