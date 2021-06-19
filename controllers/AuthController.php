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
                $validatedData = Form::validate($req->getBody(), ['firstname', 'lastname', 'salutation', 'insurance', 'birthday', 'username', 'email', 'password', 'agb']);
                $id = Auth::registerUser(
                    new User(
                        $validatedData['firstname'],
                        $validatedData['lastname'],
                        $validatedData['salutation'],
                        $validatedData['birthday'],
                        $validatedData['insurance'],
                        ""
                    )
                );
                $code = Auth::registerAccount(
                    new Account(
                        $id,
                        $validatedData['username'],
                        $validatedData['email'],
                        $validatedData['password'],
                        false
                    ), true
                );

                $token = Auth::specialLogin($id, false);
                if($token!=null){
                    setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');
                } else {
                    echo "Fehler";      //todo
                }

                $link =  $_SERVER['HTTP_HOST'].'/auth/account/approve?code='.$code;
                echo "mail: <a href=\"$link\">".$link."</a><br>";
                echo "<a href=../../../auth/account/notApproved>automatische Weiterleitung</a>";
                    
                /*$from = "FROM Terminplanung @noreply";
                $subject = "Account bestätigen";
                $msg = "BlaBlaBla Hier ihr anmelde Link: '.$link;
                mail($account->email, $subject, $msg, $from);
                    
                Path::redirect('../../../auth/account/emailApproved');
                */
            }
        }

        public function approve(Request $req, Response $res){
            $data = Form::validate($req->getBody(), ['code']);
            $id = Auth::approveAccount($data['code']);
            if($id!=null){
                $token = Auth::specialLogin($id, false);
                setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');
                Auth::start();
                echo $res->view("auth/approved");                               //todo
            } else {
                ErrorUI::errorMsg(500, 'bad request');
                exit();
            }
        }
        public function notApproved(Request $req, Response $res){
            if(Auth::getStatus() == 1){
                if($req->getMethod() == "GET"){
                    echo $res->view("auth/notApproved");
                } else if ($req->getMethod() == "POST") {
                    if(isset($req->getBody()['email'])){
                        DB::query("UPDATE account SET email = :email WHERE userID = :userID", [ 'email'=>$req->getBody()['email'], 'userID'=>Auth::getUser()['id'] ]);
                    }
                    $code = Auth::createNewCode(Auth::getUser()['id']);

                    $link =  $_SERVER['HTTP_HOST'].'/auth/account/approve?code='.$code;
                    echo "mail: <a href=\"$link\">".$link."</a><br>";
                    echo "<a href=../../../auth/account/notApproved>automatische Rückleitung</a>";
                    
                    /*$from = "FROM Terminplanung @noreply";
                    $subject = "Account bestätigen";
                    $msg = "BlaBlaBla Hier ihr anmelde Link: '.$link;
                    mail($account->email, $subject, $msg, $from);
                    
                    Path::redirect('../../../auth/account/emailApproved');
                    */
                }    
            } else {
                ErrorUI::errorMsg(500, 'bad request');
                exit();
            }
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
                        Path::redirect('../../../auth/account/notApproved');
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
