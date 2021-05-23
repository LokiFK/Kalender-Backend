<?php

    class Model extends Queryable {
        const INCLUDE_FOREIGN_DATA = true;
        const IGNORE_FOREIGN_DATA = false;

        public static function fetchAll($tableName="")
        {
            return new QueryResult(DB::db()->query("SELECT * FROM " . $tableName . ";"));
        }

        public static function fetch($id, $tableName="")
        {
            return new QueryResult(DB::db()->query("SELECT * FROM " . $tableName . " WHERE id = :id;", array(':id' => $id)));
        }
 
        public static function delete($id, $tableName="")
        {
            DB::db()->query("DELETE FROM $tableName WHERE id = :id", array(':id' => $id));
        }

        public static function insert($values = array(), $tableName="")
        {
            $sql = "INSERT INTO $tableName(";
            $columnNames = DB::db()->queryColumns("$tableName");
            for ($i=1; $i < count($columnNames); $i++) { 
                $sql .= $columnNames[$i];
                if (isset($columnNames[$i+1])) {
                    $sql .= ", ";
                }
            }

            $sql .= ") VALUES (";
            $sqlParameters = array();
            for ($i=0; $i < count($values); $i++) {
                $columnValue = $values[$i];
                $sql .= ":a" . $i;
                $sqlParameters[":a" . $i] = $columnValue;
                if (isset($values[$i+1])) {
                    $sql .= ", ";
                }
            }
            $sql .= ");";

            DB::db()->query($sql, $sqlParameters);
        }

        public static function where($conditions, $withForeignRelations=false, $tableName="")
        {
            $returnArr = array();
            $conditionsString = "";
            $conditionValues = array();
            if (isset($conditions['control'])) {
                $conditionsString = $conditions["control"];
                $i = 0;
                while (true) {
                    if (!isset($conditionsString[$i])) break;
                    if (is_numeric($conditionsString[$i]) == 1) {
                        $currentNum = (int) $conditionsString[$i];
                        
                        $toAdd = "$tableName." . $conditions[$currentNum]->getAttributeName() . " " . $conditions[$currentNum]->getCompareOperator() . " :a" . $i;
                        $conditionsString = substr_replace($conditionsString, $toAdd, $i, 1);
                        $conditionValues[":a".$i] = $conditions[$currentNum]->getComparingValue();
                        $i += strlen($toAdd)-1;
                    }
                    $i++;
                }
            } else {
                $conditionsString = "$tableName." . $conditions[0] . " " . $conditions[1] . " :a";
                $conditionValues[":a"] = $conditions[2];
            }

            $res = DB::db()->query("SELECT * FROM $tableName WHERE $conditionsString;", $conditionValues);
            $resColumns = DB::db()->queryColumns($tableName);
            for ($i=0; $i < count($resColumns); $i++) {
                if (isset($res[$i])) {
                    $returnArr[$resColumns[$i]] = $res[$resColumns[$i]];
                }
            }
            
            if ($withForeignRelations) {
                $foreignData = array();
                $columnNames = DB::db()->queryColumns("$tableName");
                for ($i=0; $i < count($columnNames); $i++) {
                    if (str_contains($columnNames[$i], "_id")) {
                        $tableColumnName = $columnNames[$i];
                        $foreignTableName = str_replace("_id", '', $columnNames[$i]) . "s";
                        $data = DB::db()->query("SELECT $foreignTableName.* FROM $foreignTableName INNER JOIN $tableName ON $foreignTableName.id = $tableName.$tableColumnName");
                        $data["return_array_relation_key"] = $tableColumnName;
                        array_push($foreignData, $data);
                    }
                }

                for ($i=0; $i < count($foreignData); $i++) {
                    $key = $foreignData[$i]["return_array_relation_key"];
                    unset($foreignData[$i]["return_array_relation_key"]);
                    $returnArr[str_replace("_id", '', $key)] = $foreignData[$i];
                    unset($returnArr[$key]);
                }
            }

            return new QueryResult($returnArr);
        }

        public static function drop($tableName="")
        {
            DB::db()->query("DROP TABLE " . $tableName);
        }
    }

    class QueryResult {
        private $result;

        public function __construct($result)
        {
            $this->result = $result;
        }

        public function get($columns=array())
        {
            if (count($columns) == 0) { return $this->result; }

            if (count($columns) == 1 && isset($this->result[$columns[0]])) {
                return $this->result[$columns[0]];
            } else {
                return;
            }

            $returnArr = array();
            for ($i=0; $i < count($columns); $i++) {
                if (isset($this->result[$columns[$i]])) {
                    array_push($returnArr, $this->result[$columns[$i]]);
                }
            }
            return $returnArr;
        }
    }

    class Condition {
        private $attributeName;
        private $compareOperator;
        private $comparingValue;

        public function __construct($attributeName, $compareOperator, $comparingValue)
        {
            $this->attributeName = $attributeName;
            $this->compareOperator = $compareOperator;
            $this->comparingValue = $comparingValue;
        }

        public function getAttributeName()
        {
            return $this->attributeName;
        }

        public function getCompareOperator()
        {
            return $this->compareOperator;
        }

        public function getComparingValue()
        {
            return $this->comparingValue;
        }
    }


?>