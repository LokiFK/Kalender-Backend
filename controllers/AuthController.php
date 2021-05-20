<?php


    class AuthController {

        public function login()
        {
            $user_id = Auth::userID();
            $allUsers = DB::db()->query('SELECT * FROM users WHERE id = :id', array(':id' => $user_id));
            echo json_encode($allUsers);
        }

        public function logout()
        {
            print_r(Auth::userID());
        }
    }

?>