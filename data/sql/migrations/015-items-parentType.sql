ALTER TABLE `items` ADD COLUMN `parentItemType` varchar(32) DEFAULT NULL after `parentItemId`;

UPDATE items as child
    INNER JOIN items as parent
    ON child.parentItemId = parent.itemId
SET child.parentItemType = parent.itemType
WHERE child.itemId = 'd155a85c-cecb-4432-803c-1307365a39d9';
