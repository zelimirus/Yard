<?php

/**
 * Medias Model class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 *
 * @version  january 2015
 */
class Default_Model_Medias extends My_Db_Table
{

    protected $_name = 'medias';
    protected $_primary = 'id';

    public function getByLibraryId($lib_id)
    {
        $select = $this->select()
            ->from($this->_name)
            ->setIntegrityCheck(false)
            ->joinLeft("media_libraries_medias as mlm", "medias.id = mlm.media_id", "media_library_id")
            ->joinLeft("media_libraries as ml", "ml.id = mlm.media_library_id", "path")
            ->where('ml.id = ?', (int) $lib_id)
            ->order('medias.id asc');
        
        return $this->fetchAll($select);
    }

    public function getById($id)
    {
        $select = $this->select()
            ->from($this->_name)
            ->setIntegrityCheck(false)
            ->joinLeft("media_libraries_medias as mlm", "medias.id = mlm.media_id", "media_library_id")
            ->joinLeft("media_libraries as ml", "ml.id = mlm.media_library_id", "path")
            ->where('medias.id = ?', (int) $id);
        
        return $this->fetchRow($select);
    }
}
