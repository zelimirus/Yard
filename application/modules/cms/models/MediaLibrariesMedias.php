<?php

/**
 * junction table media_libraries_medias model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 *
 * @version  august 2013
 */
class Cms_Model_MediaLibrariesMedias extends My_Db_Table
{

    protected $_name = 'media_libraries_medias';
    protected $_primary = 'id';

    public function doSave($data, $id = null)
    {
        if ($id) {
            return $this->update($data, array('id = ?' => (int) $id));
        } else {
            return $this->insert($data);
        }
    }

    public function saveByMediaLibraryId($media_id, $media_library_id)
    {
        $this->doSave(array('media_id' => $media_id, 'media_library_id' => $media_library_id));
    }

    public function doDeleteByMediaLibraryId($media_library_id)
    {
        return $this->delete(array('media_library_id = ?' => (int) $media_library_id)) > 0;
    }
}
