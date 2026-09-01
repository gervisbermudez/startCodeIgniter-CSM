-- =====================================================
-- Migration: Usergroups access fase 2 (CRUD keys for gallery/videos/fragments)
-- Date: 2026-08-31
-- =====================================================

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_GALLERY', 'Add gallery album', 'gallery', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_GALLERY'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_GALLERY', 'Update gallery', 'gallery', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_GALLERY'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_GALLERY', 'Delete gallery', 'gallery', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_GALLERY'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_VIDEO', 'Add video', 'videos', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_VIDEO'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_VIDEO', 'Update video', 'videos', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_VIDEO'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_VIDEO', 'Delete video', 'videos', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_VIDEO'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_FRAGMENT', 'Add fragment', 'fragments', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_FRAGMENT'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_FRAGMENT', 'Update fragment', 'fragments', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_FRAGMENT'
);

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_FRAGMENT', 'Delete fragment', 'fragments', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_FRAGMENT'
);

INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ug.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup` ug
INNER JOIN `permisions` p ON p.`permision_name` IN (
  'CREATE_GALLERY',
  'UPDATE_GALLERY',
  'DELETE_GALLERY',
  'CREATE_VIDEO',
  'UPDATE_VIDEO',
  'DELETE_VIDEO',
  'CREATE_FRAGMENT',
  'UPDATE_FRAGMENT',
  'DELETE_FRAGMENT'
)
WHERE ug.`usergroup_id` IN (1, 2)
  AND NOT EXISTS (
    SELECT 1
    FROM `usergroup_permisions` ugp
    WHERE ugp.`usergroup_id` = ug.`usergroup_id`
      AND ugp.`permision_id` = p.`permisions_id`
  );
