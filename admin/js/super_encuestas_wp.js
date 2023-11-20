jQuery(document).ready(function ($) {
  // ejecutar modal
  $("#sewp_new_survey").click(function () {
    $("#sewpModalNewSurvey").modal("show");
  });

  // preguntas inicializadas en 1
  var asks_fields = 1;
  $("#sewp_add").click(function () {
    asks_fields++;
    $("#sewp_dinamic_fields").append(
      `<div id="sewp_dinamic_field${asks_fields}" class="container mt-2">
            <div class="row">
                <div class="input-group col">
                    <input type="text" name="sewp_pregunta[]" id="sewp_pregunta"
                        class="form-control" placeholder="Ingrese pregunta ${asks_fields}">
                </div>
                <div class="input-group col">
                    <select name="sewp_type[]" id="sewp_type" class="form-control">
                        <option value="" selected disabled>Selecciona el Típo de Pregunta</option>
                        <option value="1">Selección Simple</option>
                        <option value="2">Numérica</option>
                    </select>
                    <button type="button" class="btn btn-danger btn-remove" name="remove" id="${asks_fields}">X</button>
                </div>
            </div>
        </div>`
    );
  });

  $("#sewp_dinamic_fields").on("click", ".btn-remove", function () {
    var button_field = $(this).attr("id");
    console.log("#sewp_dinamic_field" + button_field);
    $("#sewp_dinamic_field" + button_field).remove();
  });
});
