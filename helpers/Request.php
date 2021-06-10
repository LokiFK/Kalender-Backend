<?php

    class Request {
        private $body;
        private $method;

        public function __construct($body = array(), $method = "")
        {
            $this->body = $body;
            $this->method = $method;
        }

        public function getBody()
        {
            return $this->body;
        }

        public function getMethod()
        {
            return $this->method;
        }
    }

?>