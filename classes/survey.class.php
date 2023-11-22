<?php
final class survey
{

    private function generar_valor_aleatorio($longitud)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'sewp_survey';
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $campos_generados_query = "SELECT * FROM {$table};";
        $campos_generados = $wpdb->get_results($campos_generados_query, ARRAY_A);
        do {
            $valor_aleatorio = '';
            for ($i = 0; $i < $longitud; $i++) {
                $valor_aleatorio .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }
        } while (in_array($valor_aleatorio, $campos_generados));

        $campos_generados[] = $valor_aleatorio;

        return $valor_aleatorio;
    }
    function CreateNewSurvey(string $name, array $asks_array)
    {
        if (isset($name) && count($asks_array) > 0) {

            global $wpdb;
            $table = $wpdb->prefix . 'sewp_survey';
            $join_table = $wpdb->prefix . 'sewp_questions';

            $valor_aleatorio = $this->generar_valor_aleatorio(10);

            
            $wpdb->query("START TRANSACTION;");
            $insert_survey = $wpdb->insert(
                $table,
                [
                    'name' => $name,
                    'short_code' => $valor_aleatorio,
                ],
            );
            $last_id = $wpdb->get_var("SELECT LAST_INSERT_ID() FROM {$table};");
            if (!$insert_survey) {
                $wpdb->query("ROLLBACK");
                echo 0;
            } else {
                foreach ($asks_array as $values) {
                    $data = [
                        'survey_id' => $last_id,
                        'ask' => $values[0],
                        'type_ask' => $values[1],
                    ];
                    $respuesta = $wpdb->insert(
                        $join_table,
                        $data,
                    );
                }

                $last_id2 = $wpdb->get_var("SELECT LAST_INSERT_ID() FROM {$join_table};");
                if ($last_id2) {
                    echo 1;
                    $wpdb->query("COMMIT;");
                } else {
                    $wpdb->query("ROLLBACK");
                    echo 0;
                }
            }

        }
        return;

    }
}

?>