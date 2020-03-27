<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class FeedTest extends TestCase
{
  public function testLevel1()
  {
    $items = [
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
        'itemId' => 'cb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 18:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'submitter' => 'b0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'silent',
      ],
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
      [
        'id' => 4,
        'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'photo',
        'date' => '2019-07-05 19:18:06.769',
        'parentItemId' => 'a2655b91-6c4f-42f8-a1d6-403bf206175d',
        'parentItemType' => 'mediaalbum',
        'submitter' => 'c0bfb8a7-5754-4186-acd2-44b20ef32399',
        'notification' => 'silent',
      ],
    ];

    $feed = new Api_Feed();
    list($levels) = $feed->buildLevels($items);
    list($level1) = $levels;

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
  }
}