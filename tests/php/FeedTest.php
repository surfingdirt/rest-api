<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once('FeedTestCases.php');

final class FeedTest extends TestCase
{
  protected function _getLevels($items) {
    $feed = new Api_Feed();
    $feed->buildLevels($items);
    return $feed->getLevels();
  }

  protected function _getNewItemsAndSubItems($items) {
    $feed = new Api_Feed();
    $feed->buildLevels($items);
    $feed->mergeLevels();
    $newItems = $feed->getNewItems();
    $newSubItems = $feed->getNewSubItems();
    return [$newItems, $newSubItems];
  }

  protected function _getSortedItems($items) {
    $feed = new Api_Feed();
    $feed->buildLevels($items);
    $feed->mergeLevels();
    $result = $feed->getSortedItems();
    return $result;
  }

  /**
   * Levels
   */
  public function testLevelsEmpty() {
    list($level1, $level2, $level3) = $this->_getLevels([]);

    $this->assertEquals([], $level1);
    $this->assertEquals([], $level2);
    $this->assertEquals([], $level3);
  }

  public function testLevelsNewSilentAlbum() {
    // Silent items are hidden
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'parentItemDate' => null,
        'notification' => 'silent',
      ],
    ]);

    $this->assertEquals([], $level1);
    $this->assertEquals([], $level2);
    $this->assertEquals([], $level3);
  }

  public function testLevelsNewAnnouncedAlbum() {
    list($level1, $level2, $level3) = $this->_getLevels([
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
    ]);

    $this->assertEquals($level1, [
      'bb918b70-e541-42b0-a5fe-e32eb4748021' => [
        'id' => 1,
        'itemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'itemType' => 'mediaalbum',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => null,
        'parentItemType' => null,
        'parentItemDate' => null,
        'notification' => 'announce',
        'children' => [],
      ],
    ]);
    $this->assertEquals([], $level2);
    $this->assertEquals([], $level3);
  }

  public function testLevelsNewSilentPhoto() {
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-02 21:18:06.769',
        'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'parentItemType' => 'mediaalbum',
        'parentItemDate' => '2019-07-01 21:18:06.769',
        'notification' => 'silent',
      ],
    ]);

    $this->assertEquals([], $level1);
    $this->assertEquals([], $level2);
    $this->assertEquals([], $level3);
  }

  public function testLevelsNewAnnouncedPhoto() {
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'parentItemType' => 'mediaalbum',
        'parentItemDate' => '2019-07-01 21:18:06.769',
        'notification' => 'announce',
      ],
    ]);

    $this->assertEquals([], $level1);
    $this->assertEquals([
      'e9c060b6-8d72-4187-8441-bf89c412e4d6' => [
        'id' => 1,
        'itemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'itemType' => 'photo',
        'date' => '2019-07-05 17:18:06.769',
        'parentItemId' => 'bb918b70-e541-42b0-a5fe-e32eb4748021',
        'parentItemType' => 'mediaalbum',
        'parentItemDate' => '2019-07-01 21:18:06.769',
        'notification' => 'announce',
        'children' => [],
      ],
    ], $level2);
    $this->assertEquals([], $level3);
  }

  public function testLevelsNewSilentComment() {
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => '2a2f012f-4476-41a9-a37d-931749371228',
        'itemType' => 'comment',
        'date' => '2019-07-02 22:18:06.769',
        'parentItemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'parentItemType' => 'photo',
        'parentItemDate' => '2019-07-01 21:18:06.769',
        'notification' => 'silent',
      ],
    ]);
    $this->assertEquals([], $level1);
    $this->assertEquals([], $level2);
    $this->assertEquals([], $level3);
  }

  public function testLevelsNewAnnouncedComment() {
    list($level1, $level2, $level3) = $this->_getLevels([
      [
        'id' => 1,
        'itemId' => '2a2f012f-4476-41a9-a37d-931749371228',
        'itemType' => 'comment',
        'date' => '2019-07-02 22:18:06.769',
        'parentItemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'parentItemType' => 'photo',
        'parentItemDate' => '2019-07-01 21:18:06.769',
        'notification' => 'announce',
      ],
    ]);

    $this->assertEquals([], $level1);
    $this->assertEquals([], $level2);
    $this->assertEquals([
      '2a2f012f-4476-41a9-a37d-931749371228' => [
        'id' => 1,
        'itemId' => '2a2f012f-4476-41a9-a37d-931749371228',
        'itemType' => 'comment',
        'date' => '2019-07-02 22:18:06.769',
        'parentItemId' => 'e9c060b6-8d72-4187-8441-bf89c412e4d6',
        'parentItemType' => 'photo',
        'parentItemDate' => '2019-07-01 21:18:06.769',
        'notification' => 'announce',
      ],
    ], $level3);
  }

  /**
   * @dataProvider mergeProvider
   */
  public function testMerge($items, $expectedNewItems, $expectedNewSubItems)
  {
    list($newItems, $newSubItems) = $this->_getNewItemsAndSubItems($items);

    $this->assertEquals($expectedNewItems, $newItems);
    $this->assertEquals($expectedNewSubItems, $newSubItems);
  }

  public function mergeProvider() {
    return FeedTestCases::MERGE;
  }

  /**
   * @dataProvider sortProvider
   */
  public function testSort($items, $expectedSortedItems)
  {
    $sortedItems = $this->_getSortedItems($items);
    $this->assertEquals($expectedSortedItems, $sortedItems);
  }

  public function sortProvider() {
    return FeedTestCases::SORT;
  }
}