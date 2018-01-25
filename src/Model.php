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
     * @param $database
     * @return mixed
     */
    public function getTables($database)
    {
        return $this->db->query("SHOW TABLES FROM `{$this->db->escape($database)}`")->rows;
    }

    /**
     * @param $database
     * @param $table
     * @return mixed
     */
    public function getDescribeColumns($database, $table)
    {
        return $this->db->query("DESCRIBE `{$this->db->escape($database)}`.`{$this->db->escape($table)}`")->rows;
    }

    /**
     * @param $database
     * @param $table
     * @return mixed
     */
    public function getColumns($database, $table)
    {
        return $this->db->query("SHOW COLUMNS FROM `{$this->db->escape($database)}`.`{$this->db->escape($table)}`")->rows;
    }

    /**
     * @param $database
     * @param $table
     * @param $column
     * @return mixed
     */
    public function getColumn($database, $table, $column)
    {
        return $this->db->query("SHOW COLUMNS FROM `{$this->db->escape($database)}`.`{$this->db->escape($table)}` LIKE '{$this->db->escape($column)}'")->rows;
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
