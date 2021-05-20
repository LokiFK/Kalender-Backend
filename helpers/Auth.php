<?php

    class Auth {

        public static function user()
        {
            $token = Auth::getToken();
            
            if (Auth::isValidToken($token)) {
                $userID = DB::db()->query("SELECT user_id FROM tokens WHERE token = :token", array(':token' => $token))[0]['user_id'];
                return DB::db()->query("SELECT * FROM users WHERE id = :id", array(':id' => $userID))[0];
            }
        }

        public static function login($userID)
        {
            $token = bin2hex(random_bytes(64));
            $timestamp = date("Y-m-d");
            DB::db()->query("INSERT INTO tokens (user_id, token, timestamp) VALUES (:user_id, :token, :timestamp)", array(':user_id' => $userID, ':token' => $token, ':timestamp' => $timestamp));
        }

        public static function logout()
        {
            $token = Auth::getToken();
            $userID = Auth::user()['id'];
            DB::db()->query("DELETE FROM tokens WHERE user_id = :user_id AND token = :token", array(':user_id' => $userID, ':token' => $token));
        }

        public static function isValidToken($token)
        {
            $timestamp = DB::db()->query("SELECT timestamp FROM tokens WHERE token = :token", array(':token' => $token));
            if (count($timestamp) > 0) {
                $lastLogin = new DateTime($timestamp[0]['timestamp']);
                $currentTime = new DateTime();
                return $lastLogin->diff($currentTime)->d < 10;
            }
            return false;
        }

        public static function userID()
        {
            $token = Auth::getToken();

            if (isset($token)) {
                return DB::db()->query("SELECT user_id FROM tokens WHERE token = :token", array(':token' => $token));
            }
        }

        public static function getToken()
        {
            $token = isset($_POST['token']) ? $_POST['token'] : $_GET['token'];
            return Auth::isValidToken($token) ? $token : NULL;
        }
    }

