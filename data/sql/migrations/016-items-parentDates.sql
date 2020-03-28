ALTER TABLE `items` ADD COLUMN `parentItemDate` datetime(3) DEFAULT NULL after `parentItemType`;

UPDATE items as child
    INNER JOIN items as parent
    ON child.parentItemId = parent.itemId
SET child.parentItemDate = parent.date;
