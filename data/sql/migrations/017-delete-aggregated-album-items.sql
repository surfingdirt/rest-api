DELETE from items where itemId in (
    SELECT id from media_albums where albumType = 'aggregate'
);