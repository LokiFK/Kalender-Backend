<?php

    class AuthController {
        public function test(Request $req, Response $res)
        {
            echo $res->view('auth/login');
        }

        public function createUser(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/createUser');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['firstname', 'lastname', 'salutation', 'insurance', 'birthday']);
                $code = Auth::registerUser(
                    new User(
                        $validatedData['firstname'],
                        $validatedData['lastname'],
                        $validatedData['salutation'],
                        $validatedData['birthday'],
                        $validatedData['insurance'],
                        ""
                    )
                );
                Path::redirect('../../auth/account/create?code=' . $code);
            }
        }

        public function createAccount(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                $validatedData = Form::validate($req->getBody(), ['code']);
                echo $res->view('auth/createAccount', ['code' => $validatedData['code']]);
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['username', 'email', 'password', 'code']);
                $userID = DB::table('notapproved')->where('`code` = :code', [':code' => $validatedData['code']])->get();

                if (count($userID) > 0) {
                    $code = Auth::registerAccount(
                        new Account(
                            $userID[0]['userID'],
                            $validatedData['username'],
                            $validatedData['email'],
                            $validatedData['password'],
                            false
                        ), true
                    );

                    $link =  $_SERVER['HTTP_HOST'].'/auth/account/approve?code='.$code;
                    echo $link;
                    echo "<a href=../../../auth/account/notApproved>kopiert und normal weiter</a>";
                    
                    /*$from = "FROM Terminplanung @noreply";
                    $subject = "Account bestätigen";
                    $msg = "BlaBlaBla Hier ihr anmelde Link: '.$link;
                    mail($account->email, $subject, $msg, $from);
                    
                    Path::redirect('../../../auth/account/emailApproved');
                    */
                }
            }

            /*
            $from = "FROM Terminplanung @noreply";
                    $subject = "Account bestätigen";
                    $msg = $code;
            //mail($account->email, $subject, $msg, $from);
            */
        }
        public function approve(Request $req, Response $res){
            $data = Form::validate($req->getBody(), ['code']);
            if(Auth::approveAccount($data['code'])){
                echo 'account bestätigt';                               //todo
            } else {
                echo 'Tut uns leid es liegt ein Fehler vor.';
            }
        }
        public function notApproved(Request $req, Response $res){
            $token = Auth::getTokenWithUnapprovedUsers();
            if($token == null){
                echo "bad request";
                exit();
            }
            echo "todo: email ändern, neue schicken";
        }

        public function login(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/login');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['username', 'password']);
                $remember = false;
                if(isset($req->getBody()['remember']) && $req->getBody()['remember'] == 'on'){
                    $remember = true;
                }
                $token = Auth::login($validatedData['username'], $validatedData['password'], $remember);
                //echo $token;
                if($token!=null){
                    setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');

                    $res = DB::query("select count(*) as Anzahl from session, account where session.userID = account.userID and token = :token and createdAt is not null", [":token"=>$token]);
                    if($res[0]['Anzahl']==1){   //approved
                        Path::redirect('../../../');
                    } else {
                        Path::redirect('../../../auth/account/emailApproved');
                    }
                } else {
                    echo "wrong username or password";      //todo
                    echo $res->view('auth/login');
                }
            }
        }
        public function logout(Request $req, Response $res){
            if(Auth::logout()!=true){

            }
            Path::redirect('../../../');
        }

        public function permissions(Request $req, Response $res)
        {
            Middleware::auth();

            $userID = Auth::userID();
            
            $adminRelation = DB::table('admin')->where('userID = :userID', [':userID' => $userID])->get();
            return count($adminRelation) > 0
                ? $res->json(array('role' => 'admin'))
                : $res->json(array('role' => 'account'));
        }
    }

?>
