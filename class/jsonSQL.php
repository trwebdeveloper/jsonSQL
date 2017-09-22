<?php

class jsonSQL
{
    private $database;
    private $value;
    private $table;
    private $where;
    private $filter;
    private $sort;
    private $result;
    private $insertTable;
    private $insertCategory;
    public $lastId;

    function db()
    {
        $this->database = fopen(DATABASE, 'r+b') or exit(ERR_CANT_OPEN);
    }

    function read()
    {
        $this->db();
        @$file = fread($this->database, filesize(DATABASE));
        fclose($this->database);
        return json_decode($file, true);
    }

    function addId($data)
    {
        $id = 1;
        foreach ($data as $key => $value){
            $data[$key]['id'] = $id++;
        }
        return $data;
    }

    function downImage($data){
        foreach ($data[$this->insertTable] as $key => $value){
            if(strpos($value['image'], 'ttp://')){
                $expImg = explode('.', $value['image']);
                $imagePath = 'json/images/'.permalink($value['title']).'-'.$key.'.'.end($expImg);
                multiSave(array($value['image']), array($imagePath));
                $data[$this->insertTable][$key]['image'] = $imagePath;
            }
        }
        return $data;
    }

    function add($data, $image = false)
    {
        $this->db();
        if (is_writable(DATABASE)) {
            $data = json_encode($data);
            if ($data === false || is_null($data)) {
                exit(ERR_JSON_ENCODE);
            }
            $status = fwrite($this->database, $data);
        } else {
            echo ERR_NOT_WRITABLE;
        }
    }

    function select($table = false, $callback = false)
    {
        if (is_callable($callback)) {
            $callback($this->read());
        } else {
            $this->value = $this->read();
            if (isset($this->value[$table])) {
                $this->result = $this->table = $this->addId($this->value[$table]);
            }
        }
        return $this;
    }

    function array_sort($array, $on, $order = SORT_DESC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    function sort($type, $sort = false)
    {
        $this->result = $this->limit = $this->table = $this->value = $this->array_sort($this->table, $type, $sort);
        return $this;
    }

    function filter($type, $keyValue)
    {
        $arr = [];
        foreach ($this->table as $key => $value) {
            if ($value[$type] == $keyValue) {
                $arr[$key] = $value;
            }
        }
        $this->result = $this->limit = $this->table = $this->value = $arr;
        return $this;
    }

    function limit($offset = 0, $limit = 0)
    {
        $i = 0;
        $l = $offset + $limit;
        $arr = [];
        foreach ($this->limit as $key => $value) {
            $i++;
            if ($i > $offset) {
                $arr[$key] = $value;
            }
            if ($limit != 0 && $i == $l) {
                break;
            }
        }
        $this->result = $this->limit = $this->table = $this->value = $arr;
        return $this;
    }

    function insert($table)
    {
        $this->insertTable = $table;
        return $this;
    }

    function category($table)
    {
        $this->insertCategory = $table;
        return $this;
    }

    function set($id, $data)
    {
        $this->value[$this->insertTable][$id] = $data;
        return $this;
    }

    function id($id)
    {
        if (isset($this->table[$id])) {
            $this->result = $this->table[$id];
        }

        return $this;
    }

    function getLastId()
    {
        return $this->lastId;
    }

    function getTable()
    {
        return $this->table;
    }

    function getValue()
    {
        return $this->value;
    }

    function result()
    {
        return $this->result;
    }
}
