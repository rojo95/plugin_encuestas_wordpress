<?php
class ShortcodeClass
{
    function GetSurvey($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'sewp_survey';
        $table_join = $wpdb->prefix . 'sewp_questions';
        $query = "SELECT * FROM {$table} AS a INNER JOIN {$table_join} AS b ON a.survey_id=b.survey_id WHERE short_code  = \"{$id}\";";
        $datos = $wpdb->get_results($query, ARRAY_A);
        if (empty($datos)) {
            $datos = [];
        }
        return $datos;
    }

    // function GetQuestions($id)
    // {
    //     global $wpdb;
    //     $table = $wpdb->prefix . 'sewp_questions';
    //     $query = "SELECT * FROM {$table} WHERE survey_id = {$id};";
    //     $datos = $wpdb->get_results($query, ARRAY_A);
    //     if (empty($datos)) {
    //         $datos = [];
    //     }
    //     return $datos[0];
    // }

    function OpenForm($titulo)
    {
        $html = "
            <div class='wrap'>
                <h4> $titulo</h4>
                <form method='POST'>

        ";

        return $html;
    }

    public function CloseForm()
    {
        $html = "
              <br>
                 <input type='submit' id='btnguardar' name='btnguardar' class='page-title-action' value='enviar'>
            </form>
          </div>  
        ";

        return $html;
    }

    function FormInput($id, $ask, $type)
    {
        $html = "";
        if ($type == 1) {
            $html = "select";
            $html = "
                <diV class='from-group'>
                    <p><b>$ask</b></p>
                    <div class='col-sm-8'>  
                        <select class='from-control' id='$id' name='$id'>
                            <option value='1'>SI</option>
                            <option value='0'>NO</option>
                        </select>
                    </div>
                </div>
            ";
        } elseif ($type == 2) {
            $html = "rango";
        } else {
            $html = "<diV class='from-group'>
                <p><b>$ask</b></p>
                <div class='col-sm-8'>  
                    <input type='text' class='from-control' id='$id' name='$id' />
                </div>
            </div>";
        }
        return $html;
    }

    function FormCreator($id)
    {
        //obtener todas las preguntas
        $enc = $this->GetSurvey($id);
        $nombre = $enc[0]['name'];
        $preguntas = "";
        foreach ($enc as $key => $value) {
            $detalleid = $value['question_id'];
            $pregunta = $value['ask'];
            $tipo = $value['type_ask'];
            $encid = $value['survey_id'];
            $short_code = $value['short_code'];

            if ($short_code == $id) {
                $preguntas .= $this->FormInput($detalleid, $pregunta, $tipo);
            }
        }

        $html = $this->OpenForm($nombre);
        $html .= $preguntas;
        $html .= $this->CloseForm();

        return $html;

    }


    function GuardarDetalle($datos)
    {
        global $wpdb;
        $tabla = "{$wpdb->prefix}encuestas_respuesta";
        return $wpdb->insert($tabla, $datos);
    }


}
?>