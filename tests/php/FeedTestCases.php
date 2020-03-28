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
          'parentItemDate' => null,
          'notification' => 'announce',
        ],
      ],
      'newItems' => [
        'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
          'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'mediaalbum',
          'sortDate' => '2019-07-05 17:18:06.769',
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
          'parentItemDate' => null,
          'notification' => 'announce',
        ],
        [
          'id' => 2,
          'itemId' => 'cb918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'mediaalbum',
          'date' => '2019-07-05 18:18:06.769',
          'parentItemId' => null,
          'parentItemType' => null,
          'parentItemDate' => null,
          'notification' => 'silent',
        ],
        [
          'id' => 3,
          'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'photo',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
          'parentItemType' => 'mediaalbum',
          'parentItemDate' => '2019-07-01 21:18:06.769',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [
        'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
          'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
          'itemType' => 'mediaalbum',
          'sortDate' => '2019-07-05 17:18:06.769',
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
          'parentItemDate' => '2019-07-01 21:18:06.769',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [],
      'newSubItems' => [
        'album-id' => [
          'itemId' => 'album-id',
          'itemType' => 'mediaalbum',
          'sortDate' => '2019-07-05 19:18:06.769',
          'children' => [
            [
              'itemId' => 'db918b70-e541-42b0-a5fe-e32eb4748021',
              'itemType' => 'photo',
              'date' => '2019-07-05 19:18:06.769',
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
          'parentItemDate' => '2019-07-01 21:18:06.769',
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
          'parentItemDate' => null,
          'notification' => 'announce',
        ],
        [
          'id' => 2,
          'itemId' => 'new-photo-id',
          'itemType' => 'photo',
          'date' => '2019-07-05 18:18:06.769',
          'parentItemId' => 'new-album-id',
          'parentItemType' => 'mediaalbum',
          'parentItemDate' => '2019-07-01 17:18:06.769',
          'notification' => 'announce',
        ],
        [
          'id' => 3,
          'itemId' => 'new-comment-id',
          'itemType' => 'comment',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'new-photo-id',
          'parentItemType' => 'photo',
          'parentItemDate' => '2019-07-01 18:18:06.769',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [
        'new-album-id' => [
          'itemId' => 'new-album-id',
          'itemType' => 'mediaalbum',
          'sortDate' => '2019-07-05 17:18:06.769',
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
          'date' => '2019-07-05 18:18:06.769',
          'parentItemId' => 'old-album-id',
          'parentItemType' => 'mediaalbum',
          'parentItemDate' => '2019-07-01 17:18:06.769',
          'notification' => 'announce',
        ],
        [
          'id' => 2,
          'itemId' => 'new-comment-id',
          'itemType' => 'comment',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'new-photo-id',
          'parentItemType' => 'photo',
          'parentItemDate' => '2019-07-05 18:18:06.769',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [],
      'newSubItems' => [
        'old-album-id' => [
          'itemId' => 'old-album-id',
          'itemType' => 'mediaalbum',
          'sortDate' => '2019-07-05 18:18:06.769',
          'children' => [
            [
              'itemId' => 'new-photo-id',
              'itemType' => 'photo',
              'date' => '2019-07-05 18:18:06.769',
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
          'parentItemDate' => '2019-07-01 17:18:06.769',
          'notification' => 'announce',
        ],
      ],
      'newItems' => [],
      'newSubItems' => [
        'new-photo-id' => [
          'itemId' => 'new-photo-id',
          'itemType' => 'photo',
          'sortDate' => '2019-07-05 17:18:06.769',
          'children' => [
            [
              'itemId' => 'new-comment-id',
              'itemType' => 'comment',
              'date' => '2019-07-05 17:18:06.769',
            ]
          ],
        ],
      ],
    ],
  ];

  const SORT = [
    'Sort everything' => [
      'items' => [
        [
          'id' => 1,
          'itemId' => 'new-album-id',
          'itemType' => 'mediaalbum',
          'date' => '2019-07-05 17:18:06.769',
          'parentItemId' => null,
          'parentItemType' => null,
          'parentItemDate' => null,
          'notification' => 'announce',
        ],
        [
          'id' => 2,
          'itemId' => 'new-photo-id',
          'itemType' => 'photo',
          'date' => '2019-07-05 18:18:06.769',
          'parentItemId' => 'new-album-id',
          'parentItemType' => 'mediaalbum',
          'parentItemDate' => '2019-07-01 17:18:06.769',
          'notification' => 'announce',
        ],
        [
          'id' => 3,
          'itemId' => 'new-comment-id',
          'itemType' => 'comment',
          'date' => '2019-07-05 19:18:06.769',
          'parentItemId' => 'new-photo-id',
          'parentItemType' => 'photo',
          'parentItemDate' => '2019-07-01 18:18:06.769',
          'notification' => 'announce',
        ],
        [
          'id' => 4,
          'itemId' => 'new-comment2-id',
          'itemType' => 'comment',
          'date' => '2019-07-03 20:18:06.769',
          'parentItemId' => 'old-photo-id',
          'parentItemType' => 'photo',
          'parentItemDate' => '2019-07-01 18:18:06.769',
          'notification' => 'announce',
        ],
      ],
      'result' => [
        [
          'itemType' => 'mediaalbum',
          'itemId' => 'new-album-id',
          'children' => [],
          'sortDate' => '2019-07-05 17:18:06.769',
        ],
        [
          'itemType' => 'photo',
          'itemId' => 'old-photo-id',
          'children' => [
            [
              'itemType' => 'comment',
              'itemId' => 'new-comment2-id',
              'date' => '2019-07-03 20:18:06.769',
            ]
          ],
          'sortDate' => '2019-07-03 20:18:06.769',
        ],
      ],
    ],
  ];
}