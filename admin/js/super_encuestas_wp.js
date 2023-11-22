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

    // Si hay campos vacíos, isValid se establece en false
    if (emptyFields.length > 0) {
      isValid = false;
    }

    return isValid;
  }

  // preguntas inicializadas en 1
  var asksFields = 1;
  $("#sewp_add").click(function () {
    if (!validFields()) {
      return;
    }
    asksFields++;
    $("#sewp_dinamic_fields").append(
      `<div id="sewp_dinamic_field${asksFields}" class="container mt-2">
            <div class="row">
                <div class="input-group col">
                    <input type="text" name="sewp_pregunta" id="sewp_pregunta"
                        class="form-control sewp_pregunta" placeholder="Ingrese pregunta ${asksFields}" required>
                </div>
                <div class="input-group col">
                    <select name="sewp_type" id="sewp_type" class="form-control sewp_type" required>
                        <option value="" selected disabled>Selecciona el Típo de Pregunta</option>
                        <option value="1">Selección Simple</option>
                        <option value="2">Selección de Rango</option>
                        <option value="3">Respuesta Breve</option>
                      </select>
                    <button type="button" class="btn btn-danger btn-remove" name="remove" id="${asksFields}">X</button>
                </div>
            </div>
        </div>`
    );
  });

  $("#sewp_dinamic_fields").on("click", ".btn-remove", function () {
    var button_field = $(this).attr("id");
    $("#sewp_dinamic_field" + button_field).remove();
  });

  /**
   *
   * @param {*} data
   * @returns
   * funcion para transformar los datos serializados a un formato objeto
   */
  function decodeFormData(data) {
    var pairs = data.split("&");

    var result = {};

    pairs.forEach(function (pair) {
      pair = pair.split("=");

      let foundEntry = Object.entries(result).find(([key]) => key === pair[0]);
      if (foundEntry) {
        // Si ya existe la clave, añadir el valor actual a la matriz existente
        if (!Array.isArray(result[pair[0]])) {
          result[pair[0]] = [result[pair[0]]];
        }
        result[pair[0]].push(decodeURIComponent(pair[1] || ""));
      } else {
        // Si no existe la clave, crear un nuevo array con el valor actual
        result[pair[0]] = decodeURIComponent(pair[1] || "");
      }
    });

    return JSON.parse(JSON.stringify(result));
  }

  $("#sewp_save_new").click(function (e) {
    e.preventDefault(); // Evita que el formulario se envíe de la manera predeterminada
    if (!validFields() || $("#sewp_name").val() === "") {
      $("p.error-msg").html(
        "Debe completar todos los campos, todos son obligatorios."
      );
      return;
    }
    $("p.error-msg").html("");

    const decodedData = decodeFormData($(this).closest("form").serialize());
    console.log("decodedData.sewp_pregunta: ", decodedData.sewp_pregunta);

    const asks_array = [];
    const sewp_pregunta = decodedData.sewp_pregunta.isArray
      ? decodedData.sewp_pregunta
      : [decodedData.sewp_pregunta];
    const sewp_type = decodedData.sewp_type.isArray
      ? decodedData.sewp_type
      : [decodedData.sewp_type];
    sewp_pregunta.forEach((value, key) => {
      asks_array.push([value, decodedData.sewp_type[key]]);
    });

    const data = {
      action: "saveSurvey",
      nonce: SolicitudesAjax.seguridad,
      name: decodedData.sewp_name,
      asks_array: asks_array,
    };

    const url = SolicitudesAjax.url;
    console.log("url: ", url);
    $.ajax({
      type: "POST",
      url: url,
      data: data,
      success: function (response) {
        const res = parseInt(response);
        // Aquí va el código que se ejecutará si la solicitud se completa con éxito
        if (res === 0) {
          alert(
            "Error al realizar la transacción, no se han podido ingresar los datos"
          );
        } else {
          alert("Datos registrados de manera exitosa");
          location.reload();
        }
      },
      error: function (xhr, status, error) {
        // Aquí va el código que se ejecutará si ocurre un error
        console.log("Error: ", error);
      },
    });
  });

  $(document).on("click", "a[data-id]", function () {
    var id = this.dataset.id;
    const res = window.confirm(
      "¿Está seguro que desea habilitar/deshabilitar ésta encuesta?"
    );

    if (res) {
      var url = SolicitudesAjax.url;
      $.ajax({
        type: "POST",
        url: url,
        data: {
          action: "deleteSurvey",
          nonce: SolicitudesAjax.seguridad,
          id: id,
        },
        success: function (res) {
          alert("Acción Realizada.");
          location.reload();
        },
      });
    }
  });
});
