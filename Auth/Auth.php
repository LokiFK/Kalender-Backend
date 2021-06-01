<?php

    class Auth {

        const GUEST = 0;
        const USER = 1;
        const ADMIN = 2;
        const DOCTOR = 3;

        public static function user()
        {
            $token = Auth::getToken();
            if (!isset($token)) { return; }

            $tokenWithUser = DB::table('tokens')->where('token = :token', [':token' => $token])->get([new ForeignDataKey('user_id', 'users', 'id')])[0];
            if (!isset($tokenWithUser)) {
                ErrorUI::errorCode(401);
                ErrorUI::error('Invalid Token');
                exit;
            }

            return $tokenWithUser['user'];
        }

        public static function userID()
        {
            $token = Auth::getToken();
            return DB::table('tokens')->where('token = :token', [':token' => $token])->get([], ['user_id'])[0]['user_id'];
        }

        public static function login($userID)
        {
            $token = bin2hex(random_bytes(64));
            $timestamp = date("Y-m-d");

            DB::query("INSERT INTO tokens (user_id, token, timestamp) VALUES (:userID, :token, :timestamp);", [':userID' => $userID, ':token' => $token, ':timestamp' => $timestamp]);
            return $token;
        }

        public static function logout()
        {
            $token = Auth::getToken();
            if (!isset($token)) { return; }
            
            $userID = Auth::userID();
            if (!isset($userID)) { return; }

            $tokenID = DB::table('tokens')->where('token = :token AND user_id = :user_id', [':token' => $token, ':user_id' => $userID])->get([], ['id'])[0]['id'];
            DB::query('DELETE FROM tokens WHERE id = :id', [':id' => $tokenID]);
        }

        private static function isValidToken($token, $exitIfNot)
        {
            $timestamp = DB::table('tokens')->where('token = :token', [':token' => $token])->get([], ['timestamp'])[0]['timestamp'];
            if (!isset($timestamp) && $exitIfNot) {
                ErrorUI::errorCode(401);
                ErrorUI::error('Invalid Token');
                exit;
            }
            $lastLogin = new DateTime($timestamp);
            $currentTime = new DateTime();
            return $lastLogin->diff($currentTime)->d < 10;
        }

        public static function getToken()
        {
            $token = "";
            if (isset($_POST['token'])) $token = $_POST['token'];
            else if (isset($_GET['token'])) $token = $_GET['token'];
            else { return false; }
            if (Auth::isValidToken($token, true)) {
                return $token;
            }
        }

        public static function isLoggedIn()
        {
            $token = "";
            if (isset($_POST['token'])) $token = $_POST['token'];
            else if (isset($_GET['token'])) $token = $_GET['token'];
            else { return false; }

            $isValid = Auth::isValidToken($token, false);
            
            return isset($isValid) && $isValid;
        }

        public static function getPermissions()
        {
            $userID = Auth::userID();
            $permission = DB::query('SELECT permissions.permission FROM permissions INNER JOIN users ON permissions.user_id = :user_id', array(':user_id' => $userID));
            return $permission[0]['permission'];
        }
    }

?>