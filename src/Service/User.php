<?php

namespace Ions\Mvc\Service;

use Ions\Mvc\ServiceManager;

class User extends ServiceManager
{
    private $id;
    private $username;
    private $firstname;
    private $lastname;
    private $group_id;
    private $email;
    private $permission = [];

    public function __construct()
    {
        if ($this->session->has('user_id')) {
            $user_query = $this->db->query("SELECT * FROM `user` WHERE id = '" . (int)$this->session->get('user_id') . "' AND status = '1'");

            if ($user_query->count) {
                $this->id = $user_query->row['id'];
                $this->username = $user_query->row['username'];
                $this->firstname = $user_query->row['firstname'];
                $this->lastname = $user_query->row['lastname'];
                $this->group_id = $user_query->row['group_id'];
                $this->email = $user_query->row['email'];

                $this->db->execute('UPDATE `user` SET ip = ' . $this->db->escape($this->request->getServer('REMOTE_ADDR')) . " WHERE id = '" . (int)$this->id . "'");

                $ip_query = $this->db->query("SELECT * FROM user_ip WHERE user_id = '" . (int)$this->session->get('user_id') . "' AND ip = " . $this->db->escape($this->request->getServer('REMOTE_ADDR')));

                if (!$ip_query->count) {
                    $this->db->execute("INSERT INTO user_ip SET user_id = '" . (int)$this->session->get('user_id') . "', ip = " . $this->db->escape($this->request->getServer('REMOTE_ADDR')) . ', date_added = NOW()');
                }

                $group_query = $this->db->query("SELECT `permission` FROM `user_group` WHERE id = '" . (int)$this->group_id . "'");

                $permissions = json_decode($group_query->row['permission'], true);

                if (is_array($permissions)) {
                    foreach ($permissions as $key => $value) {
                        $this->permission[$key] = $value;
                    }
                }

            } else {
                $this->logout();
            }
        }
    }

    public function login($username, $email, $password, $override = false)
    {
        if ($username) {
            $query = $this->db->query('SELECT * FROM `user` WHERE `username` = ' . $this->db->escape($username) . " AND status = '1'");

            if(!password_verify($password, $query->row['password'])) {
                return false;
            }
        }

        if ($email) {
            if ($override) {
                $query = $this->db->query('SELECT * FROM `user` WHERE LOWER(email) = ' . $this->db->escape(strtolower($email)) . " AND status = '1'");
            } else {
                $query = $this->db->query('SELECT * FROM `user` WHERE LOWER(email) = ' . $this->db->escape(strtolower($email)) . ' AND password = ' . $this->db->escape(password_hash($password, PASSWORD_DEFAULT)) . " AND status = '1'");
            }
        }

        if (isset($query) && $query->count) {
            $this->session->set('user_id', $query->row['id']);

            $this->id = $query->row['id'];
            $this->username = $query->row['username'];
            $this->firstname = $query->row['firstname'];
            $this->lastname = $query->row['lastname'];
            $this->group_id = $query->row['group_id'];
            $this->email = $query->row['email'];

            $this->db->execute('UPDATE `user` SET ip = ' . $this->db->escape($this->request->getServer('REMOTE_ADDR')) . " WHERE id = '" . (int)$this->id . "'");

            $group_query = $this->db->query("SELECT `permission` FROM `user_group` WHERE `id` = '" . (int)$query->row['group_id'] . "'");

            $permissions = json_decode($group_query->row['permission'], true);

            if (is_array($permissions)) {
                foreach ($permissions as $key => $value) {
                    $this->permission[$key] = $value;
                }
            }

            return true;
        }

        return false;
    }

    public function hasPermission($key, $value)
    {
        if (isset($this->permission[$key])) {
            return in_array($value, $this->permission[$key], true);
        }

        return false;
    }
    public function logout()
    {
        $this->session->delete('user_id');

        $this->id = '';
        $this->username = '';
        $this->firstname = '';
        $this->lastname = '';
        $this->group_id = '';
        $this->email = '';
    }

    public function isLogged()
    {
        return $this->id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function getLastName()
    {
        return $this->lastname;
    }

    public function getGroupId()
    {
        return $this->group_id;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
