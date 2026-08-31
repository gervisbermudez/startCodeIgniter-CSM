-- =====================================================
-- Migration: Siteforms (public forms) permissions
-- Date: 2026-08-30
-- =====================================================

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_SITEFORMS', 'View public forms', 'siteforms', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_SITEFORMS'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_SITEFORM', 'Add public form', 'siteforms', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_SITEFORM'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_SITEFORM', 'Update public form', 'siteforms', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_SITEFORM'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_SITEFORM', 'Delete public form', 'siteforms', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_SITEFORM'
);

INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ug.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup` ug
INNER JOIN `permisions` p ON p.`permision_name` IN (
  'SELECT_SITEFORMS', 'CREATE_SITEFORM', 'UPDATE_SITEFORM', 'DELETE_SITEFORM'
)
WHERE ug.`usergroup_id` IN (1, 2)
  AND NOT EXISTS (
    SELECT 1
    FROM `usergroup_permisions` ugp
    WHERE ugp.`usergroup_id` = ug.`usergroup_id`
      AND ugp.`permision_id` = p.`permisions_id`
  );
