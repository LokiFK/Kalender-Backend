<?php

    class FormController {

        public function validate(Request $req, Response $res)
        {
            $data = Form::validate($req->getBody(), ['name']);
            $data = Form::validateIsset($data, ['value', 'validation']);
            if (empty($data['value'])) {
                $res->json(
                    [
                        'success' => false,
                        'failedOn' => 'length',
                        'feedback' => 'Bitte wähle einen Wert.',
                        'color' => 'red'
                    ]
                );
                exit;
            }

            $validationKeywords = strpos($data['validation'], ',') !== false ? explode(',', $data['validation']) : [$data['validation']];

            foreach ($validationKeywords as $keyword) {
                if (strpos($keyword, 'unique') !== false) {
                    $dbTableName = str_replace(['unique(', ')'], '', $keyword);
                    $sameValues = DB::query("SELECT * FROM `$dbTableName` WHERE `$data[name]` = :placeholder", [':placeholder' => $data['value']]);
                    if (count($sameValues) > 0 || ($sameValues === false)) {
                        $res->json(
                            [
                                'success' => false,
                                'failedOn' => 'unique',
                                'feedback' => 'Der gewählte Wert existiert bereits. Bitte wähle einen anderen.',
                                'color' => 'red'
                            ]
                        );
                        exit;
                    }
                } else if (strpos($keyword, 'password') !== false) {
                    if (strlen($data['value']) < 8) {
                        $res->json(
                            [
                                'success' => false,
                                'failedOn' => 'password',
                                'feedback' => 'Das Passwort muss mindestens 8 Charaktere beinhalten.',
                                'color' => 'red'
                            ]
                        );
                        exit;
                    }
                }
            }
            $res->json(
                [
                    'success' => true,
                    'feedback' => 'Gut',
                    'color' => 'green'
                ]
            );
        }
    }


?>
