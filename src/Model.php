<?php

namespace Ions\Mvc;

/**
 * Class Model
 * @package Ions\Mvc
 */
abstract class Model extends ServiceManager
{
    /**
     * @return mixed
     */
    public function getDatabases()
    {
        return $this->db->query('SHOW DATABASES')->rows;
    }

    /**
     * @return mixed
     */
    public function getTables()
    {
        return $this->db->query("SHOW TABLES")->rows;
    }

    /**
     * @param $table
     * @return mixed
     */
    public function getDescribeColumns($table)
    {
        return $this->db->query("DESCRIBE `$table`")->rows;
    }

    /**
     * @param $table
     * @return mixed
     */
    public function getColumns($table)
    {
        return $this->db->query("SHOW COLUMNS FROM `$table`")->rows;
    }

    /**
     * @param $table
     * @param $column
     * @return mixed
     */
    public function getColumn($table, $column)
    {
        return $this->db->query("SHOW COLUMNS FROM `$table` LIKE " . $this->db->escape($column))->rows;
    }

    /**
     * @param $profiling
     */
    public function setProfiling($profiling)
    {
        $this->db->query('SET profiling = ' . (int)$profiling);
    }

    /**
     * @return mixed
     */
    public function getProfiles()
    {
        return $this->db->query('SHOW PROFILES')->rows;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->db->query('SHOW PROFILE')->rows;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getQueryProfile($id)
    {
        return $this->db->query('SHOW PROFILE FOR QUERY ' . (int)$id)->rows;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getQueryCpuProfile($id)
    {
        return $this->db->query('SHOW PROFILE CPU FOR QUERY ' . (int)$id)->rows;
    }
}
