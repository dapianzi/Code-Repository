<?php

/**
 * MySQL PDO class.
 * @Author: Carl
 * @Since: 2017-7-19 15:10
 * Created by PhpStorm.
 */
class C_PDO_MySQL
{
    private $dbLink;
    private $db_pool;

    private $lastSql;
    private $errMessage;

    /**
     * @param $db_pool array(
     *      'db_host'   => 'localhost',
     *      'db_port'   => '3306',
     *      'database'  => 'db_name',
     *      'username'  => 'user',
     *      'password'  => 'pass',
     * )
     */
    public function __construct($db_pool)
    {
        $this->db_pool = $db_pool;
    }

    public function db_open($is_write=FALSE) {

        list($dsn, $username, $password) = $this->init_db_pool($this->db_pool, $is_write);

        $opts = array (
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => TRUE,
            PDO::ATTR_TIMEOUT => 60,
            // Cancel one specific SQL mode option that RackTables has been non-compliant
            // with but which used to be off by default until MySQL 5.7. As soon as
            // respective SQL queries and table columns become compliant with those options
            // stop changing @@SQL_MODE but still keep SET NAMES in place.
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "utf8", @@SQL_MODE = REPLACE(@@SQL_MODE, "NO_ZERO_DATE", "")',
        );
//        if (isset ($pdo_bufsize))
//            $opts[PDO::MYSQL_ATTR_MAX_BUFFER_SIZE] = $pdo_bufsize;
//        if (isset ($pdo_ssl_key))
//            $opts[PDO::MYSQL_ATTR_SSL_KEY] = $pdo_ssl_key;
//        if (isset ($pdo_ssl_cert))
//            $opts[PDO::MYSQL_ATTR_SSL_CERT] = $pdo_ssl_cert;
//        if (isset ($pdo_ssl_ca))
//            $opts[PDO::MYSQL_ATTR_SSL_CA] = $pdo_ssl_ca;
        try
        {
            $this->dbLink = new PDO ($dsn, $username, $password, $opts);
        }
        catch (PDOException $e)
        {
            throw new Exception($e);
        }
    }


    private function init_db_pool($db_conf) {
        return array(
            'mysql:host=' . $db_conf['db_host'] . ';dbname=' . $db_conf['database'] . ';port=' . $db_conf['db_port'],
            $db_conf['username'],
            $db_conf['password']
        );
    }

    public function isConnectOk() {
        return !!$this->dbLink;
    }

    public function query($sql, $param = array()) {
        $this->db_open();
        try {
            $pre = $this->dbLink->prepare($sql);
            $pre->execute($param);
            $this->lastSql = $pre->queryString;
            return $pre;
        } catch (PDOException $e) {
            $this->errMessage = $e->getMessage();
            throw new Exception($e);
        }
    }

    public function execute($sql, $param = array()) {
        $this->db_open(TRUE);
        try {
            $pre = $this->dbLink->prepare($sql);
            $pre->execute($param);
            $this->lastSql = $pre->queryString;
            return $pre;
        } catch (PDOException $e) {
            $this->errMessage = $e->getMessage();
            throw new Exception($e);
        }
    }

    public function insert($table, $columns) {
        $this->db_open(TRUE);
        $query = " INSERT INTO {$table} (" . implode (', ', array_keys ($columns));
        $query .= ') VALUES (' . $this->questionMarks (count ($columns)) . ')';
        // Now the query should be as follows:
        // INSERT INTO table (c1, c2, c3) VALUES (?, ?, ?)
        try
        {
            $pre = $this->dbLink->prepare ($query);
            $pre->execute (array_values ($columns));
            $this->lastSql = $pre->queryString;
            return $pre->rowCount();
        }
        catch (PDOException $e)
        {
            $this->errMessage = $e->getMessage();
            throw new Exception($e);
        }
    }

    public function update($table, $param, $where, $conjunction = 'AND') {
        $this->db_open(TRUE);
        if (!count($param)) {
            $this->errMessage = 'delete must have set.';
            throw new Exception('delete must have set.');
        }
        if (!count($where)) {
            $this->errMessage = 'delete must have where.';
            throw new Exception('delete must have where.');
        }
        $whereValues = array();
        $sql = " UPDATE $table SET " . $this->makeSetSQL(array_keys($param)) . ' WHERE ' . $this->makeWhereSQL($where, $conjunction, $whereValues);
        try {
            $pre = $this->dbLink->prepare ($sql);
            $pre->execute (array_merge (array_values ($param), $whereValues));
            unset($whereValues);
            $this->lastSql = $pre->queryString;
            return $pre->rowCount();
        } catch (PDOException $e) {
            $this->errMessage = $e->getMessage();
            throw new Exception($e);
        }
    }

    public function delete($table, $where, $conjunction = 'AND') {
        $this->db_open(TRUE);
        if (!count($where)) {
            $this->errMessage = 'delete must have where.';
            throw new Exception('delete must have where.');
        }
        $whereValues = array();
        $sql = " DELETE FROM $table WHERE " . $this->makeWhereSQL($where, $conjunction, $whereValues);
        try {
            $pre = $this->dbLink->prepare ($sql);
            $pre->execute ($whereValues);
            $this->lastSql = $pre->queryString;
            return $pre->rowCount();
        } catch (PDOException $e) {
            $this->errMessage = $e->getMessage();
            throw new Exception($e);
        }
    }

    public function beginTransaction() {
        $this->db_open(TRUE);
        return $this->dbLink->beginTransaction();
    }

    public function commit() {
        return $this->dbLink->commit();
    }

    public function rollBack() {
        return $this->dbLink->rollBack();
    }

    public function getColumn($sql, $col = 0, $param = array()) {
        $res = $this->query($sql, $param);
        $col = $res->fetchColumn($col);
        $res->closeCursor();
        return $col;
    }

    public function getKeyValue($sql, $param = array()) {
        $res = $this->query($sql, $param);
        return $res->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function getCount($sql, $param = array()) {
        $res = $this->query($sql, $param);
        return $res->rowCount();
    }

    public function getAll($sql, $param = array()) {
        $pre = $this->query($sql, $param);
        $res = $pre->fetchAll(PDO::FETCH_ASSOC);
        unset($pre);
        return $res;
    }

    public function getRow($sql, $param = array()) {
        $res = $this->query($sql, $param);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $res->closeCursor();
        return $row;
    }

    public function makeSetSQL($columns) {
        if (! count ($columns))
            throw new Exception ('columns must not be empty');
        $tmp = array();
        // Same syntax works for NULL as well.
        foreach ($columns as $each)
            $tmp[] = "${each}=?";
        return implode (', ', $tmp);
    }

    public function makeWhereSQL ($where_columns, $conjunction='AND', &$params = array())
    {
        if (! in_array (strtoupper ($conjunction), array ('AND', '&&', 'OR', '||', 'XOR')))
            throw new Exception ('conjunction'. $conjunction. 'invalid operator');
        if (! count ($where_columns))
            throw new Exception ('where_columns must not be empty');
        $params = array();
        $tmp = array();
        foreach ($where_columns as $colName => $colValue) {
            if ($colValue === NULL)
                $tmp[] = "$colName IS NULL";
            elseif (is_array ($colValue))
            {
                // Suppress any string keys to keep array_merge() from overwriting.
                $params = array_merge ($params, array_values ($colValue));
                $tmp[] = sprintf ('%s IN(%s)', $colName, $this->questionMarks (count ($colValue)));
            }
            else
            {
                $tmp[] = "${colName}=?";
                $params[] = $colValue;
            }
        }
        return implode (" ${conjunction} ", $tmp);
    }

    public function questionMarks($count) {
        if ($count <= 0) {
            throw new Exception('count must be greater than zero');
        }
        return implode(', ', array_fill(0, $count, '?'));
    }

    public function getLastSQL() {
        return $this->lastSql;
    }

    public function getLastInsertId() {
        return $this->dbLink->lastInsertId();
    }

    public function getError() {
        return iconv('gbk', 'utf-8', $this->errMessage);
    }

}

?>
