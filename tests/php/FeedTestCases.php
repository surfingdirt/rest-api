<?php
class FeedTestCases
{
  const MERGE = [
    'New album' => [
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
    ],
    'New photo in new album' => [
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
        [
          'id' => 2,
          'itemId' => 'cb918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'mediaalbum',
          'date' => '2019-07-05 18:18:06.769',
          'parentItemId' => null,
          'parentItemType' => null,
          'submitter' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
          'notification' => 'silent',
        ],
        [
          'id' => 3,
          'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'photo',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
          'parentItemType' => 'mediaalbum',
          'submitter' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
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
    ],
    'New photo in old announced album' => [
      'items' => [
        [
          'id' => 1,
          'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'photo',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'album-id',
          'parentItemType' => 'mediaalbum',
          'submitter' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [],
      'newSubItems' => [
        'album-id' => [
          'itemId' => 'album-id',
          'itemType' => 'mediaalbum',
          'children' => [
            [
              'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
              'itemType' => 'photo',
              'date' => '2019-07-05 19:18:06.769',
              'submitter' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
            ]
          ],
        ]
      ],
    ],
    'New photo in old silent album' => [
      'items' => [
        [
          'id' => 1,
          'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'photo',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'album-id',
          'parentItemType' => 'mediaalbum',
          'submitter' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
          'notification' => 'silent',
        ],
      ],
      'newItems' => [],
      'newSubItems' => [],
    ],
    'New comment on new photo on new album' => [
      'items' => [
        [
          'id' => 1,
          'itemId' => 'new-album-id',
          'itemType' => 'mediaalbum',
          'date' => '2019-07-05 17:18:06.769',
          'parentItemId' => null,
          'parentItemType' => null,
          'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
          'notification' => 'announce',
        ],
        [
          'id' => 2,
          'itemId' => 'new-photo-id',
          'itemType' => 'photo',
          'date' => '2019-07-05 17:18:06.769',
          'parentItemId' => 'new-album-id',
          'parentItemType' => 'mediaalbum',
          'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
          'notification' => 'announce',
        ],
        [
          'id' => 3,
          'itemId' => 'new-comment-id',
          'itemType' => 'comment',
          'date' => '2019-07-05 17:18:06.769',
          'parentItemId' => 'new-photo-id',
          'parentItemType' => 'photo',
          'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [
        'new-album-id' => [
          'itemId' => 'new-album-id',
          'itemType' => 'mediaalbum',
          'date' => '2019-07-05 17:18:06.769',
          'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
          'children' => [],
        ],
      ],
      'newSubItems' => [
      ],
    ],
    'New comment on new photo on old album' => [
      'items' => [
        [
          'id' => 1,
          'itemId' => 'new-photo-id',
          'itemType' => 'photo',
          'date' => '2019-07-05 17:18:06.769',
          'parentItemId' => 'old-album-id',
          'parentItemType' => 'mediaalbum',
          'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
          'notification' => 'announce',
        ],
        [
          'id' => 2,
          'itemId' => 'new-comment-id',
          'itemType' => 'comment',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'new-photo-id',
          'parentItemType' => 'photo',
          'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [],
      'newSubItems' => [
        'old-album-id' => [
          'itemId' => 'old-album-id',
          'itemType' => 'mediaalbum',
          'children' => [
            [
              'itemId' => 'new-photo-id',
              'itemType' => 'photo',
              'date' => '2019-07-05 17:18:06.769',
              'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
            ]
          ],
        ],
      ],
    ],
    'New comment on old photo on old album' => [
      'items' => [
        [
          'id' => 1,
          'itemId' => 'new-comment-id',
          'itemType' => 'comment',
          'date' => '2019-07-05 17:18:06.769',
          'parentItemId' => 'new-photo-id',
          'parentItemType' => 'photo',
          'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [],
      'newSubItems' => [
        'new-photo-id' => [
          'itemId' => 'new-photo-id',
          'itemType' => 'photo',
          'children' => [
            [
              'itemId' => 'new-comment-id',
              'itemType' => 'comment',
              'date' => '2019-07-05 17:18:06.769',
              'submitter' => 'a0bfb8a7-5754-4186-acd2-44b20ef32399',
            ]
          ],
        ],
      ],
    ],
  ];
}