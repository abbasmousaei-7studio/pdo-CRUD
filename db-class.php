<?php

class DB
{
    // Database host name
    private $dbHost = 'localhost';
    // Databse Username
    private $dbUsername = 'root';
    // Database Password
    private $dbPassword = '';
    // Database name
    private $dbName = 'ppa';

    public function __construct()
    {
        if (!isset($this->db)) {
            // Connect to the database
            try {
                $conn = new PDO('mysql:host='.$this->dbHost.';dbname='.$this->dbName, $this->dbUsername, $this->dbPassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn->exec('set names utf8');
                $this->db = $conn;
            } catch (PDOException $e) {
                exit('Failed to connect with MySQL: '.$e->getMessage());
            }
        }
    }

    /*
     * Insert data into the database
     * @param string name of the table
     * @param array the data for inserting into the table
     */
    public function insert($table, $data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values = '';
            $i = 0;

            // OPTIONAL BLOCK
            // if (!array_key_exists('payment_code', $data)) {
            //     $data['payment_code'] = uniqid();
            // }

            $columnString = implode(',', array_keys($data));
            $valueString = ':'.implode(',:', array_keys($data));
            $sql = 'INSERT INTO '.$table.' ('.$columnString.') VALUES ('.$valueString.')';
            $query = $this->db->prepare($sql);
            foreach ($data as $key => $val) {
                $query->bindValue(':'.$key, $val);
            }
            $insert = $query->execute();

            return $insert ? $this->db->lastInsertId() : false;
        }

        return false;
    }

    /*
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */
    public function getRows($table, $conditions = [])
    {
        $sql = 'SELECT ';
        $sql .= array_key_exists('select', $conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM '.$table;

        if (array_key_exists('where', $conditions)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($conditions['where'] as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $sql .= $pre.$key." = '".$value."'";
                ++$i;
            }
        }

        if (array_key_exists('order_by', $conditions)) {
            $sql .= ' ORDER BY '.$conditions['order_by'];
        }

        if (array_key_exists('start', $conditions) && array_key_exists('limit', $conditions)) {
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];
        } elseif (!array_key_exists('start', $conditions) && array_key_exists('limit', $conditions)) {
            $sql .= ' LIMIT '.$conditions['limit'];
        }

        $query = $this->db->prepare($sql);
        $query->execute();

        if (array_key_exists('return_type', $conditions) && 'all' != $conditions['return_type']) {
            switch ($conditions['return_type']) {
                case 'count':
                    $data = $query->rowCount();

                    break;

                case 'single':
                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    break;

                default:
                    $data = '';
            }
        } else {
            if ($query->rowCount() > 0) {
                $data = $query->fetchAll();
            }
        }

        return !empty($data) ? $data : false;
    }

    /*
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     * The value in condition['where'] must be a full statement of a select query Like '= value OR LIKE '%value%' OR etc.'
     */
    public function dynamicGetRows($table, $conditions = [])
    {
        $sql = 'SELECT ';
        $sql .= array_key_exists('select', $conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM '.$table;

        if (array_key_exists('where', $conditions)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($conditions['where'] as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $sql .= $pre.$key.$value;
                ++$i;
            }
        }

        if (array_key_exists('order_by', $conditions)) {
            $sql .= ' ORDER BY '.$conditions['order_by'];
        }

        if (array_key_exists('start', $conditions) && array_key_exists('limit', $conditions)) {
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];
        } elseif (!array_key_exists('start', $conditions) && array_key_exists('limit', $conditions)) {
            $sql .= ' LIMIT '.$conditions['limit'];
        }

        $query = $this->db->prepare($sql);
        $query->execute();

        if (array_key_exists('return_type', $conditions) && 'all' != $conditions['return_type']) {
            switch ($conditions['return_type']) {
                case 'count':
                    $data = $query->rowCount();

                    break;

                case 'single':
                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    break;

                default:
                    $data = '';
            }
        } else {
            if ($query->rowCount() > 0) {
                $data = $query->fetchAll();
            }
        }

        return !empty($data) ? $data : false;
    }

    /*
     * Update data into the database
     * @param string name of the table
     * @param array the data for updating into the table
     * @param array where condition on updating data
     */
    public function update($table, $data, $conditions)
    {
        if (!empty($data) && is_array($data)) {
            $colvalSet = '';
            $whereSql = '';
            $i = 0;

            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $colvalSet .= $pre.$key."='".$val."'";
                ++$i;
            }
            if (!empty($conditions) && is_array($conditions)) {
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach ($conditions as $key => $value) {
                    $pre = ($i > 0) ? ' AND ' : '';
                    $whereSql .= $pre.$key." = '".$value."'";
                    ++$i;
                }
            }
            $sql = 'UPDATE '.$table.' SET '.$colvalSet.$whereSql;
            $query = $this->db->prepare($sql);
            $update = $query->execute();

            return $update ? $query->rowCount() : false;
        }

        return false;
    }

    /*
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete($table, $conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre.$key." = '".$value."'";
                ++$i;
            }
        }
        $sql = 'DELETE FROM '.$table.$whereSql;
        $delete = $this->db->exec($sql);

        return $delete ? $delete : false;
    }
}
