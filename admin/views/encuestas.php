<?php
global $wpdb;
$table = $wpdb->prefix . 'sewp_survey';
$join_table = $wpdb->prefix . 'sewp_questions';
$query = "SELECT a.*, COUNT(b.question_id) as questions FROM {$table} AS a INNER JOIN {$join_table} AS b ON a.survey_id = b.survey_id GROUP BY a.survey_id;";
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
                            <td>{$result['name']}</td>
                            <td>{$result['questions']}</td>
                            <td>[scapi_shortcode=\"{$result['short_code']}\"]</td>
                            <td>
                                <a href=\"#\" class=\"page-title-action\">Ver</a>
                                <a href=\"#\" class=\"page-title-action\">Desactivar</a>
                                <a href=\"#\" class=\"page-title-action\">Borrar</a>
                            </td>
                        ";
                }
            }
            ?>
        </tbody>
    </table>

</div>

<!-- modal -->

<div class="modal modal-lg fade" id="sewpModalNewSurvey" tabindex="-1"
    aria-labelledby="sewpModalNewSurveyLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="sewpModalNewSurveyLabel">Nueva Encuesta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="POST">
                <div class="modal-body">
                    <div class="form-group d-flex">
                        <label for="name" class="col-sm-5 col-form-label">
                            <h5>
                                Nombre de la Nueva Encuesta
                            </h5>
                        </label>
                        <div class="col-sm-7">
                            <input class="form-control" type="text" name="sewp_name" id="sewp_name">
                        </div>
                    </div>
                    <div>
                        <br>
                        <div class="d-flex w-100 col-sm-12">
                            <div class="col-sm-8 col-md-9">
                                <h5>Preguntas</h5>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm" type="button" name="sewp_add" id="sewp_add">Agregar Pregunta +</button>
                            </div>
                        </div>
                        <br>
                        <div id="sewp_dinamic_fields">
                            <div id="sewp_dinamic_field" class="input-group">
                                <input type="text" name="sewp_pregunta[]" id="sewp_pregunta" class="form-control" placeholder="Ingrese pregunta 1">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success">Guardar Encuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>