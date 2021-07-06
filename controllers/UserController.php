<?php

    class UserController {

        public function landingPage(Request $req, Response $res) {
            $allAppointmentTypes = ['Sprechstunde', 'Belastungs-EKG', 'HNO-Allgemeinuntersuchung','HNO-Allgemeinuntersuchung', 'Belastungs-EKG', 'Sprechstunde','Sprechstunde','HNO-Allgemeinuntersuchung', 'Belastungs-EKG', 'Sprechstunde', 'Belastungs-EKG', 'HNO-Allgemeinuntersuchung'];
            
            echo $res->view('user/landingPage',
                [],
                [],
                [
                    'appointments' => [
                        [
                            'type' => 'Sprechstunde',
                            'start' => '16.06.2021 17:00',
                            'end' => '16.06.2021 18:00',
                        ],
                        [
                            'type' => 'Belastungs-EKG',
                            'start' => '17.06.2021 17:00',
                            'end' => '17.06.2021 18:00',
                        ],
                        [
                            'type' => 'HNO-Allgemeinuntersuchung',
                            'start' => '17.06.2021 20:00',
                            'end' => '17.06.2021 22:00',
                        ]
                    ],
                    'appointmentTypes' => $allAppointmentTypes
                ]
            );
        }

        public function new(Request $req, Response $res) {
            if (Auth::getStatus() < 1) {
                Path::redirect(Path::ROOT . "auth/login");
            }
            $treatments = DB::table("treatment")->get();
            $view = $res->view('user/new', array(), array(), [
                'appointmentTypes' => $treatments
            ]);
            echo $view;
        }

        public function new2(Request $req, Response $res) {
            if (Auth::getStatus() < 1) {
                Path::redirect(Path::ROOT . "auth/login");
            }
            $data = Form::validateDataType($req->getBody(), ['treatment'=>"existingTreatment"]);
            $treatment = $data['treatment'];
            if (DB::query("SELECT count(*) as Anzahl from treatment where name=:treatment;", [ ":treatment"=>$treatment ])[0]['Anzahl'] != 1) {
                ErrorUI::error(400, 'Treatment not found.');
            }
            
            $allAppointments = DB::query("SELECT a.id, a.day, a.start, a.end, b.name FROM appointment a, treatment b WHERE a.`userID` IS NULL AND ((a.`end` > :end AND a.`day` >= :day) OR (a.`end` <= :end AND a.`day` > :day)) AND a.treatmentID = b.id AND b.name = :name", [':end' => date(DB::TIME), ':day' => date(DB::DATE), ':name' => $treatment]);

            $view = $res->view('user/new2', [ "treatment"=>$treatment ], array(), [ "appointments"=>$allAppointments ]);
            echo $view;
        }

        public function new3(Request $req, Response $res)
        {
            if (Auth::getStatus() < 1) {
                Path::redirect(Path::ROOT . "auth/login");
            }
            Middleware::statusBiggerOrEqualTo(2);
            
            $data = Form::validate($req->getBody(), ['id']);
            
            DB::query("UPDATE `appointment` SET `userID` = :userID WHERE `id` = :id", [':id' => $data['id'], ":userID"=>Auth::getUserID()]);

            Path::redirect(Path::ROOT . "user/appointments/overview");
            
        }

        public function overview(Request $req, Response $res)
        {
            Middleware::statusBiggerOrEqualTo(2);                    

            if($req->getMethod() == "GET"){
                $data = DB::query("SELECT * from appointment, treatment WHERE appointment.treatmentID=treatment.id and appointment.userID=:userID", [":userID"=>Auth::getUser()['id']]);
                $view = $res->view('user/overview', array(), array(), [ "appointments"=>$data ]);
                echo $view;
            } else {

            }
        }

        public function profile(Request $req, Response $res) {
            $userId = Auth::getUserID();
            $user = DB::table('users')->where("id = :id",[':id'=>$userId])->get();
            $account = DB::table('account')->where("userID = :id", [':id'=>$userId])->get();
            if(count($user)>0 && count($account)>0) {
                $user = $user[0];
                $account = $account[0];
            } else {
                $res->errorVisual(500, "Nutzer nicht gefunden");
            }
            $birthday = date('Y-m-d', strtotime($user['birthday']));
            $view = $res->view('user/profile', ['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'salutation' => $user['salutation'], 'insurance' => $user['insurance'], 'birthday' => $birthday, 'email' => $account['email']]);
            echo $view;
        }

        public function customAppointments(Request $req, Response $res) {
            if ($req->getMethod()=="GET") {
                echo $res->view('/user/custom', ['treatment' => $req->getBody()['treatment']]);
            } else {
                $treatment = $req->getBody()['treatment'];
                $translate = array("Monday" => "Montag", "Tuesday" => "Dienstag", "Wednesday" => "Mittwoch", "Thursday" => "Donnerstag", "Friday" => "Freitag", "Saturday" => "Samstag", "Sunday" => "Sonntag");
                $date = date('l', strtotime($req->getBody()['date']));
                $day = strtr($date, $translate);
                $result = DB::query("SELECT * FROM appointment_typical JOIN treatment t on t.id = appointment_typical.treatment WHERE name = :treatment AND day = :day;", [':treatment'=>$treatment, ':day'=>$day]);
                if ($result==null) {
                    $result = DB::query("SELECT * FROM appointment_typical JOIN treatment t on t.id = appointment_typical.treatment WHERE name = :treatment;", [':treatment'=>$treatment]);
                    $otherDays = "";
                    if ($result==null) {
                        ErrorUI::error(404, "Für den Behandlungstypen $treatment gibt es momentan keine Terminmöglichkeiten. Bitte kontaktieren Sie Ihre Praxis.");
                    }
                    foreach ($result as $row) {
                        $otherDays .= $row['day'] . ", ";
                    }
                    ErrorUI::error(404, "Für $day gibt es leider keine Behandlungen des Typs $treatment. An folgenden Tagen gibt es $treatment: $otherDays");
                } else {
                    $duration = DB::query("SELECT * FROM treatment WHERE name = :treatment;", [':treatment'=>$treatment]);
                    $times = [];
                    foreach ($result as $row) {
                        $start = strtotime($row['endTime']);
                        $end = strtotime($duration[0]['duration']);
                        $totaltime = ($end - $start)  ;
                        $hours = intval($totaltime / 3600);
                        $seconds_remain = ($totaltime - ($hours * 3600));
                        $minutes = abs(intval($seconds_remain / 60));
                        array_push($times, "Zeitfenster: " . date('H:i',strtotime($row['startTime'])) . $hours.":".$minutes);
                    }
                    echo $res->view('/user/availableTimeSlots', ['treatment'=>$treatment, 'date'=>$req->getBody()['date'], 'duration'=>$duration[0]['duration']], array(), ['times'=>$times]);
                }
            }
        }

        public function orderCustomTime(Request $req, Response $res) {
            if ($req->getMethod()=="GET") {
//                TODO
            } else {
                $userId = Auth::getUserID();
                $start = $req->getBody()['time'];
                $duration = $req->getBody()['duration'];
                $seconds = strtotime($duration)-strtotime("00:00");
                $end = date('H:i', strtotime($start)+$seconds);
                $day = date('Y-m-d',strtotime($req->getBody()['date']));
                $result = DB::query("SELECT * FROM appointment WHERE day = :day AND ((start<=:end AND end>=:end) OR (start<=:start AND end>=:start)) AND userID IS NOT NULL;", [':day'=>$day, ':end'=>$end, ':start'=>$start]);
                $treatmentId = DB::query("SELECT * FROM treatment WHERE name=:treatment;", [':treatment'=>$req->getBody()['treatment']]);
                $results = DB::query("SELECT * FROM appointment_typical WHERE treatment=:treatment;", [':treatment' => $treatmentId[0]['id']]);
//                echo $duration." ";
//                echo $results[0]['endTime']." ";
                foreach ($results as $r) {
                    $startTime = strtotime($r["startTime"]);
                    $endTime = strtotime($r['endTime']);
    //                    echo date('H:s:i', strtotime($r['endTime']));
                    $sta = $endTime;
                    $en = strtotime($duration);
                    $totaltime = ($en - $sta)  ;
                    $hours = intval($totaltime / 3600);
                    $seconds_remain = ($totaltime - ($hours * 3600));
                    $minutes = intval($seconds_remain / 60);
//                        echo $hours.':'.$minutes;
                    if ((strtotime($end) < $startTime) || strtotime($start) > strtotime(abs($hours).":".abs($minutes))) {
                        ErrorUI::error(404, "Bitte eine Uhrzeit im gültigen Zeitrahmen angeben.");
                    }
                }
                if ($result==null) {
                    DB::query("INSERT INTO appointment(userID, treatmentID, roomID, start, end, status, day) VALUES (:userId, :treatment, '1', :start, :end, 'warten', :day)", ['userId'=>$userId, ':treatment'=>$treatmentId[0]['id'], ':start'=>$start, ':end'=>$end, ':day'=>$day]);
                    ErrorUI::error(1, "Termin wurde erflogreich erstellt.");
                } else {
                    ErrorUI::error(404, "Termin ist bereits belegt.");
                }
            }
        }

    }

?>
