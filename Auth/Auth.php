<?php

    class Auth {

        const GUEST = 0;
        const USER = 1;
        const ADMIN = 2;

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

        public static function registerUser(User $user)
        {
            return DB::query("INSERT INTO users (vorname, nachname, geburtstag, patientenID) VALUES (:vorname, :nachname, :geburtstag, :patientenID)", [':vorname' => $user->vorname, ':nachname' => $user->nachname, ':geburtstag' => $user->geburtstag, ':patientenID' => $user->patientenID]);
        }

        public static function registerAccount(Account $account)
        {
            if (Auth::userExists($account->userID)) {
                if (!$account->approvalNeeded) {
                    return DB::query("INSERT INTO account (userid, username, email, password, erstellungsdatum) VALUES (:userID, :username, :email, :password, :date);",[':userID' => $account->userID, ':username' => $account->username, ':email' => $account->email, ':password' => password_hash($account->password, PASSWORD_DEFAULT), ':date' => null]);
                }
                $token = bin2hex(random_bytes(64));
                DB::query("INSERT INTO notapproved (userid, token, datetime) VALUES (:userID, :token, :date);", [':userID' => $account->userID, ':token' => $token, ':date' => date('Y-M-D')]);
                $from = "FROM Terminplanung @noreply";
                $subject = "Account bestätigen";
                $msg = $token;
                mail($account->email, $subject, $msg, $from);
            }
        }

        public static function approveAccount($token) {
            $userID = DB::table('notapproved')->where('token = :token', [':token' => $token]);
            DB::query("UPDATE account SET erstellungsdatum = " . date('Y-M-D') . " WHERE userID = :userID;", [':userID' => $userID]);
        }

        public static function login($username, $password, $ip, $remember)
        {
            $account = DB::table('account')->where('username = :username', [':username' => $username])[0];

            if (password_verify($password, $account['password'])) {
                $token = bin2hex(random_bytes(64));
                $start = date('Y-M-D H:M:S');
                $end = null;
                if (!$remember) {
                    $date = new DateTime();
                    $date->add(new DateInterval(Auth::DURATION));
                    $end = $date->format('Y-M-D H:M:S');
                }
                DB::query("INSERT INTO `session` (userid, token, start, end, ip) VALUES (:userID, :token, :start, :end, :ip);", [':userID' => $account['userID'], ':token' => $token, ':start' => $start, ':end' => $end, ':ip' => $ip]);
                return $token;
            }
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

        public static function userExists($userID)
        {
            $res = DB::query("SELECT count(*) AS 'Anzahl' FROM user WHERE id = :userid;", [':userid' => $userID]);
            return $res[0]['Anzahl'] > 0;
        }
    }


    class User {
        public string $vorname = "";
        public string $nachname = "";
        public string $anrede = "";
        public string $geburtstag = "";
        public string $patientenID = "";
    }

    class Account {
        public string $userID = "";
        public string $username = "";
        public string $email = "";
        public string $password = "";
        public string $approvalNeeded = "";
    }

?>