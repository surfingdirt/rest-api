ALTER TABLE `items` ADD COLUMN `parentItemType` varchar(32) DEFAULT NULL after `parentItemId`;

UPDATE items as child
    INNER JOIN items as parent
    ON child.parentItemId = parent.itemId
SET child.parentItemType = parent.itemType;
