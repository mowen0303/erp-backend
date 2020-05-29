<?php

abstract class Model {
    protected $sqltool = null;      //寄存SqlTool的对象
    protected $pageHtml = null;
    protected $totalPage = 0;
    protected $totalAmount = 0;
    public $imgError = null;

    public function __construct() {
        //获取连接指针
        $this->sqltool = SqlTool::getSqlTool();
    }

    /**
     * 通过一个select查询语句返回一个二维数组,并同时封装一段分页代码
     * @param string $table
     * @param string $sql
     * @param array|null $bindParams
     * @param int $pageSize
     * @return array(二维)|null
     * @throws Exception
     */
    protected function getListWithPage($table, $sql, array $bindParams = null, $pageSize = 40) {
        $oldSql = $sql;
        $pageCurrent = @$_GET['page'] ?: 1;
        $limitX = ($pageCurrent - 1) * $pageSize;
        $sql .= " limit {$limitX},{$pageSize}";
        $sql = strtolower($sql);
        $arr = $this->sqltool->getListBySql($sql, $bindParams);
        if ($arr) {
            //封装分页
            $sqlStr1 = explode('from', $sql,2);
            $afterFromStr = explode('limit', $sqlStr1[1])[0];
            $afterFromAndBeforOrderBy = explode('order by', $afterFromStr);
            if(count($afterFromAndBeforOrderBy)==2){
                $afterFromStr = $afterFromAndBeforOrderBy[0];
            }
            if(strpos($sql,'distinct')!==false){
                $sql = "SELECT COUNT(DISTINCT {$table}_id) as amount FROM " . $afterFromStr;
            } else if(strpos($sql,'group by')!==false) {
                $sql = "SELECT COUNT(*) as amount FROM ($oldSql) AS a";
            } else {
                $sql = "SELECT COUNT({$table}_id) as amount FROM " . $afterFromStr;
            }
            $row = $this->sqltool->getRowBySql($sql, $bindParams);
            $pageCount = ceil($row['amount'] / $pageSize);
            $this->totalAmount = $row['amount'];
            $this->totalPage = $pageCount;
            $url = basename($_SERVER['REQUEST_URI']);
            $pageName = "?page=";
            if (strpos($url, '?') !== false && strpos($url, '?page=') === false) {
                $pageName = "&page=";
            }
            if ($_GET['page']) {
                $url = explode($pageName, $url)[0];
            }
            $pageHtml = '<div class="btn-group">';
            if ($pageCount > 1) {

                if ($pageCurrent == 1) {
                    $pageHtml .= '';
                } else {
                    $pageHtml .= '<a class="btn btn-default btn-outline waves-effect" href="' . $url . $pageName . 1 . '">&lt;&lt;</a>';
                    //$pageHtml .= '<a class="btn btn-default btn-outline waves-effect" href="' . $url . $pageName . ($pageCurrent - 1) . '">&lt;</a>';
                }

                for ($i = 1; $i <= $pageCount; $i++) {
                    $pageCurrentHtml = null;
                    if ($pageCurrent == $i) {
                        $pageCurrentHtml = 'class="btn btn-info waves-effect"';
                    }else{
                        $pageCurrentHtml = 'class="btn btn-default btn-outline waves-effect"';
                    }
                    if ($pageCurrent - $i < 5 && $i - $pageCurrent < 5) {
                        $pageHtml .= '<a ' . $pageCurrentHtml . ' href="' . $url . $pageName . $i . '">' . $i . '</a>';
                    }
                }

                if ($pageCurrent == $pageCount) {
                    $pageHtml .= '';
                } else {
                    //$pageHtml .= '<a class="btn btn-default btn-outline waves-effect" href="' . $url . $pageName . ($pageCurrent + 1) . '">&gt;</a>';
                    $pageHtml .= '<a class="btn btn-default btn-outline waves-effect" href="' . $url . $pageName . $pageCount . '">&gt;&gt;</a>';
                }

                $pageHtml .= '</div><span class="label label-danger label-rouded m-l-10">共有 '.$this->totalAmount.' 条结果</span>';
                $this->pageHtml = $pageHtml;
            }
        }
        return $arr;
    }

    /**
     * 输出翻页html代码块
     */
    public function echoPageList() {
        echo $this->pageHtml;
    }

    protected function getTotalPage() {
        return ["totalPage" => $this->totalPage];
    }

    protected function echoTotalAmount() {
        echo $this->totalAmount;
    }

    /**
     * 据id值从某张表内获得一条数据
     * @param string $table
     * @param int $id
     * @return array(一维)|null
     * @throws Exception
     */
    protected function getRowById($table, $id) {
        $sql = "select * from $table where {$table}_id in (?)";
        return $this->sqltool->getRowBySql($sql, [$id]);
    }

    /**
     * 向某张表内插入一条数据
     * @param string $table
     * @param array $arrKV      把字段和值封装到键值对数组中 arr[字段名] = 值
     * @return int              插入数据所在的id
     * @throws Exception
     */
    protected function addRow($table, $arrKV) {
        $field = "";
        $questionMark = "";
        $valueArr = [];
        foreach ($arrKV as $k => $v) {
            $field .= $k . ",";
            $questionMark .= "?,";
            $valueArr[] = $v;
        }
        $field = substr($field, 0, -1);
        $questionMark = substr($questionMark, 0, -1);
        $sql = "insert into {$table} ({$field}) values ($questionMark)";
        $this->sqltool->query($sql, $valueArr);
        $this->sqltool->insertID > 0 or Helper::throwException("添加失败 " . $this->sqltool->mysqli->error,500);
        return $this->sqltool->insertID;
    }

    /**
     * 通过主键id修改一条数据
     * @param string $table
     * @param int $id
     * @param array $arrKV  把字段和值封装到键值对数组中 arr[字段名] = 值
     * @param bool $isThrowExceptionOnNoAffectedRows
     * @return int          输数据所受影响的行数
     * @throws Exception
     */
    protected function updateRowById($table, $id, $arrKV, bool $isThrowExceptionOnNoAffectedRows = true) {
        $field = "";
        $valueArr = [];
        foreach ($arrKV as $k => $v) {
            $field .= "{$k}=?" . ",";
            $valueArr[] = $v;
        }
        $valueArr[] = $id;
        $field = substr($field, 0, -1);
        $sql = "update {$table} set {$field} where {$table}_id in (?)";
        $this->sqltool->query($sql, $valueArr);
        if ($isThrowExceptionOnNoAffectedRows == true) {
            $this->sqltool->affectedRows > 0 or Helper::throwException("No data has been effected",400);
        }
        return $this->sqltool->affectedRows;
    }

    /**
     * 根据ID做`真删除`，成功则返回`影响的行数`
     * @param $table
     * @param int|array $IDs    支持array和int
     * @return null
     * @throws Exception
     */
    protected function deleteByIDsReally($table, $IDs) {
        $IDs = Helper::convertIDArrayToString($IDs);
        $sql = "DELETE FROM {$table} WHERE {$table}_id IN ({$IDs})";
        $this->sqltool->query($sql);
        $this->sqltool->affectedRows > 0 or Helper::throwException("Delete Failed",400);
        return $this->sqltool->affectedRows;
    }

    /**
     * 根据ID做`逻辑删除`，成功则返回`影响的行数`
     * @param $table
     * @param int|array $IDs    支持array和int
     * @return false|int
     * @throws Exception
     */
    protected function deleteByIDsLogically($table, $IDs) {
        $IDs = Helper::convertIDArrayToString($IDs);
        $sql = "UPDATE {$table} SET {$table}_status=0 WHERE {$table}_id IN ({$IDs})";
        $this->sqltool->query($sql);
        $this->sqltool->affectedRows > 0 or Helper::throwException("Delete Failed",400);
        return $this->sqltool->affectedRows;
    }

    /**
     * 判断某个字段下,除指定id行之外的其他行的值是不是唯一 (id的值一般是修改数据的时候用)
     * @param string $table
     * @param string $fieldName
     * @param mixed $fieldValue
     * @param int|null $exceptId
     * @return bool
     * @throws Exception
     */
    public function isExistByFieldValue($table, $fieldName, $fieldValue, $exceptId = null) {
        if ($exceptId) {
            $sql = "SELECT $fieldName FROM $table WHERE $fieldName = ? AND {$table}_id NOT IN (?)";
            $this->sqltool->query($sql, [$fieldValue, $exceptId]);
        } else {
            $sql = "SELECT $fieldName FROM $table WHERE $fieldName = ?";
            $this->sqltool->query($sql, [$fieldValue]);
        }
        return $this->sqltool->affectedRows > 0 ? true : false;
    }

    public function isExist($table, array $arr, $exceptId = null) {

        $condition = [];
        $param = [];
        foreach ($arr as $key => $val){
            $condition[] = "{$key} = ?";
            $param[] = $val;
        }
        $conditionStr = implode(' AND ',$condition);
        count($param) > 0 or Helper::throwException("[ERROR][FUNCTION] isExist: Array is not validate");
        if ($exceptId) {
            $sql = "SELECT {$table}_id FROM $table WHERE {$conditionStr} AND {$table}_id NOT IN (?)";
            $this->sqltool->query($sql, array_merge($param,$exceptId));
        } else {
            $sql = "SELECT {$table}_id FROM $table WHERE {$conditionStr}";
            $this->sqltool->query($sql, $param);
        }
        return $this->sqltool->affectedRows > 0 ? true : false;
    }


    /**
     * 判断数组里的ids是不是$tableName表里的主键
     * @precondition no duplicate id in $ids
     * @param string $tableName
     * @param array $ids
     * @param bool $ignoreDeletedRow
     * @return bool
     * @throws Exception
     */
    public function isIdsExist(string $tableName, array $ids, bool $ignoreDeletedRow = false){
        $condition = "{$tableName}_id IN ({Helper::convertIDArrayToString($ids)})";
        if ($ignoreDeletedRow) $condition .= " AND {$tableName}_status NOT IN (0)";
        $sql = "SELECT COUNT(*) AS count FROM {$tableName} WHERE {$condition}";
        return $this->sqltool->getRowBySql($sql)["count"] == count($ids);
    }
}

?>