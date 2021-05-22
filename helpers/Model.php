<?php

    class Model extends Queryable {
        const INCLUDE_FOREIGN_DATA = true;
        const IGNORE_FOREIGN_DATA = false;

        public static function fetchAll($tableName="")
        {
            return DB::db()->query("SELECT * FROM " . $tableName . ";");
        }

        public static function fetch($id, $tableName="")
        {
            return DB::db()->query("SELECT * FROM " . $tableName . " WHERE id = :id;", array(':id' => $id));
        }

        public static function where($conditions, $withForeignRelations=false, $tableName="")
        {
            $conditionsString = $conditions["control"];
            $conditionValues = array();
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
            $sql = "SELECT * FROM $tableName"; // WHERE $conditionsString
            if ($withForeignRelations) {
                $columnNames = DB::db()->queryColumns("$tableName");
                for ($i=0; $i < count($columnNames); $i++) {
                    if (str_contains($columnNames[$i], "_id")) {
                        $tableColumnName = $columnNames[$i];
                        $foreignTableName = str_replace("_id", '', $columnNames[$i]) . "s";
                        $sql .= " INNER JOIN " . $foreignTableName . " ON $tableName.$tableColumnName = $foreignTableName.id";
                    }
                }
            }
            $sql .= " WHERE $conditionsString;";
            return DB::db()->query($sql, $conditionValues);
        }

        public static function drop($tableName="")
        {
            DB::db()->query("DROP TABLE " . $tableName);
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