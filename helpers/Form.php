<?php

    class Form {
        public static function validate($formData, array $columns)
        {
            foreach ($columns as $column) {
                if (!isset($formData[$column]) || $formData[$column] == null) {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                }
            }
            return $formData;
        }

        public static function validateNewData($formData, array $columns)
        {
            $data = [];
            $count = 0;
            foreach ($columns as $column) {
                if (!isset($formData[$column]) || $formData[$column] == null) {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                }
                $count ++; 
                $data = array_pad($data, $count, $formData[$column]);
            }
            return $data;
        }

        public static function validateDataType($formData, array $columns, bool $die=true){
            foreach($columns as $key=>$value){
                if(is_int($key)){       //falls key gleich datentyp
                    $key = $value;
                }
                if($value == "canNull"){
                    if (!isset($formData[$key])) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if ($formData[$key] == null || $formData[$key] == "") {
                    ErrorUI::error(400, 'Bad request');
                    exit;
                } else if($value == "int"){
                    if (!is_numeric($formData[$key])) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "datetime"){
                    //todo
                } else if($value == "date"){
                    //todo
                } else if($value == "birhday"){
                    //todo
                } else if($value == "incurance"){
                    if (! ($formData[$key]=="gesetzlich" || $formData[$key]=="privat")) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "role"){
                    if (! ($formData[$key]=="Sekretär" || $formData[$key]=="Arzt" || $formData[$key]=="Arzthelfer")) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "email"){
                    if (!strstr($formData[$key], '@')) {
                        if($die){
                            ErrorUI::error(400, 'Die Email kann nicht richtig sein.');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "newEmail"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM account WHERE email=:email", [":email"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        if($die){
                            ErrorUI::error(400, 'Diese Email wurde bereits registriert.');
                            exit;
                        } else {
                            return null;
                        }
                    }
                    if (!strstr($formData[$key], '@')) {
                        if($die){
                            ErrorUI::error(400, 'Die Email kann nicht richtig sein.');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "existingEmail"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM account WHERE email=:email", [":email"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    }
                } else if($value == "newUsername"){
                    if (Auth::usernameExists($formData[$key])) {
                        if($die){
                            ErrorUI::error(400, 'Username schon vergeben.');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "existingUsername"){
                    if (!Auth::usernameExists($formData[$key])) {
                        if($die){
                            ErrorUI::error(400, 'Username schon vergeben.');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if(substr($value, 0, 9) == "username:"){
                    if (Auth::usernameExists($formData[$key], substr($value,9))) {
                        if($die){
                            ErrorUI::error(400, 'Username schon vergeben.');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "existingRoom"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE number=:number", [":number"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "existingRoomID"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE id=:id", [":id"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "newRoom"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE number=:number", [":number"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        if($die){
                            ErrorUI::error(400, 'Nummer schon vergeben.');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "existingTreatment"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE name=:name", [":name"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "existingTreatmentID"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE id=:id", [":id"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "newTreatment"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE name=:name", [":name"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        if($die){
                            ErrorUI::error(400, 'Name schon vergeben');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "agb"){
                    if ($formData[$key] != 'on') {
                        if($die){
                            ErrorUI::error(400, 'Bitte Datenschutz- und Nutzungsbedingungen akzeptieren.');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                } else if($value == "resetCode"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM passwordreset WHERE code=:code and isUsed=false", [":code"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        if($die){
                            ErrorUI::error(400, 'Bad request');
                            exit;
                        } else {
                            return null;
                        }
                    } 
                }
            }
            return $formData;
        }

        public static function require($formData, array $requires)
        {
            Form::validate($formData, $requires);

            $missingColumns = array();

            foreach ($requires as $key => $value) {
                if (!isset($formData[$key]) || (isset($formData[$key]) && empty($formData[$key]))) {
                    array_push($missingColumns, $key);
                }
            }

            return $missingColumns;
        }
    }

?>