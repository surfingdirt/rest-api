<?php
class TestController extends Api_Controller_Action
{
  public function init()
  {
    parent::init();

    if (APPLICATION_ENV != 'test' && APPLICATION_ENV != 'development' && $this->_user->status != User::STATUS_ADMIN) {
      throw new Api_Exception_Unauthorised();
    }

    Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->setViewScriptPathSpec('view.phtml');
    $this->getResponse()->setRawHeader('Content-Type: application/json');
  }

  protected function _mapResource($key) {
    // Do nothing!
  }

  public function clearPublicFilesAction()
  {
    return;
    Utils::clearPublicFiles();
    @mkdir(PUBLIC_FILES_DIR);
  }

  public function freezeTimeAction()
  {
    $datetime = $this->_request->getParam('datetime');
    if ($datetime === 'NOW') {
      $datetime = date("Y-m-d H:i:s.v");
    }

    if (!preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9].\d{3}$/", $datetime, $matches)) {
      $this->view->output = array(
        "status" => false,
        "datetime" => null,
        "date" => null,
        "timestamp" => null,
      );
      return;
    }

    $parts = explode(" ", $datetime);
    $date = $parts[0];

    $cache = Globals::getGlobalCache();
    $cache->save($datetime, 'datetime');
    $cache->save($date, 'date');
    $cache->save(strtotime($datetime), 'timestamp');

    Globals::getLogger()->test("Setting time to $datetime");

    $output = array(
      'status' => true,
      'datetime' => $cache->load('datetime'),
      'date' => $cache->load('date'),
      'timestamp' => $cache->load('timestamp'),
    );
    $this->view->output = $output;
  }

  public function getTimeAction()
  {
    $this->view->status = true;
    $this->view->datetime = Utils::date("Y-m-d H:i:s");
    $this->view->date = Utils::date("Y-m-d");
  }

  public function clearCacheAction()
  {
    try {
      Globals::getGlobalCache()->clean();
      $this->view->output = array('status' => 'ok');
    } catch (Exception $e) {
      $this->view->output = array('error' => $e->getMessage());
    }
  }

  public function addOriginalCommentAction()
  {
    $db = Globals::getMainDatabase();
    $log = [];
    $output = [];

    $commentsSql = "SELECT id, content from comments where content IS NOT NULL";
    $comments = $db->query($commentsSql);

    foreach($comments as $comment) {
      $content = json_decode($comment['content'], true);
      $id = $comment['id'];

      $clean = null;
      if (is_string($content)) {
        $clean = [array(
          'locale' => DEFAULT_LOCALE,
          'original' => true,
          'text' => $content,
        )];
      } else if(is_array($content)) {
        if (sizeof($content) === 1) {
          if (isset($content[0]['original']) && $content[0]['original']) {
            $log[] = "Skipping id='$id'";
            continue;
          }
          $originalEntryIndex = 0;
        } else if (sizeof($content) > 1) {
          $originalEntryIndex = null;
          foreach($content as $index => $entry) {
            if (isset($entry['original']) && $entry['original']) {
              $log[] = "Skipping id='$id'";
              continue(2);
            }
          }
          if (is_null($originalEntryIndex)) {
            foreach($content as $index => $entry) {
              if ($entry['locale'] === DEFAULT_LOCALE) {
                $originalEntryIndex = $index;
                break;
              }
            }
          }
          if (is_null($originalEntryIndex)) {
            $log[] = "Could not decide how to handle id='$id'";
            break;
          }
        }
        $clean = [array(
          'locale' => $content[$originalEntryIndex]['locale'],
          'original' => true,
          'text' => $content[$originalEntryIndex]['text'],
        )];
      }

      if (!$clean) {
        $log[] = "Did not come up with a clean version for id='$id'";
      } else {
        $encoded = json_encode($clean);
        $quotedContent = $db->quote($encoded);
        $quotedId = $db->quote($id);
        $update = "UPDATE comments set content=$quotedContent WHERE id=$quotedId";
        $output[] = $update;
        try {
          $db->query($update);
        } catch (Exception $e) {
          $log[] = "Failed to run SQL for id='$id': ".$e->getMessage();
          continue;
        }
        $log[] = "Success for id='$id'";
      }
    }

    $this->view->output = [
      'output' => $output,
      'log' => $log,
    ];
  }

  public function addOriginalUserAction()
  {
    $db = Globals::getMainDatabase();
    $log = [];
    $output = [];

    $usersSql = "SELECT userId, bio from users where bio IS NOT NULL";
    $users = $db->query($usersSql);

    foreach($users as $user) {
      $content = json_decode($user['bio'], true);
      $id = $user['userId'];

      $clean = null;
      if (is_null($content)) {
        $log[] = "Skipping id='$id' because content was decoded to null";
        continue;
      }
      if (is_string($content)) {
        $clean = [array(
          'locale' => DEFAULT_LOCALE,
          'original' => true,
          'text' => $content,
        )];
      } else if(is_array($content)) {
        if (sizeof($content) === 1) {
          if (isset($content[0]['original']) && $content[0]['original']) {
            $log[] = "Skipping id='$id'";
            continue;
          }
          $originalEntryIndex = 0;
        } else if (sizeof($content) > 1) {
          $originalEntryIndex = null;
          foreach($content as $index => $entry) {
            if (isset($entry['original']) && $entry['original']) {
              $log[] = "Skipping userId='$id'";
              continue(2);
            }
          }
          foreach($content as $index => $entry) {
            if ($entry['locale'] === DEFAULT_LOCALE) {
              $originalEntryIndex = $index;
              break;
            }
          }
          if (is_null($originalEntryIndex)) {
            $log[] = "Could not decide how to handle id='$id'";
            break;
          }
        }
        $clean = [array(
          'locale' => $content[$originalEntryIndex]['locale'],
          'original' => true,
          'text' => $content[$originalEntryIndex]['text'],
        )];
      }

      if (!$clean) {
        $log[] = "Did not come up with a clean version for userId='$id'";
      } else {
        $encoded = json_encode($clean);
        $quotedContent = $db->quote($encoded);
        $quotedId = $db->quote($id);
        $update = "UPDATE users set bio=$quotedContent WHERE userId=$quotedId";
        $output[] = $update;
        try {
          $db->query($update);
        } catch (Exception $e) {
          $log[] = "Failed to run SQL for id='$id': ".$e->getMessage();
          continue;
        }
        $log[] = "Success for id='$id'";
      }
    }

    $this->view->output = [
      'output' => $output,
      'log' => $log,
    ];
  }

  public function addOriginalAlbumTitleAction()
  {
    $this->view->output = $this->_addOriginal('media_albums', 'title');
  }

  public function addOriginalAlbumDescriptionAction()
  {
    $this->view->output = $this->_addOriginal('media_albums', 'description');
  }

  public function addOriginalMediaTitleAction()
  {
    $this->view->output = $this->_addOriginal('media_items', 'title');
  }

  public function addOriginalMediaDescriptionAction()
  {
    $this->view->output = $this->_addOriginal('media_items', 'description');
  }

  protected function _addOriginal($table, $column)
  {
    $db = Globals::getMainDatabase();
    $log = [];
    $output = [];

    $itemSql = "SELECT id, $column from $table where $column IS NOT NULL";
    $items = $db->query($itemSql);

    foreach($items as $item) {
      $content = json_decode($item[$column], true);
      $id = $item['id'];

      $clean = null;
      if (is_null($content)) {
        $log[] = "Skipping id='$id' because content was decoded to null";
        continue;
      }
      if (is_string($content)) {
        $clean = [array(
          'locale' => DEFAULT_LOCALE,
          'original' => true,
          'text' => $content,
        )];
      } else if(is_array($content)) {
        if (sizeof($content) === 1) {
          if (isset($content[0]['original']) && $content[0]['original']) {
            $log[] = "Skipping id='$id'";
            continue;
          }
          $originalEntryIndex = 0;
        } else if (sizeof($content) > 1) {
          $originalEntryIndex = null;
          foreach($content as $index => $entry) {
            if (isset($entry['original']) && $entry['original']) {
              $log[] = "Skipping id='$id'";
              continue(2);
            }
          }
          foreach($content as $index => $entry) {
            if ($entry['locale'] === DEFAULT_LOCALE) {
              $originalEntryIndex = $index;
              break;
            }
          }
          if (is_null($originalEntryIndex)) {
            $log[] = "Could not decide how to handle id='$id'";
            break;
          }
        }
        $clean = [array(
          'locale' => $content[$originalEntryIndex]['locale'],
          'original' => true,
          'text' => $content[$originalEntryIndex]['text'],
        )];
      }

      if (!$clean) {
        $log[] = "Did not come up with a clean version for id='$id'";
      } else {
        $encoded = json_encode($clean);
        $quotedContent = $db->quote($encoded);
        $quotedId = $db->quote($id);
        $update = "UPDATE $table set $column=$quotedContent WHERE id=$quotedId";
        $output[] = $update;
        try {
          $db->query($update);
        } catch (Exception $e) {
          $log[] = "Failed to run SQL for id='$id': ".$e->getMessage();
          continue;
        }
        $log[] = "Success for id='$id'";
      }
    }

    return [
      'output' => $output,
      'log' => $log,
    ];
  }

  public function portTranslationsAction()
  {
    Globals::getGlobalCache()->clean();
    $log = [];
    $db = Globals::getMainDatabase();

    // Fetch all translations
    $translationsTable = new Data_TranslatedText();
    $translations = $translationsTable->fetchAll();

    // For each of them, update the corresponding item column
    $commentsTable = new Comment();
    $photosTable = new Media_Item_Photo();
    $videosTable = new Media_Item_Video();

    $langLocaleMap = [
      'en' => 'en-US',
      'fr' => 'fr-FR',
    ];

    foreach ($translations as $translation) {
      $locale = $langLocaleMap[$translation->lang];
      if (!$locale) {
        $log[] = "Could not find locale for item with id '$translation->id' and itemType '$translation->itemType': '$translation->lang'";
        continue;
      }

      switch($translation->itemType) {
        case 'comment':
          try {
            $item = $commentsTable->fetchRow($db->quoteInto('id = ?', $translation->id));
          } catch (Exception $e) {
            $item = null;
          }
          if (!$item) {
            $log[] = "Could not find comment with id '$translation->id'";
            continue;
          }
          try {
            // Comments only have 'content' columns
            // $existingContent = $item->content ? json_decode($item->content, true) : [];
            $existingContent = []; // Temporary to fix array issue in db
            if (!is_array($existingContent)) {
              $existingContent = [];
            }
            $content = array_merge($existingContent, [['locale' => $locale, 'text' => $translation->text]]);
            $item->content = json_encode($content, true);
            $item->save(true);
          } catch (Exception $e) {
            $log[] = "Failed to process comment with id '$translation->id': ".$e->getMessage();
          }
          break;
        case 'mediaalbum':
          try {
            $item = Media_Album_Factory::buildAlbumById($translation->id);
          } catch (Exception $e) {
            $item = null;
          }
          if (!$item) {
            $log[] = "Could not find album with id '$translation->id'";
            continue;
          }
          try {
            $column = $translation->type;
            // $existing = $item->{$column} ? json_decode($item->{$column}, true) : [];
            $existing = []; // Temporary to fix array issue in db
            if (!is_array($existing)) {
              $existing = [];
            }
            $merged = array_merge($existing, [['locale' => $locale, 'text' => $translation->text]]);
            $item->{$column} = json_encode($merged);

            $item->save(true);
          } catch (Exception $e) {
            $log[] = "Failed to process album with id '$translation->id': ".$e->getMessage();
          }
          break;
        case 'photo':
          try {
            $item = $photosTable->fetchRow($db->quoteInto('id = ?', $translation->id));
          } catch (Exception $e) {
            $item = null;
          }
          if (!$item) {
            $log[] = "Could not find photo with id '$translation->id'";
            continue;
          }
          try {
            $column = $translation->type;
            // $existing = $item->{$column} ? json_decode($item->{$column}, true) : [];
            $existing = []; // Temporary to fix array issue in db
            if (!is_array($existing)) {
              $existing = [];
            }
            $merged = array_merge($existing, [['locale' => $locale, 'text' => $translation->text]]);
            $item->{$column} = json_encode($merged);

            $item->save(true);
          } catch (Exception $e) {
            $log[] = "Failed to process photo with id '$translation->id': ".$e->getMessage();
          }
          break;
        case 'video':
          try {
            $item = $videosTable->fetchRow($db->quoteInto('id = ?', $translation->id));
          } catch (Exception $e) {
            $item = null;
          }
          if (!$item) {
            $log[] = "Could not find video with id '$translation->id'";
            continue;
          }
          try {
            $column = $translation->type;
            //$existing = $item->{$column} ? json_decode($item->{$column}, true) : [];
            $existing = []; // Temporary to fix array issue in db
            if (!is_array($existing)) {
              $existing = [];
            }
            $merged = array_merge($existing, [['locale' => $locale, 'text' => $translation->text]]);
            $item->{$column} = json_encode($merged);

            $item->save(true);
          } catch (Exception $e) {
            $log[] = "Failed to process video with id '$translation->id': ".$e->getMesage();
          }
          break;
      }
      $log[] = "Processed $translation->itemType/$translation->id";
    }

    Globals::getGlobalCache()->clean();
    $this->view->output = $log;
  }

  public function portBiosAction()
  {
    Globals::getGlobalCache()->clean();
    $log = [];

    $userTable = new User();
    $users = $userTable->fetchAll();
    $locale = 'en-US';
    foreach ($users as $user) {
      if (!$user->bio) {
        $log[] = "Skipping user '$user->username' - no bio";
        continue;
      }
      $user->bio = json_encode([['locale' => $locale, 'text' => $user->bio]]);
      try {
        $user->save();
      } catch (Exception $e) {
        $log[] = "Failed to save user '$user->username': ".$e->getMesage();
      }
      $log[] = "Processed user '$user->username'";
    }

    Globals::getGlobalCache()->clean();
    $this->view->output = $log;
  }
}