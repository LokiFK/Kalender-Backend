<?php

    class DB {

        const host = '127.0.0.1';
        const dbname = 'calendar';
        const username = 'root';
        const password = '';

        private static function connect()
        {
            try {
                $pdo = new PDO('mysql:host=' . DB::host . ';dbname=' . DB::dbname . ';charset=utf8', DB::username, DB::password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                UI::error(500, 'Error connecting to DB: ' . $e->getMessage());
                exit;
            }
        }

        public static function query($query, $params = array())
        {
            try {
                $stmt = DB::connect()->prepare($query);
                $stmt->execute($params);

                if (explode(' ', $query)[0] == 'SELECT') {
                    $result = $stmt->fetchAll();
                    if (isset($result)) {
                        return $result;
                    }
                } else if (explode(' ', $query)[0] == 'INSERT') {
                    return DB::connect()->lastInsertID();
                }
            } catch (PDOException $e) {
                echo $query;
                UI::error(500, 'Error querying DB: ' . $e->getMessage());
                exit;
            }
        }

        public static function table($tableName)
        {
            
        }

        public static function queryColumns($tableName)
        {
            try {
                $stmt = DB::connect()->prepare("DESCRIBE $tableName");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (PDOException $e) {
                UI::error(500, 'Error querying DB: ' . $e->getMessage());
                exit;
            }
        }
    }

    class TableReturn {
        public const ASC = 0;
        public const DESC = 1;

        private $name;
        private $contents;

        public function __construct($name)
        {
            $this->name = $name;
            $this->contents = DB::query("SELECT * FROM :tableName", array(':tableName' => $name));
        }

        public function orderBy($column = array(), $direction = 0)
        {
            
        }

        public function get($columns = array())
        {
            if (count($columns) == 0) { return $this->contents; }

            $returnArr = array();
            if (count($columns) == 1) {
                for ($i=0; $i < count($this->contents); $i++) {
                    if (isset($this->contents[$i][$columns[0]])) {
                        $returnArr[$i][$columns[0]] = $this->contents[$i][$columns[0]];
                    }
                }
                return $returnArr;
            }

            for ($i=0; $i < count($this->contents); $i++) { 
                for ($j=0; $j < count($columns); $j++) {
                    if (isset($this->contents[$i][$columns[$j]])) {
                        $returnArr[$i][$columns[$j]] = $this->contents[$i][$columns[$j]];
                    }
                }
            }
            
            return $returnArr;
        }
    }


    class TableContents {
        private $contents;

        public function __construct($contents)
        {
            $this->contents = $contents;
        }
    }