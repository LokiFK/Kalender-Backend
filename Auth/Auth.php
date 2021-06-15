<?php

    class Auth {
        
		const DURATION = '30 Minutes';

        public static function user()
        {
            $token = Auth::getCheckedToken();
            if (!isset($token)) { return; }

            $tokenWithUser = DB::table('session')->where('token = :token', [':token' => $token])->get([new ForeignDataKey('userID', 'users', 'id')]);
            if (!isset($tokenWithUser)) {
                ErrorUI::error(401, 'Invalid Token');
                exit;
            }

            return $tokenWithUser[0]['user'];
        }

        public static function userID()
        {
            $token = Auth::getCheckedToken();
            return DB::table('session')->where('token = :token', [':token' => $token])->get([], ['userID'])[0]['userID'];
        }

        public static function registerUser(User $user)
        {
            DB::query("INSERT INTO users (firstname, lastname, salutation, insurance, birthday, patientID) VALUES (:firstname, :lastname, :salutation, :insurance, :birthday, :patientID)", [':firstname' => $user->firstname, ':lastname' => $user->lastname, ':salutation' => $user->salutation, ':insurance' => $user->insurance, ':birthday' => $user->birthday, ':patientID' => $user->patientID]);
            $userID = DB::table('users')->where('firstname = :firstname', [':firstname' => $user->firstname])->get([], ['id'])[0]['id'];
            $code = Auth::createNewToken();
            
            DB::query("INSERT INTO notapproved (userID, code, datetime) VALUES (:userID, :code, :date);", [':userID' => $userID, ':code' => $code, ':date' => date('Y-M-D')]);
            $from = "FROM Terminplanung @noreply";
            $subject = "Account bestätigen";
            $msg = $code;
            return $code;
            //mail($account->email, $subject, $msg, $from);
        }

        public static function registerAccount(Account $account)
        {
            if (Auth::userExists($account->userID)) {
                DB::query("INSERT INTO account (userID, username, email, password, createdAt) VALUES (:userID, :username, :email, :password, :createdAt);",[':userID' => $account->userID, ':username' => $account->username, ':email' => $account->email, ':password' => password_hash($account->password, PASSWORD_DEFAULT), ':createdAt' => date('Y-m-d')]);
            }
        }

        public static function approveAccount($token, $code) {
            $userID = DB::table('notapproved')->where('token = :token AND code = :code', [':token' => $token, ":code"=>$code]);
            DB::query("UPDATE account SET erstellungsdatum = " . date('Y-M-D') . " WHERE userID = :userID;", [':userID' => $userID]);
        }

        public static function login($username, $password, bool $remember): string
        {
            $account = DB::table('account')->where('username = :username', [':username' => $username])->get()[0];

            if (password_verify($password, $account['password'])) {
                $token = Auth::createNewToken();
                $start = date('Y-M-D H:M:S');
                $end = null;
                if (!$remember) {
                    $date = new DateTime();
                    $date->add(new DateInterval(Auth::DURATION));
                    $end = $date->format('Y-M-D H:M:S');
                }
                DB::query(
                    "INSERT INTO `session` (`userid`, `token`, `start`, `end`) VALUES (:userID, :token, :start, :end);",
                    [':userID' => $account['userID'], ':token' => $token, ':start' => $start, ':end' => $end]
                );
                return $token;
            }
        }

        public static function logout()
        {
            $token = Auth::getCheckedToken();

            DB::query('UPDATE `session` SET `end` = :end WHERE `token` = :token', [':token' => $token, ':end' => date('Y-M-D H:M:S')]);
        }

        public static function userExists($id): bool
        {
            $res = DB::query("SELECT `id` FROM `users` WHERE id = :id;", [':id' => $id]);
            return count($res) > 0;
        }

        private static function isValidToken($token): bool
        {
            $erg = DB::query("SELECT `end` FROM `session` WHERE `token` = :token AND (`end` IS NULL OR `end` < :end);", [':token' => $token, ":end" => date('Y-M-D H:M:S')]);
            if (count($erg) == 1) {
                if ($erg[0]['end'] != null) {
                    $date = new DateTime();
                    $date->add(new DateInterval(Auth::DURATION));
                    $end = $date->format('Y-M-D H:M:S');
                    DB::query("UPDATE `session` SET `end` = :end WHERE `token` = :token;", [':token' => $token, ':end' => $end]);
                } 
                return true;
            }
            return false;
        }

        public static function createNewToken()
        {
            try {
                $token = bin2hex(random_bytes(25));
            } catch (Exception $e) {
                ErrorUI::error(605, $e);
            }
            while (count(DB::table('session')->where('token = :token', [':token' => $token])->get()) > 0) {
                try {
                    $token = bin2hex(random_bytes(25));
                } catch (Exception $e) {
                    ErrorUI::error(605, $e);
                }
            }
            return $token;
        }

        public static function getCheckedToken() {
            $token = Auth::getToken();
            if ($token == null) {
                debug_print_backtrace();
                ErrorUI::error(401, 'Invalid Token');
                exit;
            }
            return $token;
        }
        public static function getToken() {
            $token = Auth::getGivenToken();
            if (Auth::isValidToken($token, false)) {
                return $token;
            }
        }
        public static function getGivenToken()
        {
            $token = "";
            if (isset($_POST['token'])) $token = $_POST['token'];
            else if (isset($_GET['token'])) $token = $_GET['token'];
            else if (isset($_COOKIE['token'])) $token = $_COOKIE['token'];
            else { return false; }
            return $token;
        }

        public static function isLoggedIn(): bool
        {
            $token = Auth::getToken();
            //echo "token: ".$token;
            return $token!=null;
        }

        public static function getUsername(): string
        {
            //return "testusername";
            $token = Auth::getCheckedToken();
            $username = DB::query("SELECT username FROM `account`, `session` WHERE account.userID = `session`.userID AND token = :token;", [':token' => $token]);
            if (count($username) > 0) {
                return $username[0]['username'];
            }
            return "Default";
        }

        public static function userIDExists($userID): bool
        {
            $res = DB::query("SELECT count(*) AS 'Anzahl' FROM users WHERE id = :userid;", [':userid' => $userID]);
            return $res[0]['Anzahl'] > 0;
        }
    }


    class User {
        public string $firstname = "";
        public string $lastname = "";
        public string $salutation = "";
        public string $birthday = "";
        public string $insurance = "";
        public string $patientID = "";

        public function __construct($firstname, $lastname, $salutation, $birthday, $insurance, $patientID)
        {
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->salutation = $salutation;
            $this->birthday = $birthday;
            $this->insurance = $insurance;
            $this->patientID = $patientID;
        }
    }

    class Account {
        public string $userID = "";
        public string $username = "";
        public string $email = "";
        public string $password = "";
        public string $approvalNeeded = "";

        public function __construct($userID, $username, $email, $password, $approvalNeeded)
        {
            $this->userID = $userID;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->approvalNeeded = $approvalNeeded;
        }
    }

?>