<?php

    class DB {

        private $connection;
        private $host = '127.0.0.1';
        private $dbname = 'calendar';
        private $username = 'root';
        private $password = '';

        public function __construct()
        {
            try {
                $pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->username, $this->password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection = $pdo;
            } catch (PDOException $e) {
                UI::error(500, 'Error connecting to DB: ' . $e->getMessage());
                exit;
            }
        }

        public function query($query, $params = array())
        {
            try {
                $stmt = $this->connection->prepare($query);
                $stmt->execute($params);

                if (explode(' ', $query)[0] == 'SELECT') {
                    $result = $stmt->fetchAll();
                    if (isset($result)) {
                        return $result;
                    }
                } else if (explode(' ', $query)[0] == 'INSERT') {
                    return $this->connection->lastInsertID();
                }
            } catch (PDOException $e) {
                echo $query;
                UI::error(500, 'Error querying DB: ' . $e->getMessage());
                exit;
            }
        }

        public function queryColumns($tableName)
        {
            try {
                $stmt = $this->connection->prepare("DESCRIBE $tableName");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (PDOException $e) {
                UI::error(500, 'Error querying DB: ' . $e->getMessage());
                exit;
            }
        }

        public static function db()
        {
            return new DB();
        }
    }