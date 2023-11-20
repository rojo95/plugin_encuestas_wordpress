<?php
global $wpdb;

$table = $wpdb->prefix . 'sewp_survey';
$join_table = $wpdb->prefix . 'sewp_questions';

function generar_valor_aleatorio($longitud)
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

if (isset($_POST["sewp_save_new"])) {
    if (!isset($_POST['sewp_type']) || !isset($_POST['sewp_pregunta']) || !isset($_POST['sewp_pregunta'])) {
        echo "<script>alert('Error al ingresar los datos, recuerde que todos los datos son obligatorios.')</script>";
    } else {

        $asks_array = [];
        foreach ($_POST['sewp_pregunta'] as $key => $value) {
            array_push($asks_array, [$value, $_POST['sewp_type'][$key]]);
        }

        $valor_aleatorio = generar_valor_aleatorio(10);

        $wpdb->query("START TRANSACTION;");
        $insert_survey = $wpdb->insert(
            $table,
            array(
                'name' => $_POST["sewp_name"],
                'short_code' => $valor_aleatorio,
            ),
        );
        $last_id = $wpdb->get_var("SELECT LAST_INSERT_ID() FROM {$table};");
        echo "<br/>";
        if (!$insert_survey) {
            $wpdb->query("ROLLBACK");
            echo "<script>alert('Error al realizar la transacción, no se han podido ingresar los datos.')</script>";
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
                echo "<script>alert('Datos registrados de manera exitosa.')</script>";
                $wpdb->query("COMMIT;");
            } else {
                $wpdb->query("ROLLBACK");
                echo "<script>alert('Error al realizar la transacción, no se han podido ingresar los datos.')</script>";
            }
        }


    }

}


$query = "SELECT a.*, COUNT(b.question_id) as questions FROM {$table} AS a LEFT JOIN {$join_table} AS b ON a.survey_id = b.survey_id GROUP BY a.survey_id;";
$results = $wpdb->get_results($query, ARRAY_A);
?>
<div class="wrap">
    <?php
    echo "<h1 class='wp-heading-inline'>" . get_admin_page_title() . "</h1>";
    ?>
    <a href="#" id="sewp_new_survey" class="page-title-action">Añadir Nueva</a>

    <br><br><br>

    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <th>Nombre de la encuesta</th>
            <th>Cantidad de preguntas</th>
            <th>ShortCode</th>
            <th>Acciones</th>
        </thead>
        <tbody id="sewp_surveys_list">
            <?php
            if (empty($results)) {
                echo "<td colspan='4'>No se ha conseguido registros de encuestas.</td>";
            } else {
                foreach ($results as $result) {
                    echo "
                        <tr>
                            <td>{$result['name']}</td>
                            <td>{$result['questions']}</td>
                            <td>[scapi_shortcode=\"{$result['short_code']}\"]</td>
                            <td>
                                <a href=\"#\" class=\"page-title-action\">Ver</a>
                                <a href=\"#\" class=\"page-title-action\" data-id=\"{$result['survey_id']}\">";
                                if($result['status']){echo "Deshabilitar"; }else {echo "Habilitar";}
                                echo "</a>
                            </td>
                        </tr>
                        ";
                }
            }
            ?>
        </tbody>
    </table>

</div>

<!-- modal -->

<div class="modal modal-lg fade" id="sewpModalNewSurvey" tabindex="-1" aria-labelledby="sewpModalNewSurveyLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="sewpModalNewSurveyLabel">Nueva Encuesta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="sewp_form">
                <div class="modal-body">
                    <p class="error-msg text-danger">Debe completar todos los campos, todos son obligatorios</p>
                    <div class="form-group d-flex">
                        <label for="sewp_name" class="col-sm-5 col-form-label">
                            <h5>
                                Nombre de la Nueva Encuesta
                            </h5>
                        </label>
                        <div class="col-sm-7">
                            <input class="form-control" type="text" name="sewp_name" id="sewp_name" required>
                        </div>
                    </div>
                    <div>
                        <hr>
                        <div class="d-flex w-100 col-sm-12">
                            <div class="col-sm-8 col-md-9">
                                <h5>Preguntas</h5>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm" type="button" name="sewp_add"
                                    id="sewp_add">Agregar Pregunta +</button>
                            </div>
                        </div>
                        <br>
                        <div id="sewp_dinamic_fields">
                            <div id="sewp_dinamic_field" class="container">
                                <div class="row">
                                    <div class="input-group col">
                                        <input type="text" name="sewp_pregunta[]" id="sewp_pregunta"
                                            class="form-control" placeholder="Ingrese pregunta 1" required>
                                    </div>
                                    <div class="input-group col">
                                        <select name="sewp_type[]" id="sewp_type" class="form-control" required>
                                            <option value="" selected disabled>Selecciona el Típo de Pregunta</option>
                                            <option value="1">Selección Simple</option>
                                            <option value="2">Selección de Rango</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <table id="sewp_dinamic_fields">
                            <tr>
                                <td>
                                    <input type="text" name="sewp_pregunta[]" id="sewp_pregunta" class="form-control">
                                </td>
                                <td><button class="btn btn-primary" type="button" name="sewp_add"
                                        id="sewp_add">+</button></td>
                            </tr>
                        </table> -->
                    </div>
                    <p class="error-msg text-danger"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success" name="sewp_save_new" id="sewp_save_new">Guardar
                        Encuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>