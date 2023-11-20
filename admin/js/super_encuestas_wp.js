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
      `<div id="sewp_dinamic_field${asks_fields}" class="input-group mt-2"><input type="text" name="sewp_pregunta[]" id="sewp_pregunta" class="form-control" placeholder="Ingrese pregunta ${asks_fields}"><button type="button" class="btn btn-danger btn-remove" name="remove" id="${asks_fields}">Borrar Pregunta -</span></div>`
    );
  });

  $("#sewp_dinamic_fields").on("click", ".btn-remove", function () {
    var button_field = $(this).attr("id");
    console.log("#sewp_dinamic_field" + button_field);
    $("#sewp_dinamic_field" + button_field).remove();
  });
});
