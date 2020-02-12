<?php

class SqlTool
{
    public static $sqltool = false;     //保存一个类的实例
    public $mysqli;

    public $affectedRows = null;
    public $insertID = null;

    public function __construct()
    {
        global $dbInfo;
        $this->mysqli = @new MySqli($dbInfo['host'], $dbInfo['user'], $dbInfo['password'], $dbInfo['database']);
        if ($this->mysqli->connect_error) {
            Helper::throwException('Can not access to DB '.$dbInfo['database'], 500);
        }
        $this->mysqli->query("set names utf8");
    }

    /**
     * 返回一个SqlTool类的实例
     * 这种写法不能降低3306连接数,但是可以省去每个页面多次因操作数据库,反复连接所需要的时间
     * @return bool|SqlTool
     */
    public static function getSqlTool()
    {
        if (self::$sqltool==false) {
            self::$sqltool = new self();
        }
        return self::$sqltool;
    }

    /**
     * 用参数绑定的方式, 向数据库发送一条sql语法, 如果有语法错误, 则终止程序运行并将错误报出
     * @param $sql
     * @param array|null $bindParams
     * @return bool|mysqli_result
     * @throws Exception
     */
    public function query($sql, array $bindParams = null)
    {
//        $stmt = $this->mysqli->prepare($sql) or Helper::throwException($this->mysqli->errno . " : " . $this->mysqli->error . "==>" . $sql, 500);
        if (DEV_MODEL || @$_COOKIE['cc_ia']==1) {
            $stmt = $this->mysqli->prepare($sql) or Helper::throwException($this->mysqli->errno . " : " . $this->mysqli->error . "==>" . $sql, 500);
        } else {
            $stmt = $this->mysqli->prepare($sql) or Helper::throwException("QUERY语句出错", 500);
        }
        if (is_array($bindParams) === true) {
            $params = array('');
            foreach ($bindParams as $prop => $val) {
                $params[0] .= self::determineType($val);
                array_push($params, $bindParams[$prop]);
            }
            $refs = array();
            foreach ($params as $key => $value) {
                $refs[$key] = &$params[$key];
            }
            call_user_func_array(array($stmt, 'bind_param'), $refs);
        }
        $stmt->execute();
        if ($this->mysqli->errno != 0) {
            if (DEV_MODEL || @$_COOKIE['cc_ia']==1) {
                Helper::throwException($this->mysqli->errno . ": " . $this->mysqli->error, 500);
            } else {
                Helper::throwException('Internal error of query', 500);
            }
        }
        $result = $stmt->get_result();
        $this->affectedRows = $this->mysqli->affected_rows;
        $this->insertID = $this->mysqli->insert_id;
        $stmt->free_result();
        $stmt->close();
        return $result;
    }


    /**
     * 根据select查询语句, 将多行结果封装成一个一维数组返回
     * @param $sql
     * @param array|null $bindParams
     * @return array(一维)|null
     * @throws Exception
     */
    public function getRowBySql($sql, array $bindParams = null)
    {
        $result = $this->query($sql, $bindParams);
        if ($result) {
            $arr = $result->fetch_assoc();
            $result->free();
            return $arr;
        } else {
            return null;
        }
    }

    /**
     * 根据select查询语句, 将多行结果封装成一个二维数组返回
     * @param $sql
     * @param array|null $bindParams
     * @return array(二维)|null
     * @throws Exception
     */
    public function getListBySql($sql, array $bindParams = null)
    {
        $result = $this->query($sql, $bindParams);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $arr[] = $row;
            }
            $result->free();
            return $arr;
        } else {
            return null;
        }

    }

    private function determineType($item)
    {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;
            case 'boolean':
            case 'integer':
                return 'i';
                break;
            case 'blob':
                return 'b';
                break;
            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    /**
     * Turn on rollback
     * Perform any queries after this function call,
     * Use `commit()` or `rollback()` to either process the commit or rollback the previous queries
     * @throws Exception
     */
    public function turnOnRollback() {
        $this->mysqli->autocommit(FALSE);
    }

    public function commit() {
        $this->mysqli->commit();
    }

    public function rollback() {
        $this->mysqli->rollback();
    }
}

?>
