jQuery(document).ready(function ($) {
  // ejecutar modal
  $("#sewp_new_survey").click(() => {
    $("#sewpModalNewSurvey").modal("show");
  });

  // preguntas inicializadas en 1
  var asks_fields = 1;
  $("#sewp_add").click(() => {
    asks_fields++;
    $("#sewp_dinamic_fields").append(
      `<div id="sewp_dinamic_field${i}" class="mt-2"><input type="text" name="sewp_pregunta[]" id="sewp_pregunta" class="form-control"></div>`
    );
  });
});
