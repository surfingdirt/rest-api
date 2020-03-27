<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class FeedTest extends TestCase
{
  protected function _getLevels($items) {
    $feed = new Api_Feed();
    list($levels) = $feed->buildLevels($items);
    return $levels;
  }

  public function testEmpty() {
    list($level1, $level2, $level3) = $this->_getLevels([]);

    $this->assertEquals($level1, []);
    $this->assertEquals($level2, []);
    $this->assertEquals($level3, []);
  }

  public function testNewSilentAlbum() {
    // Silent items are hidden
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'silent',
      ],
    ]);

    $this->assertEquals($level1, []);
    $this->assertEquals($level2, []);
    $this->assertEquals($level3, []);
  }

  public function testNewAnnouncedAlbum() {
    // Announced new albums are level 1
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
    ]);

    $this->assertEquals($level1, [
      'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
        'children' => [],
      ],
    ]);
    $this->assertEquals($level2, []);
    $this->assertEquals($level3, []);
  }

  public function testNewPhotoInNewAlbum() {
    // Announced new albums are level 1
    // Announced new photos in new albums are hidden (b/c already part of the album)
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
      [
        'id' => 2,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-02 21:18:06.769',
        'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
    ]);

    $this->assertEquals($level1, [
      'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
        'children' => [],
      ],
    ]);
    $this->assertEquals($level2, []);
    $this->assertEquals($level3, []);
  }

  public function testNewPhotoInOldAlbum() {
    // Announced new photos in old albums are level 2
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-02 21:18:06.769',
        'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
    ]);

    $this->assertEquals($level1, []);
    $this->assertEquals($level2, [
      'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-02 21:18:06.769',
        'children' => [
          [
            'id' => 1,
            'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
            'itemType' => 'photo',
            'date' => '2019-07-02 21:18:06.769',
            'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
            'parentItemType' => 'mediaalbum',
            'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
            'notification' => 'announce',
          ],
        ],
      ],
    ]);
    $this->assertEquals($level3, []);
  }

  public function testNewCommentOnNewPhotoInNewAlbum() {
    // Announced new albums are level 1
    // Announced new photos in new albums are hidden
    // Announced new comments on new photos in new albums are hidden
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
      [
        'id' => 2,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-02 21:18:06.769',
        'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
      [
        'id' => 3,
        'itemId' => '2a2f012f-4476-41a9-a37d-931749371228',
        'itemType' => 'comment',
        'date' => '2019-07-02 22:18:06.769',
        'parentItemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'parentItemType' => 'photo',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
    ]);

    $this->assertEquals($level1, [
      'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
        'children' => [],
      ],
    ]);
    $this->assertEquals($level2, []);
    $this->assertEquals($level3, []);
  }

  public function testNewCommentOnNewPhotoInOldAlbum() {
    // Announced new photos in old albums are level 2
    // Announced new comments on new photos are hidden
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'this-is-a-photo-id',
        'itemType' => 'photo',
        'date' => '2019-07-02 21:18:06.769',
        'parentItemId' => 'this-is-an-album-id',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'this-is-a-user-id',
        'notification' => 'announce',
      ],
      [
        'id' => 2,
        'itemId' => 'this-is-a-comment-id',
        'itemType' => 'comment',
        'date' => '2019-07-02 22:18:06.769',
        'parentItemId' => 'this-is-a-photo-id',
        'parentItemType' => 'photo',
        'submitter' => 'this-is-a-user-id',
        'notification' => 'announce',
      ],

    ]);

    $this->assertEquals($level1, []);
    $this->assertEquals($level2, [
      'this-is-an-album-id' => [
        'itemId' => 'this-is-an-album-id',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-02 21:18:06.769',
        'children' => [
          [
            'id' => 1,
            'itemId' => 'this-is-a-photo-id',
            'itemType' => 'photo',
            'date' => '2019-07-02 21:18:06.769',
            'parentItemId' => 'this-is-an-album-id',
            'parentItemType' => 'mediaalbum',
            'submitter' => 'this-is-a-user-id',
            'notification' => 'announce',
          ],
        ],
      ],

    ]);
    $this->assertEquals($level3, []);
  }

  public function testNewCommentOnOldPhotoInOldAlbum() {
    // Announced new photos in old albums are level 2
    // Announced new comments on old photos are in level 3
    list($level1, $level2, $level3) = $this->_getLevels([]);

    $this->assertEquals($level1, []);
    $this->assertEquals($level2, []);
    $this->assertEquals($level3, []);
  }

  public function _testLevel1()
  {
    $items = [
      // Announced parent item => level 1
      [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
      // Silent parent item => hidden
      [
        'id' => 2,
        'itemId' => 'cb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 18:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'b0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'silent',
      ],
      // Silent parent item => hidden
      [
        'id' => 3,
        'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'image',
        'date' => '2019-07-05 19:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'silent',
      ],
      // Announced child item with no parent listed in level 1 => level 2
      [
        'id' => 4,
        'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'photo',
        'date' => '2019-07-05 19:18:06.769',
        'parentItemId' => 'a2655b91-6c4f-42f8-a1d6-403bf206175d',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],

      // Announced parent item => level 1
      [
        'id' => 5,
        'itemId' => '639cecdd-b91c-4b29-9b98-d41706e6a41d',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-01 20:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
      // Announced child item with parent => hidden
      [
        'id' => 6,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-02 21:18:06.769',
        'parentItemId' => '639cecdd-b91c-4b29-9b98-d41706e6a41d',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],


      // Announced child item with no parent listed in level 1 => level 2
      [
        'id' => 7,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-01 21:18:06.769',
        'parentItemId' => '624bc96b-a704-4e23-8bcb-f36b13acf049',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
      // Announced child item with parent listed in level 2 => level 3
      [
        'id' => 7,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-01 21:18:06.769',
        'parentItemId' => '624bc96b-a704-4e23-8bcb-f36b13acf049',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
      ],
    ];

    $feed = new Api_Feed();
    list($levels) = $feed->buildLevels($items);
    list($level1, $level2) = $levels;

    $this->assertEquals($level1, [
      'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
        'children' => [],
      ],
      '639cecdd-b91c-4b29-9b98-d41706e6a41d' => [
        'id' => 5,
        'itemId' => '639cecdd-b91c-4b29-9b98-d41706e6a41d',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-01 20:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'announce',
        'children' => [],
      ],
    ]);

    $this->assertEquals($level2, [
      'a2655b91-6c4f-42f8-a1d6-403bf206175d' => [
          'itemId' => 'a2655b91-6c4f-42f8-a1d6-403bf206175d',
          'itemType' => 'mediaalbum',
          'date' => '2019-07-05 19:18:06.769',
          'children' => [
            [
              'id' => 4,
              'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
              'itemType' => 'photo',
              'date' => '2019-07-05 19:18:06.769',
              'parentItemId' => 'a2655b91-6c4f-42f8-a1d6-403bf206175d',
              'parentItemType' => 'mediaalbum',
              'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
              'notification' => 'announce',
            ],
          ],
      ],
    ]);
  }
}