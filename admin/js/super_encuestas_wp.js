jQuery(document).ready(function ($) {
  // ejecutar modal
  $("#sewp_new_survey").click(function () {
    $("#sewpModalNewSurvey").modal("show");
  });

  /**
   * funcion para validar si los campos tienen valores validos antes de agregar otro row
   */
  function validFields() {
    var isValid = true;

    // Busca todos los campos select e input dentro de #sewp_dinamic_fields
    var fields = $("#sewp_dinamic_fields").find("select, input");

    // Filtra los campos que no estén llenos
    var emptyFields = fields.filter(function (v) {
      return (
        $(this).val() === "" ||
        $(this).val() === null ||
        $(this).val() === undefined
      );
    });
    console.log(emptyFields);

    // Si hay campos vacíos, isValid se establece en false
    if (emptyFields.length > 0) {
      isValid = false;
    }

    return isValid;
  }

  // preguntas inicializadas en 1
  var asksFields = 1;
  $("#sewp_add").click(function () {
    if(!validFields()) {
        return
    }
    asksFields++;
    $("#sewp_dinamic_fields").append(
      `<div id="sewp_dinamic_field${asksFields}" class="container mt-2">
            <div class="row">
                <div class="input-group col">
                    <input type="text" name="sewp_pregunta[]" id="sewp_pregunta"
                        class="form-control" placeholder="Ingrese pregunta ${asksFields}">
                </div>
                <div class="input-group col">
                    <select name="sewp_type[]" id="sewp_type" class="form-control sewp_type">
                        <option value="" selected disabled>Selecciona el Típo de Pregunta</option>
                        <option value="1">Selección Simple</option>
                        <option value="2">Selección de Rango</option>
                    </select>
                    <button type="button" class="btn btn-danger btn-remove" name="remove" id="${asksFields}">X</button>
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
