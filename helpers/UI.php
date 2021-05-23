<?php

    class UI {
        public static function send($input)
        {
            echo json_encode($input);
        }

        public static function error($error, $msg)
        {
            http_response_code($error);
            echo json_encode(
                array(
                    'error' => $error,
                    'message' => $msg
                )
            );
        }
    }