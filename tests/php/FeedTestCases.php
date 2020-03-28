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

  const NEW_PHOTO_IN_NEW_ALBUM = [
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
  ];

  const NEW_PHOTO_IN_OLD_ALBUM = [
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
  ];

  const NEW_COMMENT_ON_NEW_PHOTO_ON_NEW_ALBUM = [
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
  ];

  const NEW_COMMENT_ON_NEW_PHOTO_ON_OLD_ALBUM = [
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
  ];

  const NEW_COMMENT_ON_OLD_PHOTO_ON_OLD_ALBUM = [
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
  ];
}