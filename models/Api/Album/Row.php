<?php
class Api_Album_Row extends Media_Album_Row
{
	public $page;
	    
	public function __construct(array $config = array(), $page = 1)
	{
		parent::__construct($config);
		$this->page = $page;
	}	
	
	/**
	 * This array contains the list of attributes that
	 * should not be sent to a client.
	 * @var array
	 */
	protected $_hiddenFromClientAttr = array(

	);

	/**
	 * This method returns the data that is 'public',
	 * that is to say, visible to the client.
	 */
	public function getDataForClient()
	{
		$ret = array();
		foreach($this->_data as $name => $value){
			if(in_array($name, $this->_hiddenFromClientAttr)){
				continue;
			}

			$ret[$name] = $value;
		}
		return $ret;
	}

	public function isDeletableBy(User_Row $user, Lib_Acl $acl)
	{
		if($this->albumType != Media_Album::TYPE_SIMPLE){
			return false;
		}
		
		$itemSet = $this->getItemSet();
		if(!$itemSet || count($itemSet) > 0){
			return false;
		}
		
		if($this->submitter == $user->getId()){
			return true;
		}
		
		if($user->isAdmin() || $user->isEditor()){
			return true;
		}
		
		return false;
	}	
	
	protected function _getItems()
	{
		if($this->albumType == Media_Album::TYPE_SIMPLE){
			return $this->_getSimpleItems();
		} else {
			return $this->_getAggregateItems();
		}
	}

    public function clearCache()
    {
    	if($this->albumType == Media_Album::TYPE_SIMPLE) {
    		parent::clearCache();
    	}
    	
    	$cache = $this->getCache();
    	$cacheIds = array(
    		$this->_getCommentsCacheId(),
    		$this->getItemsCacheId(),
    		$this->_getTranslatedTextsCacheId(),
    	);

    	foreach($cacheIds as $cacheId){
    		$cache->remove($cacheId);
    	}
    }	
	
	protected function _getSimpleItems()
	{
		$cache = $this->getCache();
		$cacheId = $this->getItemsCacheId();

		if(ALLOW_CACHE || !($albumItems = $cache->load($cacheId))){

			$db = Globals::getMainDatabase();

			$sql = "
				SELECT m.id, m.mediaType
				FROM ".Constants_TableNames::MEDIA." m
				WHERE m.albumId = $this->id
				ORDER BY m.id DESC
			";
			$albumItems = $db->query($sql)->fetchAll();
			if(ALLOW_CACHE){
				$this->getTable()->saveDataInCache($cache, $albumItems, $cacheId);
			}
		}

		return $albumItems;
	}

	protected function _getAggregateItems()
	{
		$cache = $this->getCache();
		$cacheId = $this->getItemsCacheId();

		if(!$albumItems = $cache->load($cacheId)){

			$aggregationTable = new Media_Album_Aggregation();
		    $where = $aggregationTable->getAdapter()->quoteInto('albumId = ?', $this->id);
			$aggregationRow = $aggregationTable->fetchRow($where);
			if(empty($aggregationRow)){
				return;
			}

			if($aggregationRow->keyName == Media_Album_Aggregation::KEYNAME_USER){
				$rawItems = $this->_getUserAggregationItems($aggregationRow->keyValue);
				$parentTable = new User();
			} else {
				$parentType = Data::mapDataType($aggregationRow->keyName);
				$parentTable = new $parentType();
				$rawItems = $this->_getSimpleAggregationItems($aggregationRow);
			}
			if(empty($rawItems)){
				return;
			}

			$this->_parentItem = $parentTable->find($aggregationRow->keyValue)->current();

			$albumItems = array();
			foreach($rawItems as $rawAlbumItem){
				$albumItems[] = Media_Item_Factory::buildItem($rawAlbumItem->id, $rawAlbumItem->mediaType);
			}

			$this->getTable()->saveDataInCache($cache, $albumItems, $cacheId);
		}

		return $albumItems;
	}
		
	/**
	 * Aggregation via the intermediate media_users_items table
	 * Here we return a list of media items that the given user
	 * is tagged in.
	 * This information is held in a separate table, in order to
	 * be able to tag several users on a single media item.
	 *
	 * @param int $userId
	 * @return Zend_Db_Table_Rowset
	 */
	protected function _getUserAggregationItems($userId)
	{
		// Build the list of media where the user $userId appears
		$select = new Zend_Db_Select(Globals::getMainDatabase());
		$select->from(Constants_TableNames::MEDIAITEMUSERS)
			   ->distinct()
			   ->columns('mediaId')
			   ->where('userId = '.$userId);
		$rowset = Globals::getMainDatabase()->query($select);
		if(empty($rowset)){
			return null;
		}

		$mediaIds = array();
		foreach($rowset as $row){
			$mediaIds[] = $row['mediaId'];
		}
		if(empty($mediaIds)){
			return array();
		}

		// Get the rowset of medias with the list we just built
		$itemsTable = new Media_Item();
		$where = 'id IN ('.implode(', ', $mediaIds).')';
		$rawItems = $itemsTable->fetchAll($where);
		return $rawItems;
	}

	/**
	 * Simple aggregation
	 * Here we return a rowset of media items which have
	 * an attribute that matches the aggregation.
	 * This attribute is held in the table itself.
	 *
	 * @param Media_Album_Aggregation_Row $aggregationRow
	 * @param Data_Row $parentTable
	 * @return Zend_Db_Table_Rowset
	 */
	protected function _getSimpleAggregationItems(Media_Album_Aggregation_Row $aggregationRow)
	{
		if(in_array($aggregationRow->keyName, self::$allowedSimpleAggregations)){
			$where = "`{$aggregationRow->keyName}` = {$aggregationRow->keyValue}";
		} else {
			$where = "`id` = {$aggregationRow->keyValue}";
		}

		$itemsTable = new Media_Item();
		$rawItems = $itemsTable->fetchAll($where);
		return $rawItems;
	}	
}