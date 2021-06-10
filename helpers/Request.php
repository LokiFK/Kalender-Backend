<?php

    class Request {
        private mixed $body;
        private mixed $method;

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