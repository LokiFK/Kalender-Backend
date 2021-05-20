<?php

    class DB {

        private $connection;
        private $host = '127.0.0.1';
        private $dbname = 'calendar';
        private $username = 'root';
        private $password = '';

        public function __construct()
        {
            $pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection = $pdo;
        }

        public function query($query, $params = array())
        {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);

            if (explode(' ', $query)[0] == 'SELECT') {
                return $stmt->fetchAll();
            }
        }

        public static function db()
        {
            return new DB();
        }
    }