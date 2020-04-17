ALTER TABLE `media_items` ADD COLUMN `thumbWidth` INT NOT NULL after `height`;
ALTER TABLE `media_items` ADD COLUMN `thumbHeight` INT NOT NULL after `thumbWidth`;

UPDATE media_items as media
    INNER JOIN images as image
    ON image.id = media.imageId
SET media.thumbWidth = image.width, media.thumbHeight = image.height;
