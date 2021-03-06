<?php

    class ErrorUI {
        public static function errorMsg($errCode, $msg)
        {
            echo json_encode(
                array(
                    'errorCode' => $errCode,
                    'message' => $msg
                )
            );
        }

        public static function error($errCode, $msg)
        {
            //debug_print_backtrace();
            $message = strval($errCode) . " " . $msg;
            echo "<script>alert('$message'); window.history.go(-1);</script>";
            /*http_response_code($errCode);
            if(strpos($msg, "/api")) {
                ErrorUI::errorMsg($errCode, $msg);
            } else {
                echo Response::view("general/error", ["errorCode" => $errCode, "msg" => $msg]);
            }
            exit();*/
        }

        public static function generalError() {
            ErrorUI::error(0000, "Unknown Error");
        }

        public static function errorFiveHundred($errCode)
        {
            ErrorUI::error(500, 'Error: ' . $errCode->getMessage());
        }

        public static function popRedirect($message, $redirect) {
            echo "<script>alert('$message'); window.location.href='$redirect';</script>";
        }

        public static function tetrisError(){
            echo Response::view("general/stuff/tetrisError");
        }
    }