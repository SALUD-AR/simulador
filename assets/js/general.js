var loading_fade = false;

// Enviar una post a una pagina sin AJAX
// ejemplos:
// $.form('/new', { title: 'Hello World', body: 'Foo Bar' })
// $.form('/info', { userIds: [1, 2, 3, 4] }, 'GET')
// $.form('/test', { q: '[ajax]' }, 'GET').submit();
jQuery(function($) { 
  $.extend({
    form: function(url, data, method) {
      if (method == null) method = 'POST';
      if (data == null) data = {};

      var form = $('<form>').attr({
        method: method,
        action: url
      }).css({
        display: 'none'
      });

      var addData = function(name, data) {
        if ($.isArray(data)) {
          for (var i = 0; i < data.length; i++) {
            var value = data[i];
            addData(name + '[]', value);
          }
        } 
        else if (typeof data === 'object') {
          for (var key in data) {
            if (data.hasOwnProperty(key)) {
              addData(name + '[' + key + ']', data[key]);
            }
          }
        } 
        else if (data != null) {
          form.append($('<input>').attr({
            type: 'hidden',
            name: String(name),
            value: String(data)
          }));
        }
      };

      for (var key in data) {
        if (data.hasOwnProperty(key)) {
          addData(key, data[key]);
        }
      }

      return form.appendTo('body');
    }
  }); 
});

cargar_pagina = function(url, titulo, params, callback) {
  // console.log("cargar_pagina: url="+url+" titulo="+titulo);
  // if (titulo !== false) {
  //   $(".page-content .page-title").html(titulo);
  // }
  // loading_fade = true;
  setTimeout(function() {
    $(".page-content .page-content-body").load(url, params, callback);
  }, 100);
}

// function adjustModalMaxHeightAndPosition(){
//   $('.modal').each(function(){
//     if($(this).hasClass('in') === false){
//       $(this).show();
//     }
//     var contentHeight = $(window).height() - 60;
//     var headerHeight = $(this).find('.modal-header').outerHeight() || 2;
//     var footerHeight = $(this).find('.modal-footer').outerHeight() || 2;

//     $(this).find('.modal-dialog').addClass('modal-dialog-center').css({
//       'margin-top': function () {
//         return -($(this).outerHeight() / 2);
//       },
//       'margin-left': function () {
//         return -($(this).outerWidth() / 2);
//       }
//     });
//     if($(this).hasClass('in') === false){
//       $(this).hide();
//     }
//   });
// }

function cargar_paciente_url(url, callback) {
  var id_paciente =  $('#paciente-seleccionado').data('id');
  if (parseInt(id_paciente) > 0) {
    var nombre =  $('#paciente-seleccionado').find('span').text();
    cargar_pagina(html_root+url, "<i class='fa fa-user'></i> Paciente: "+nombre, {'id_paciente': id_paciente}, callback);
  }
}

function notify_info(msg) {
  $.notify({
    icon: 'fa fa-info-circle',
    message: msg
  }, 
  {
    type: 'info'
  });
}

function notify_success(msg) {
  $.notify({
    icon: 'fa fa-check-circle',
    message: msg
  }, 
  {
    type: 'success'
  });
}

function notify_error(msg) {
  $.notify({
    icon: 'fa fa-exclamation-circle',
    message: msg
  }, 
  {
    type: 'error'
  });
}

// function nuevo_producto_form_submit(form, url) {
//   loading_fade = false;
//   $.post(url, $(form).serialize(), function(data) {
//     if (data == "OK") {
//       if (typeof(tabla) != 'undefined') {
//         tabla.ajax.reload();
//       }
//       $('#popup_modal').modal('hide');
//       notify_success('Producto agregado');
//     }
//     else {
//       BootstrapDialog.alert(data);
//     }
//   }).fail(function() {
//     BootstrapDialog.alert('Ocurrio un error al guardar los datos!');
//   });
//   $('#popup_modal').modal('hide');
//   return false;
// }

// function ingreso_egreso_producto_form_submit(form, url) {
//   var modo = $(form).find('#producto_form_modo').val();
//   var cantidad_orig = parseInt($(form).find('#producto_form_cantidad_orig').val());
//   var cantidad = parseInt($(form).find('#producto_form_cantidad').val());

//   if (modo != 'ingreso' && modo != 'egreso') {
//     BootstrapDialog.alert('Modo incorrecto!');
//     return false;
//   }

//   if (cantidad <= 0) {
//     BootstrapDialog.alert('Debe ingresar la cantidad!');
//     return false;
//   }

//   if (modo == 'egreso' && cantidad > cantidad_orig) {
//     BootstrapDialog.alert('La cantidad ingresada supera la cantidad disponible del producto!');
//     return false;
//   }

//   loading_fade = false;
//   $.post(url, $(form).serialize(), function(data) {
//     if (data == "OK") {
//       if (typeof(tabla) != 'undefined') {
//         tabla.ajax.reload();
//       }
//       $('.panel .in .medicacion_stock').text(parseInt($(form).find('#producto_form_total').val()));
//       $('#popup_modal').modal('hide');
//       notify_success('Producto actualizado');
//     }
//     else {
//       BootstrapDialog.alert(data);
//     }
//   }).fail(function() {
//     BootstrapDialog.alert('Ocurrio un error al guardar los datos!');
//   });
//   $('#popup_modal').modal('hide');
//   return false;
// }

// function actualizar_cantidad_producto(obj) {
//   if ($("#producto_form_modo").val() == "ingreso") {
//     $("#producto_form_total").val(parseInt($("#producto_form_cantidad_orig").val()) + parseInt($(obj).val()));
//   }
//   else {
//     $("#producto_form_total").val(parseInt($("#producto_form_cantidad_orig").val()) - parseInt($(obj).val()));
//   }
// }

// function alerta_leida_form_submit(form) {

//   var url = $(form).find('#alerta_leida_form_url').val();
//   var id = $(form).find('#alerta_leida_form_id').val();

//   loading_fade = false;
//   $.post(url,
//     { id_alerta: id },
//     function(data) {
//       if (data.trim() == "OK") {
//         $('#popup_modal').modal('hide');
//         notify_success('Alerta marcada como leida');
//         if (typeof(tabla) != 'undefined') {
//           tabla.ajax.reload();
//         }
//       }
//       else {
//         BootstrapDialog.alert(data);
//       }
//     }
//   ).fail(function() {
//       BootstrapDialog.alert('Ocurrio un error al marcar como leida la alerta!');
//   });
//   $('#popup_modal').modal('hide');
//   return false;
// }

function mensaje_leido_form_submit(form) {

  var url = $(form).find('#mensaje_leido_form_url').val();
  var id = $(form).find('#mensaje_leido_form_id').val();

  $.post(url,
    { id_mensaje: id },
    function(data) {
      if (data.trim() == "OK") {
        $('#popup_modal').modal('hide');
        toastr.success('Mensaje marcado como leido');
        if (typeof(tabla) != 'undefined') {
          tabla.ajax.reload();
        }
      }
      else {
        toastr.error(data);
      }
    }
  ).fail(function() {
    toastr.error('Ocurrio un error al marcar como leida la alerta!');
  });
  $('#popup_modal').modal('hide');
  return false;
}

function consultorios_form_submit(form) {
  $.post(url_consultorios_form, $(form).serialize(), function(data) {
    if (data.trim() == "OK") {
      toastr.success("Consultorio guardado correctamente");
      if (typeof tabla_consultorios != 'undefined') {
        tabla_consultorios.ajax.reload();
      }
      $('#popup_modal').modal('hide');
      location.reload();
    }
    else {
      // BootstrapDialog.alert(data);
      toastr.error(data);
    }
  }).fail(function() {
    // BootstrapDialog.alert('Ocurrio un error al guardar los datos!');
    toastr.error('Ocurrio un error al guardar los datos!');
  });
  $('#popup_modal').modal('hide');
  return false;
}

function isDate(txtDate) {
  var currVal = txtDate;
  if(currVal == '')
    return false;

  //Declare Regex 
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  var dtArray = currVal.match(rxDatePattern); // is format OK?
 
  if (dtArray == null)
     return false;
  
  //Checks for dd/mm/yyyy format.
  dtDay   = dtArray[1];
  dtMonth = dtArray[3];
  dtYear  = dtArray[5];
 
  if (dtMonth < 1 || dtMonth > 12)
      return false;
  else if (dtDay < 1 || dtDay> 31)
      return false;
  else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
      return false;
  else if (dtMonth == 2)
  {
     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
     if (dtDay> 29 || (dtDay ==29 && !isleap))
          return false;
  }
  return true;
}

function isDBDate(txtDate) {
  var currVal = txtDate;
  if(currVal == '')
    return false;

  //Declare Regex 
  var rxDatePattern = /^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/;
  var dtArray = currVal.match(rxDatePattern); // is format OK?
 
  if (dtArray == null)
     return false;
  
  //Checks for dd/mm/yyyy format.
  dtYear  = dtArray[1];
  dtMonth = dtArray[3];
  dtDay   = dtArray[5];
 
  if (dtMonth < 1 || dtMonth > 12)
      return false;
  else if (dtDay < 1 || dtDay> 31)
      return false;
  else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
      return false;
  else if (dtMonth == 2)
  {
     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
     if (dtDay> 29 || (dtDay ==29 && !isleap))
          return false;
  }
  return true;
}

function poll_mensajes() {
  var delay = 3000;
  if ($.active == 0) {
    $.ajax({
        loading_indicator: false,
        url: url_mensajes,
        success: function(data) {
          var cantidad = $(data).find("li").length;
          if (cantidad > 0) {
            $('#mensajes-count-header').text(cantidad);
            var plural = (cantidad > 1) ? 's' : '';
            $('#mensajes-count-new').html('Tiene <span class="bold">'+cantidad+' nuevo'+plural+'</span> mensaje'+plural);
          }
          else {
            $('#mensajes-count-header').text('');
            $('#mensajes-count-new').html('No hay mensajes nuevos');
          }
          $('#mensajes-list').html(data);
        }
    }).done(function(data, statusText, jqXHR){
      setTimeout(poll_mensajes, delay);
    });
  }
  else {
    setTimeout(poll_mensajes, delay);
  }
}

function actualizar_reloj(id_turno) {
  var el = $('#timer-turno');
  var turno;
  if (id_turno == 0) {
    turno_activo = id_turno;
    $("#timer-turno").html("");
  }
  else {
    $.ajax({
      type: "POST",
      cache: false,
      url: url_reloj,
      data: { id_turno: turno_activo },
      dataType: "json",
      success: function(res) {
          App.unblockUI(el);
          if (res.error == "" && res.fecha != "" && res.id != "" && res.url != "" && res.paciente != "") {
            $("#timer-turno").countup({start: new Date(res.fecha)});
            turno_activo = res.id;
            $("#timer-turno").closest('.dropdown-timer').find('ul a').each(function(index, el) {
              $(el).off("click").on("click", function(e) {
                e.preventDefault();
                cargar_pagina(res.url);
              });
              $(el).find('.timer-paciente').html(res.paciente);
            });
          }
          else {
            $("#timer-turno").html("");
            turno_activo = 0;
          }
      },
      error: function(xhr, ajaxOptions, thrownError) {
          App.unblockUI(el);
          toastr.error('Ocurrió un error al cargar obtener los datos del reloj!');
      }
    });
  }
}


$(document).ready(function() {

  // $('.switch').not('[data-switch-no-init]').bootstrapSwitch();

  // $('body').on('show.bs.collapse', function(e) {
  //   setTimeout("$.fn.dataTable.tables( { visible: true, api: true } ).columns.adjust()", 100);
  // });

  $.extend( true, $.fn.dataTable.defaults, {
    dom: "<'row'<'col-xs-6'l><'col-xs-6'f>>" +
         "<'row'<'#tabla-extra'>>" +
         "<'row'<'col-sm-12'tr>>" +
         "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    stateSave: true,
    stateDuration: -1,
    pagingType: "full_numbers",
    pageLength: 25,
    lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
    language: {
      emptyTable:     "No se encontraron registros",
      zeroRecords:    "No se encontraron registros",
      info:           "Mostrando desde _START_ a _END_ de _TOTAL_ registros",
      infoEmpty:      "No se encontraron registros",
      infoFiltered:   "(filtrados de un total de _MAX_ registros)",
      lengthMenu:     "Mostrar _MENU_ registros por p&aacute;gina",
      loadingRecords: "Cargando...",
      processing:     "Procesando...",
      search:         "Buscar:",
      paginate: {
        first:        "Primera",
        last:         "&Uacute;ltima",
        next:         "Siguiente",
        previous:     "Anterior"
      },
    }
  });

  // BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DEFAULT] = 'Informaci&oacute;n';
  // BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_INFO] = 'Informaci&oacute;n';
  // BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_PRIMARY] = 'Informaci&oacute;n';
  // BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_SUCCESS] = 'Correcto';
  // BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_WARNING] = 'Atenci&oacute;n';
  // BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DANGER] = 'Atenci&oacute;n';
  // BootstrapDialog.DEFAULT_TEXTS['OK'] = 'Aceptar';
  // BootstrapDialog.DEFAULT_TEXTS['CANCEL'] = 'Cancelar';
  // BootstrapDialog.DEFAULT_TEXTS['CONFIRM'] = 'Confirmaci&oacute;n';

  // $(document).ajaxSend(function(event, request, settings) {
  //   $(".tooltip").hide();
  //   if (loading_fade) {
  //     $(".page-content .page-content-body").fadeOut(250);
  //   }
  //   if (settings.loading_indicator !== false) {
  //     // Pace.restart(); 
  //   }
  // });

  // $(document).ajaxComplete(function(event, request, settings) {
  //   if (loading_fade) {
  //     $(".page-content .page-content-body").fadeIn(250);
  //   }
  //   $('.switch:not(.has-switch)').bootstrapSwitch();
  // });

  $("body").on("click", ".ajax-load", function(e) {
    e.preventDefault();
    e.stopPropagation();
    var url, titulo, params;
    if (typeof($(this).prop('href')) != 'undefined') { 
      url = $(this).prop('href');
    }
    else if (typeof($(this).data('url')) != 'undefined') {
      url = $(this).data('url');
    }
    else {
      return false;
    }

    if (typeof($(this).data('titulo')) != 'undefined') {
      titulo = $(this).data('titulo');
    }
    else {
      // titulo = $(this).html();
      titulo = false;
    }

    if (typeof($(this).data('id')) != 'undefined') {
      params = { id: $(this).data('id') };
    }
    else {
      params = null;
    }

    cargar_pagina(url, titulo, params);
    return false;
  });

  // $("section.sidebar .sidebar-menu li.active a").trigger('click');

  // $('#paciente-seleccionado').on('click', function(e){
  //   e.preventDefault();
  //   cargar_paciente_url('/pacientes/informacion');
  // });

  // $('#paciente-nutricion').on('click', function(e){
  //   e.preventDefault();
  //   cargar_paciente_url('/pacientes/nutricion');
  // });

  // $('#paciente-enfermeria').on('click', function(e){
  //   e.preventDefault();
  //   cargar_paciente_url('/pacientes/enfermeria');
  // });

  // $('#paciente-farmacia').on('click', function(e){
  //   e.preventDefault();
  //   cargar_paciente_url('/pacientes/farmacia', function() { $('#farmacia-calendario').fullCalendar('today')});
  // });

  // $('#paciente-registro-medico').on('click', function(e){
  //   e.preventDefault();
  //   cargar_paciente_url('/pacientes/registro_medico');
  // });

  // $('#paciente-kinesiologia').on('click', function(e){
  //   e.preventDefault();
  //   cargar_paciente_url('/pacientes/kinesiologia');
  // });

  // $(document).tooltip({
  //   selector: "[data-tooltip=true]",
  //   container: "body",
  //   trigger: "hover"
  // });

  // $.notifyDefaults({
  //   type: 'info',
  //   delay: 2000,
  //   newest_on_top: false,
  //   allow_dismiss: false,
  //   offset: {
  //     x: 15,
  //     y: 60
  //   }
  // });

  // $('#popup_modal').on('show.bs.modal', function (event) {
  //   var button = $(event.relatedTarget);
  //   var titulo = button.prop('title');
  //   var url = button.data('url');
  //   var callback = button.data('callback');
  //   var custom_id = button.data('id');

  //   if (titulo == '') {
  //     titulo = button.data('original-title');
  //   }
  //   $(this).find('.modal-title').text(titulo).focus();
  //   $(this).find('.modal-body').html('<h4 class="text-center">Cargando...</h4>');
  //   loading_fade = false;
  //   $.post(url,
  //     {
  //       id_paciente: $('#paciente-seleccionado').data('id'),
  //       id: (custom_id ? custom_id : 0)
  //     },
  //     function(data) {
  //       $('#popup_modal .modal-body').html(data);
  //       if (typeof(callback) != "undefined") {
  //         eval(callback);
  //       }

  //       $('#popup_modal_aceptar').off('click');
  //       var form = $('#popup_modal .modal-body form');
  //       if (form.length == 1) {
  //         $('#popup_modal_aceptar').on('click', function(e) {
  //           e.preventDefault();
  //           $(form).submit();
  //         });
  //       }
  //       else {
  //         $('#popup_modal_aceptar').on('click', function(e) {
  //           e.preventDefault();
  //           $('#popup_modal').modal('hide');
  //         });
  //       }
  //     }
  //   ).fail(function() {
  //       $(this).modal('hide');
  //       BootstrapDialog.alert('Ocurrio un error al cargar la pagina!');
  //     }
  //   );
  // });
  // $('#popup_modal').on('shown.bs.modal', function() {
  //   $('#popup_modal .modal-body').slimScroll({
  //     height: '',
  //     scrollTo: '0',
  //     railVisible: true,
  //     matchParentHeight: true,
  //     // alwaysVisible: true
  //   });
  // });
});