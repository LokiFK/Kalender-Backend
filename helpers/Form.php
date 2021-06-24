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

        public static function require($formData, array $requires): array
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

            $html .= "
                <script>
                    const allInputs = document.getElementsByClassName('form-input');
                    for (var i = 0; i < allInputs.length; i++) {
                        const data = allInputs[i];
                        document.getElementById(allInputs[i].id).addEventListener('input', function() { checkInputField(data) }, false);
                    };

                    function checkInputField(inputField) {
                        $.ajax({
                            type: 'get', 
                            url: '../../../form/validate', 
                            data: JSON.parse(JSON.stringify({
                                'name': inputField.name,
                                'value': document.getElementsByName(inputField.name)[0].value,
                                'validation': document.getElementsByName('validation-' + inputField.name)[0].value
                            })),
                            dataType: 'json',
                            contentType : 'application/json',
                            success: function (data) {
                                document.getElementById('feedback-' + inputField.name).style.color = data.color;
                                document.getElementById('feedback-' + inputField.name).innerHTML = data.feedback;
                            }
                        });
                    }
                </script>
                </form>
            ";

            return $html;
        }
    }

    class FormField {
        private $name;
        private $inputType;
        private $placeholder;
        private $validation;
        private $label;

        public function __construct($name, $label, $inputType, $defaultValue, array $validation)
        {
            $this->name = $name;
            $this->inputType = $inputType;
            $this->defaultValue = $defaultValue;
            $this->validation = $validation;
            $this->label = $label;
        }

        public function getHTML(): string
        {
            $validationString = "";
            for ($i = 0; $i < count($this->validation); $i++) {
                $validationString .= $this->validation[$i];
                if (isset($this->validation[$i+1])) {
                    $validationString .= ",";
                }
            }

            return "
                <input type='hidden' name='validation-$this->name' value=$validationString><br>
                <label for='$this->name'>$this->label</label>
                <input class='form-input' id='$this->name' type='$this->inputType' name='$this->name' placeholder='$this->placeholder'><br>
                <p id='feedback-$this->name'></p>
            ";
        }
    }

?>
