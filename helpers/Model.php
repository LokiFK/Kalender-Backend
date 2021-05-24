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

            return DB::db()->query($sql, $sqlParameters);
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
                        
                        if (isset($conditions[$currentNum])) {
                            $toAdd = "$tableName." . $conditions[$currentNum][0] . " " . $conditions[$currentNum][1] . " :a" . $i;
                            $conditionsString = substr_replace($conditionsString, $toAdd, $i, 1);
                            $conditionValues[":a".$i] = $conditions[$currentNum][2];
                            $i += strlen($toAdd)-1;
                        } else {
                            UI::error(500, 'Error processing sql conditions');
                            exit;
                        }
                    }
                    $i++;
                }
            } else {
                $conditionsString = "$tableName." . $conditions[0] . " " . $conditions[1] . " :a";
                $conditionValues[":a"] = $conditions[2];
            }

            $res = DB::db()->query("SELECT * FROM $tableName WHERE $conditionsString;", $conditionValues);
            $resColumns = DB::db()->queryColumns($tableName);
            for ($i=0; $i < count($res); $i++) { 
                for ($j=0; $j < count($resColumns); $j++) {
                    if (isset($res[$i][$j])) {
                        $returnArr[$i][$resColumns[$j]] = $res[$i][$resColumns[$j]];
                    }
                }
            }

            
            if ($withForeignRelations) {
                $foreignData = array();
                $columnNames = DB::db()->queryColumns("$tableName");
                for ($i=0; $i < count($columnNames); $i++) {
                    if (str_contains($columnNames[$i], "_id")) {
                        $tableColumnName = $columnNames[$i];
                        $foreignTableName = str_replace("_id", '', $columnNames[$i]) . "s";
                        $data = DB::db()->query("SELECT $foreignTableName.* FROM $foreignTableName INNER JOIN $tableName ON $foreignTableName.id = :foreign_column_val LIMIT 1", [':foreign_column_val' => $res[0][$tableColumnName]]);
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

            $returnArr = array();
            if (count($columns) == 1) {
                for ($i=0; $i < count($this->result); $i++) {
                    if (isset($this->result[$i][$columns[0]])) {
                        $returnArr[$i][$columns[0]] = $this->result[$i][$columns[0]];
                    }
                }
                return $returnArr;
            }

            for ($i=0; $i < count($this->result); $i++) { 
                for ($j=0; $j < count($columns); $j++) {
                    if (isset($this->result[$i][$columns[$j]])) {
                        $returnArr[$i][$columns[$j]] = $this->result[$i][$columns[$j]];
                    }
                }
            }
            
            return $returnArr;
        }
    }



?>