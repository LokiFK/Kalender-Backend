<?php

    class DB {

        public const DATE_FORMAT = "Y/m/d H:i:s";
        public const DATE = "Y-m-d";
        public const TIME = "H:i:s";
        public const ASC = 0;
        public const DESC = 1;
        
        const host = '127.0.0.1';
        const dbname = 'calendar';
        const username = 'root';
        const password = '';

        private static function connect(): PDO
        {
            try {
                $pdo = new PDO('mysql:host=' . DB::host . ';dbname=' . DB::dbname . ';charset=utf8', DB::username, DB::password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                ErrorUI::error(500, 'Error connecting to DB');
                exit;
            }
        }

        public static function query(string $query, array $params = array())
        {
            try {
                $db = DB::connect();
                $stmt = $db->prepare($query);
                $stmt->execute($params);

                if (strtoupper(explode(' ', $query)[0]) == 'SELECT') {
                    $result = $stmt->fetchAll();
                    if (isset($result)) {
                        return $result;
                    }
                } else if (strtoupper(explode(' ', $query)[0]) == 'INSERT') {
                    return $db->lastInsertId();
                } /*else if (strtoupper(explode(' ', $query)[0]) == 'UPDATE' || strtoupper(explode(' ', $query)[0]) == 'DELETE') {
                    return DB::connect()->affected_rows;
                }*/
            } catch (PDOException $e) {
                ErrorUI::errorFiveHundred($e);
                exit;
            }
        }

        public static function table(string $tableName): TableReturn
        {
            return new TableReturn($tableName);
        }

        public static function queryColumns(string $tableName): array
        {
            try {
                $stmt = DB::connect()->prepare("DESCRIBE $tableName");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (PDOException $e) {
                ErrorUI::errorFiveHundred($e);
                exit;
            }
        }
    }

    class TableReturn {

        private $name;
        private $contents;
        private $orderedBy;
        private $orderedByDirection;

        public function __construct(string $name)
        {
            $this->name = $name;
            $this->contents = DB::query("SELECT * FROM `$name`");
        }

        public function where(string $query, array $params = array())
        {
            $this->contents = DB::query("SELECT * FROM `$this->name` WHERE $query", $params);
            return $this;
        }

        public function orderBy(string $column, int $direction = 0)
        {
            $this->orderedByDirection = $direction;
            $this->orderedBy = $column;
            usort($this->contents, function ($a, $b) {
                return $this->orderedByDirection == 0
                    ? (strtolower($a[$this->orderedBy]) <=> strtolower($b[$this->orderedBy]))
                    : (strtolower($b[$this->orderedBy]) <=> strtolower($a[$this->orderedBy]));
            });
            return $this;
        }

        public function get(array $foreignData = array(), array $columns = array())
        {
            $contentData = $this->contents;

            if (count($foreignData) > 0) {
                for ($i=0; $i < count($this->contents); $i++) {
                    for ($j=0; $j < count($foreignData); $j++) {
                        $foreignTable = $foreignData[$j]->getRelationTable();
                        $foreignColumn = $foreignData[$j]->getRelationColumn();
                        $foreignKey = $foreignData[$j]->getKey();
                        $data = DB::query("SELECT $foreignTable.* FROM $foreignTable INNER JOIN $this->name ON $foreignTable.$foreignColumn = :foreign_column_val LIMIT 1", [':foreign_column_val' => $this->contents[$i][$foreignKey]])[0];
                        $contentData[$i][str_replace("ID", '', $foreignData[$j]->getKey())] = $data;
                        unset($contentData[$i][$foreignData[$j]->getKey()]);
                    }
                }
            }

            if (count($columns) == 0) { return $contentData; }

            $finalData = array();
            for ($i=0; $i < count($contentData); $i++) {
                for ($j=0; $j < count($columns); $j++) {
                    $columnNameRelation = explode(".", $columns[$j]);
                    if (isset($contentData[$i][$columns[$j]])) {
                        $finalData[$i][$columns[$j]] = $contentData[$i][$columns[$j]];
                    } else if (isset($contentData[$i][$columnNameRelation[0]][$columnNameRelation[1]])) {
                        $finalData[$i][$columnNameRelation[0]][$columnNameRelation[1]] = $contentData[$i][$columnNameRelation[0]][$columnNameRelation[1]];
                    }
                }
            }
            return $finalData;
        }
    }

    class ForeignDataKey {
        private $key = "";
        private $relationTable = "";
        private $relationColumn = "";

        public function __construct($key, $relationTable, $relationColumn)
        {
            $this->key = $key;
            $this->relationTable = $relationTable;
            $this->relationColumn = $relationColumn;
        }

        public function getKey()
        {
            return $this->key;
        }

        public function getRelationTable()
        {
            return $this->relationTable;
        }

        public function getRelationColumn()
        {
            return $this->relationColumn;
        }
    }

?>
