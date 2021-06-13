<?php

    class Auth {

        public static function user()
        {
            $token = Auth::getToken();
            if (!isset($token)) { return; }

            $tokenWithUser = DB::table('tokens')->where('token = :token', [':token' => $token])->get([new ForeignDataKey('user_id', 'users', 'id')])[0];
            if (!isset($tokenWithUser)) {
                ErrorUI::error(401, 'Invalid Token');
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
            DB::query("INSERT INTO users (firstname, lastname, salutation, insurance, birthday, patientID) VALUES (:firstname, :lastname, :salutation, :insurance, :birthday, :patientID)", [':firstname' => $user->firstname, ':lastname' => $user->lastname, ':salutation' => $user->salutation, ':insurance' => $user->insurance, ':birthday' => $user->birthday, ':patientID' => $user->patientID]);
            $userID = DB::table('users')->where('firstname = :firstname', [':firstname' => $user->firstname])->get([], ['id'])[0]['id'];
            $code = bin2hex(random_bytes(64));
            DB::query("INSERT INTO notapproved (userID, code, datetime) VALUES (:userID, :code, :date);", [':userID' => $userID, ':code' => $code, ':date' => date('Y-M-D')]);
            $from = "FROM Terminplanung @noreply";
            $subject = "Account bestätigen";
            $msg = $code;
            //mail($account->email, $subject, $msg, $from);
        }

        public static function registerAccount(Account $account)
        {
            if (Auth::userExists($account->userID)) {
                DB::query("INSERT INTO account (userID, username, email, password, createdAt) VALUES (:userID, :username, :email, :password, :createdAt);",[':userID' => $account->userID, ':username' => $account->username, ':email' => $account->email, ':password' => password_hash($account->password, PASSWORD_DEFAULT), ':createdAt' => date('Y-M-D')]);
            }
        }

        public static function approveAccount($token) {
            $userID = DB::table('notapproved')->where('token = :token', [':token' => $token]);
            DB::query("UPDATE account SET erstellungsdatum = " . date('Y-M-D') . " WHERE userID = :userID;", [':userID' => $userID]);
        }

        public static function login($username, $password, bool $remember)
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
            $token = Auth::getToken();
            if (!isset($token)) { return; }
            
            $userID = Auth::userID();
            if (!isset($userID)) { return; }

            $tokenID = DB::table('tokens')->where('token = :token AND user_id = :user_id', [':token' => $token, ':user_id' => $userID])->get([], ['id'])[0]['id'];
            DB::query('UPDATE tokens SET `end` = :end WHERE id = :id', [':id' => $tokenID, ':end' => date('Y-M-D')]);
        }

        private static function isValidToken($token, $exitIfNot)
        {
            $timestamp = DB::table('tokens')->where('token = :token', [':token' => $token])->get([], ['timestamp'])[0]['timestamp'];
            if (!isset($timestamp) && $exitIfNot) {
                ErrorUI::error(401, 'Invalid Token');
                exit;
            }
            $lastLogin = new DateTime($timestamp);
            $currentTime = new DateTime();
            return $lastLogin->diff($currentTime)->d < 10; // only make it valid if token is less than 10 days old
        }

        public static function createNewToken()
        {
            $token = bin2hex(random_bytes(64));
            while (count(DB::table('session')->where('token = :token', [':token' => $token])->get()) > 0) {
                $token = bin2hex(random_bytes(64));
            }
            return $token;
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
            return false;
            $token = "";
            if (isset($_POST['token'])) $token = $_POST['token'];
            else if (isset($_GET['token'])) $token = $_GET['token'];
            else { return false; }

            $isValid = Auth::isValidToken($token, false);
            
            return isset($isValid) && $isValid;
        }

        public static function getUsername(){
            return "testusername";
        }

        public static function userExists($userID)
        {
            $res = DB::query("SELECT count(*) AS 'Anzahl' FROM users WHERE id = :userid;", [':userid' => $userID]);
            return $res[0]['Anzahl'] > 0;
        }
    }


    class User {
        public $firstname = "";
        public $lastname = "";
        public $salutation = "";
        public $birthday = "";
        public $insurance = "";
        public $patientID = "";

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
        public $userID = "";
        public $username = "";
        public $email = "";
        public $password = "";
        public $approvalNeeded = "";

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