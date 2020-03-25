-- Move from storing parentItemId as a reference to the table's id to a reference to the id of an entry in the parent table
ALTER TABLE `items` MODIFY COLUMN `parentItemId` varchar (36) default  NULL;

-- Connect photo and video items to their parent album
UPDATE items
    INNER JOIN media_items
    ON items.itemId = media_items.id
SET items.parentItemId = media_items.albumId
WHERE items.itemType in ('photo', 'video');

-- Comments: save the parent id directly, not the parent's item entry id
UPDATE items
    INNER JOIN comments
    ON items.itemId = comments.id
SET items.parentItemId = comments.parentId
WHERE items.itemType in ('comment');

-- Hide Image notifications
UPDATE items SET notification = 'silent' where itemType = 'image';