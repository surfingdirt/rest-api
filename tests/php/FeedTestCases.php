<?php
class FeedTestCases
{
  const NEW_ALBUM = [
    'items' => [
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
    ],
    'newItems' => [
      'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
        'children' => [],
      ],
    ],
    'newSubItems' => [],
  ];
}