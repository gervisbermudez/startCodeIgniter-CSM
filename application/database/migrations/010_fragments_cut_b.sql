-- =====================================================
-- Migration: Fragments cut B (permisions)
-- Date: 2026-08-31
-- MySQL 5.7. Idempotent. Apply by hand (CI3 migrations is not the flow).
-- INSERT only — no ALTER on fragmentos.
-- =====================================================

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_FRAGMENT', 'View fragments', 'fragments', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_FRAGMENT');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_FRAGMENT', 'Add fragment', 'fragments', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_FRAGMENT');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_FRAGMENT', 'Update fragment', 'fragments', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_FRAGMENT');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_FRAGMENT', 'Delete fragment', 'fragments', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_FRAGMENT');

-- Same usergroups that already have SELECT_PAGES
INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ugp.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup_permisions` ugp
INNER JOIN `permisions` sp ON sp.`permisions_id` = ugp.`permision_id` AND sp.`permision_name` = 'SELECT_PAGES'
INNER JOIN `permisions` p ON p.`permision_name` IN (
  'SELECT_FRAGMENT', 'CREATE_FRAGMENT', 'UPDATE_FRAGMENT', 'DELETE_FRAGMENT'
)
WHERE NOT EXISTS (
  SELECT 1 FROM `usergroup_permisions` x
  WHERE x.`usergroup_id` = ugp.`usergroup_id`
    AND x.`permision_id` = p.`permisions_id`
);
