<?php

    class Queryable implements ManualAttributes {

        protected $tableName = "";
        private array $attributes = array();

        public function __construct($tableName)
        {
            $this->tableName = $tableName;
            $this->columns();
        }

        public function create()
        {
            $attributesString = "";
            for ($i=0; $i < count($this->attributes); $i++) { 
                $attributesString .= $this->attributes[$i]->getName() . " " . $this->attributes[$i]->getType() . "(" . $this->attributes[$i]->getLenght() . ")";
                if ($i < count($this->attributes)-1) {
                    $attributesString .= ", ";
                }
            }
            try {
                DB::db()->query("CREATE TABLE $this->tableName ($attributesString);");
            } catch (Exception $e) {

            }
        }

        public function foreign(string $columnName, string $relatingTable, string $relatingColumn)
        {
            $column = NULL;
            for ($i=0; $i < count($this->attributes); $i++) { 
                if ($this->attributes[$i]->getName() == $columnName) {
                    $column = $this->attributes[$i];
                }
            }
            DB::db()->query("ALTER TABLE " . $this->tableName . " MODIFY " . $columnName . " " . $column->getType() . "(" . $column->getLenght() . ") UNSIGNED;");
            DB::db()->query("ALTER TABLE `$this->tableName` ADD FOREIGN KEY (`$columnName`) REFERENCES `$relatingTable`(`$relatingColumn`) ON DELETE CASCADE;");
        }

        public function primary(string $columnName)
        {
            $column = NULL;
            for ($i=0; $i < count($this->attributes); $i++) { 
                if ($this->attributes[$i]->getName() == $columnName) {
                    $column = $this->attributes[$i];
                }
            }
            DB::db()->query("ALTER TABLE $this->tableName ADD PRIMARY KEY ($columnName);");
            DB::db()->query("ALTER TABLE " . $this->tableName . " MODIFY " . $columnName . " " . $column->getType() . "(" . $column->getLenght() . ") UNSIGNED AUTO_INCREMENT;");
        }

        public function string(string $name, int $length)
        {
            array_push($this->attributes, new Column($name, "VARCHAR", $length));
        }

        public function int(string $name, $length)
        {
            array_push($this->attributes, new Column($name, "INT", $length));
        }

        public function columns() {}
    }

    class Column {
        private $name;
        private $type;
        private $length;

        public function __construct($name, $type, $length)
        {
            $this->name = $name;
            $this->type = $type;
            $this->length = $length;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getType()
        {
            return $this->type;
        }

        public function getLenght()
        {
            return $this->length;
        }
    }

    interface ManualAttributes {
        public function columns();
    }

?>

