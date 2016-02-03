<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Medias
 *
 * @author nikola
 */
class Cms_Model_Medias extends My_Db_Table
{

    protected $_name = 'medias';
    protected $_primary = 'id';


    public function getLastByTitle($title)
    {
        $select = $this->select()->where('title = ?', $title)->order('id desc')->limit(1);
        return $this->fetchRow($select);
    }

    public function doDeleteByIdsArray($ids)
    {
        if (empty($ids)) {
            return false;
        }
        $where = $this->getAdapter()->quoteInto('id IN (?)', $ids);
        return $this->delete($where) > 0;
    }

    public function getIdsArrayByMediaLibId($media_lib_id)
    {
        $select = $this->select()->from($this->_name, 'id');
        $select->setIntegrityCheck(false)
                ->joinLeft("media_libraries_medias as mlm", "medias.id = mlm.media_id", "")
                ->where('mlm.media_library_id = ?', (int) $media_lib_id);

        return is_object($this->fetchAll($select)) ? $this->fetchAll($select)->toArray() : array();
    }



    public function getAll()
    {
        $select = $this->select();
        return $this->fetchAll($select);
    }

    public function getById($id)
    {
        $select = $this->select()->from($this->_name);
        $select->setIntegrityCheck(false);
        $select->joinLeft("media_libraries_medias as mlm", "medias.id = mlm.media_id", "media_library_id");
        $select->joinLeft("media_libraries as ml", "ml.id = mlm.media_library_id", "path");
        $select->where('medias.id = ?', (int) $id);
        return $this->fetchRow($select);
    }

    public function getByUrl($url)
    {
        $select = $this->select()->from($this->_name);
        $select->where('url = ?', $url);
        return $this->fetchRow($select);
    }

    public function getSelectForPagination($where = array(), $order = 'id asc', $media_library_id, $limit = null)
    {
        $select = $this->select()->from($this->_name);
        $select->setIntegrityCheck(false);
        $select->joinLeft("media_libraries_medias as mlm", "medias.id = mlm.media_id", "")
                ->joinLeft("media_libraries as ml", "ml.id = mlm.media_library_id", "path")
                ->where("mlm.media_library_id = ?", $media_library_id);

        if (count($where) > 0) {
            foreach ($where as $on_where => $on_value) {
                if ($on_value) {
                    if (is_array($on_value)) {
                        $or_where = '';
                        foreach ($on_value as $count => $v) {
                            $or_where .= $this->_db->quoteInto('text(' . $on_where . ') ILIKE ?', '%' . $v . '%') . ' OR ';
                        }
                        $select->where(substr($or_where, 0, -3));
                    } else {
                        $select->where('text(' . $on_where . ') ILIKE ?', '%' . $on_value . '%');
                    }
                }
            }
        }

        if (!empty($order)) {
            $select->order($order);
        }
        if (!empty($limit)) {
            $select->limit($limit);
        }

        return $select;
    }

    public function doSave($data, $id = null)
    {
        if ($id) {
            return $this->update($data, array('id = ?' => (int) $id));
        } else {
            return $this->insert($data);
        }
    }

    public function doDelete($id)
    {
        return $this->delete(array('id = ?' => (int) $id)) > 0;
    }

    public function getLastId()
    {
        $select = $this->select()->from($this->_name, 'id')->order('id desc')->limit(1);
        return $this->fetchRow($select)->id;
    }
}
