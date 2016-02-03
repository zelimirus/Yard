<?php


class Cms_Model_MediaLibraries extends My_Db_Table
{

    protected $_name = 'media_libraries';
    protected $_primary = 'id';

    public function getLastByTitle($name)
    {
        $select = $this->select()->where('name = ?', $name)->order('id desc')->limit(1);
        return $this->fetchRow($select);
    }

    
    public function getAll()
    {
        $select = $this->select();
        return $this->fetchAll($select);
    }

    
    public function getById($id)
    {
        $select = $this->select()->where('id = ?', (int) $id);
        return $this->fetchRow($select);
    }

    public function getByPath($path)
    {
        $select = $this->select()->where('path = ?', $path);
        return $this->fetchRow($select);
    }

    
    public function getSelectForPagination($where = array(), $order = 'id asc', $limit = null)
    {
        $select = $this->select();
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

    public function getArrayForDropdown()
    {
        $select = $this->select()->from($this->_name, array('id', 'name'));
        return $this->fetchAll($select)->toArray();
        ;
    }

    public function getMediasLibrariesByArticleId($article_id)
    {
        $select = $this->select()
                        ->from(array('ml' => 'media_libraries'), array('id', 'name'))
                        ->setIntegrityCheck(false)
                        ->joinLeft("articles_media_libraries as aml", "ml.id = aml.media_library_id", '')
                        ->where("aml.article_id = ?", $article_id);

        $result = $this->fetchAll($select);
        if ($result) {
            return $result->toArray();
        } else {
            return false;
        }
    }
}
