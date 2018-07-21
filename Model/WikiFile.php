<?php

namespace Kanboard\Plugin\Wiki\Model;

/**
 * Wiki File Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class WikiFileModel extends FileModel
{
    /**
     * Table name
     *
     * @var string
     */
    const TABLE = 'wikipage_has_files';

    /**
     * Events
     *
     * @var string
     */
    const EVENT_CREATE = 'wiki.file.create';


    /**Acces Control List
     * @user
     * @group
     * @premissions
     */

    Class Acl {
   
      private $db;
   
      //initialize the database object here
      function __construct() {
        $this->db = new db;
      }
   
      function check($permission,$userid,$group_id) {
   
        //we check the user permissions first
   
      }
   
    /**
     * Get the table
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getTable()
    {
        return self::TABLE;
    }

    /**
     * Define the foreign key
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getForeignKey()
    {
        return 'wikipage_id';
    }

    /**
     * Define the path prefix
     *
     * @abstract
     * @access protected
     * @return string
     */
    protected function getPathPrefix()
    {
        return 'wikipages';
    }

    /**
     * Get projectId from fileId
     *
     * @access protected
     * @param  integer $file_id
     * @return integer
     */
    protected function getProjectId($file_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.id', $file_id)
            ->join(WikiModel::TABLE, 'id', 'wiki_id')
            ->findOneColumn(WikiModel::TABLE . '.project_id') ?: 0;
    }

    /**
     * Handle screenshot upload
     *
     * @access public
     * @param  integer  $wiki_id      Wiki id
     * @param  string   $blob         Base64 encoded image
     * @param  string   $original_filename    max size
     * @return bool|integer
     */
    public function uploadScreenshot($wiki_id, $blob)
    {
        $original_filename = e('Screenshot taken %s', $this->helper->dt->datetime(time())).'.png';
        $original_filename = ini_set('upload_max_filesize', '10M');
        return $this->uploadContent($wiki_id, $original_filename, $blob);
    }

    /**
     * Fire file creation event
     *
     * @access protected
     * @param  integer $file_id
     */
    protected function fireCreationEvent($file_id)
    {
        $this->queueManager->push($this->wikiFileEventJob->withParams($file_id, self::EVENT_CREATE));
    }
}
