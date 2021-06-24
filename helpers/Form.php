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

        public static function validateIsset($formData, array $columns)
        {
            foreach ($columns as $column) {
                if (!isset($formData[$column])) {
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
                if (is_int($key)) {
                    $key = $value;
                }
                print_r($formData);
                if ($value == "canNull") {
                    if (!isset($formData[$key])) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if (!isset($formData[$key]) || $formData[$key] == null || $formData[$key] == "") {
                    if($die){
                        ErrorUI::error(400, 'Bad request');
                        exit;
                    } else {
                        return null;
                    }
                } else if($value == "int"){
                    if (!is_numeric($formData[$key])) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "datetime"){
                    //todo
                } else if($value == "date"){
                    //todo
                } else if($value == "birhday"){
                    //todo
                } else if($value == "incurance"){
                    if (! ($formData[$key]=="gesetzlich" || $formData[$key]=="privat")) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "role"){
                    if (! ($formData[$key]=="Sekretär" || $formData[$key]=="Arzt" || $formData[$key]=="Arzthelfer")) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "email"){
                    if (!strstr($formData[$key], '@')) {
                        self::errorDataType('Die Email kann nicht richtig sein.', $die);
                    } 
                } else if($value == "newEmail"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM account WHERE email=:email", [":email"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        self::errorDataType('Diese Email wurde bereits registriert.', $die);
                    }
                    if (!strstr($formData[$key], '@')) {
                        self::errorDataType('Die Email kann nicht richtig sein.', $die);
                    } 
                } else if($value == "existingEmail"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM account WHERE email=:email", [":email"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        self::errorDataType('Bad request', $die);
                    }
                } else if($value == "newUsername"){
                    if (Auth::usernameExists($formData[$key])) {
                        self::errorDataType('Username schon vergeben.', $die);
                    } 
                } else if($value == "existingUsername"){
                    if (!Auth::usernameExists($formData[$key])) {
                        self::errorDataType('Username schon vergeben.', $die);
                    } 
                } else if(substr($value, 0, 9) == "username:"){
                    if (Auth::usernameExists($formData[$key], substr($value,9))) {
                        self::errorDataType('Username schon vergeben.', $die);
                    } 
                } else if($value == "existingRoom"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE number=:number", [":number"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "existingRoomID"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE id=:id", [":id"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "newRoom"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM room WHERE number=:number", [":number"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        self::errorDataType('Nummer schon vergeben.', $die);
                    } 
                } else if($value == "existingTreatment"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE name=:name", [":name"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "existingTreatmentID"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE id=:id", [":id"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        self::errorDataType('Bad request', $die);
                    } 
                } else if($value == "newTreatment"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM treatment WHERE name=:name", [":name"=>$formData[$key]]);
                    if ($res[0]['Anzahl']>0) {
                        self::errorDataType('Name schon vergeben', $die);
                    } 
                } else if($value == "agb"){
                    if ($formData[$key] != 'on') {
                        self::errorDataType('Bitte Datenschutz- und Nutzungsbedingungen akzeptieren.', $die);
                    } 
                } else if($value == "resetCode"){
                    $res = DB::query("SELECT count(*) AS Anzahl FROM passwordreset WHERE code=:code and isUsed=false", [":code"=>$formData[$key]]);
                    if ($res[0]['Anzahl']==0) {
                        self::errorDataType('Bad request', $die);
                    } 
                }
            }
            return $formData;
        }

        private static function errorDataType($msg, $die)
        {
            if ($die) {
                ErrorUI::error(400, $msg);
                exit;
            }
            return null;
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

        public static function create($redirect, $method, FormField ...$formFields): string
        {
            $html = "
                <form action='$redirect' method='$method'>
            ";

            foreach ($formFields as $formField) {
                $html .= $formField->getHTML();
            }

            $html .= "</form>";

            return $html;
        }
    }

    class FormField {
        private $name;
        private $inputType;
        private $placeholder;
        private $validation;
        private $label;
        private $options;

        public function __construct($name, $label, $inputType, $defaultValue="", array $validation=array(), array $options=array())
        {
            $this->name = $name;
            $this->inputType = $inputType;
            $this->defaultValue = $defaultValue;
            $this->validation = $validation;
            $this->label = $label;
            if ($inputType == "select") {
                $this->options = $options;
            }
        }

        public function getHTML(): string
        {
            $returnVal = "";
            $validationString = "";
            for ($i = 0; $i < count($this->validation); $i++) {
                $validationString .= $this->validation[$i];
                if (isset($this->validation[$i+1])) {
                    $validationString .= ",";
                }
            }

            $returnVal .= "
                <input type='hidden' name='$this->name' id='validation-$this->name' value=$validationString><br>
            ";

            if ($this->inputType == "select") {
                $returnVal .= "
                    <label for='select-$this->name'>$this->label</label>
                    <select class='form-input' id='select-$this->name' name='$this->name'>
                ";

                for ($i = 0; $i < count($this->options); $i++) {
                    if ($i == 0) {
                        $returnVal .= "<option value='" . $this->options[$i] . "'>" . $this->options[$i] . "</option>";
                        continue;
                    }
                    $returnVal .= "<option value='" . $this->options[$i] . "'>" . $this->options[$i] . "</option>";
                }
                
                $returnVal .= "
                        <option value='sonstiges' id='sonstiges'>Sonstiges</option>
                    </select>
                    <input class='form-input' id='sonstigesFeld-$this->name' name='$this->name' style='display: none;' type='text'></input> <br>
                ";
            } else {
                if ($this->inputType == "hidden") {
                    $returnVal .= "
                        <input type='hidden' name='$this->name' value='$this->label'><br>
                    ";

                } else if ($this->inputType == "") {
                    $returnVal .= "
                        <input type='submit' name='$this->name' value='$this->label'>
                    ";
                } else {
                    $returnVal .= "
                        <label for='$this->name'>$this->label</label>
                        <input class='form-input' id='$this->name' type='$this->inputType' name='$this->name' placeholder='$this->placeholder'><br>
                    ";
                }
            }

            $returnVal .= "
                <p id='feedback-$this->name'></p>
            ";

            return $returnVal;
        }
    }

?>
