var CalendarioTurnos = function() {
  var agendas_disponibles = '';
  var agenda_dias_sem = [ "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom" ];
  var selector = null;
  var url_turnos = null;
  var url_agendas = null;

  var cargar_turnos = function(start, end, timezone, callback) {
    $.ajax({
      url: url_turnos,
      type: 'POST',
      data: {
        start: start.format("YYYY-MM-DD"),
        end: end.format("YYYY-MM-DD")
      },
      dataType: 'json',
      success: function(datos) {
          var events = [];
          $.each( datos, function( key, val ) {
            events.push({
              id: val.id,
              title: val.title,
              start: val.start,
              end: val.end,
              // color: get_event_color(val.id_agenda),
              id_agenda: val.id_agenda,
              estado: val.estado,
              tipo_consulta: val.tipo_consulta,
              obra_social: val.obra_social
            });
          });
          callback(events);
      }
    });
  }

  var cargar_agendas = function(start, end, timezone, callback) {
    $.post(url_agendas, function(data) {
      agendas_disponibles = data;
    }).fail(function() {
      agendas_disponibles = 'Ocurrio un error al cargar los datos de las agendas!';
      toastr.error(agendas_disponibles);
    }).always(function() {
      var events = [];
      var test_date, loop, pos, agendas, agenda_dias, agenda_hora_inicio, agenda_hora_fin;
      for (loop = start.unix(); loop <= end.unix(); loop = loop + (24 * 60 * 60)) {
        test_date = moment.unix(loop);
        pos = agenda_dias_sem[test_date.isoWeekday()];
        agendas = $(agendas_disponibles).find('.agenda-datos').addBack('.agenda-datos');
        $(agendas).each(function(index) {
          agenda_id = $(this).prop('id').substring(7);
          agenda_dias = $(this).find('.agenda-dias').text().split(', ');
          agenda_hora_inicio = $(this).find('.agenda-hora-inicio').text();
          agenda_hora_fin = $(this).find('.agenda-hora-fin').text();
          agenda_duracion = $(this).find('.agenda-duracion').text();
          if ($.inArray(pos, agenda_dias) != -1) {
            events.push({
              title: '',
              start: test_date.format('YYYY-MM-DD ') + agenda_hora_inicio,
              end: test_date.format('YYYY-MM-DD ') + agenda_hora_fin,
              duracion: agenda_duracion,
              id_agenda: agenda_id,
              rendering: 'background'
            });
          }
        });
      }
      callback( events );
    });
  }

  var initCalendar = function(options) {
    var h = {};

    if ($(selector).width() <= 400) {
      $(selector).addClass("mobile");
      h = {
        left: 'title prev, next',
        center: '',
        right: 'today,agendaWeek,agendaDay'
      };
    } else {
      $(selector).removeClass("mobile");
      h = {
        left: 'title',
        center: '',
        right: 'prev,next today,agendaWeek,agendaDay'
      };
    }

    var default_options = {
      disableDragging: false,
      header: h,
      editable: false,
      defaultView: 'agendaDay',
      lang: 'es',
      allDaySlot: false,
      eventSources: [
        { events: cargar_agendas },
        { events: cargar_turnos }
      ],
      titleRangeSeparator: ' al ',
      views: {
        week: {
          titleFormat: "LL"
        }
      },
      lazyFetching: false,
      axisFormat: 'HH:mm',
      timeFormat: 'HH:mm',
      slotDuration: '00:15:00',
      scrollTime: '07:00:00',
      displayEventEnd: false,
      editable: false,
      aspectRatio: 1.4,
      eventDurationEditable: false,
      droppable: false,
      eventRender: function(event, element, view){
        $(element).data(event);
      },
      eventAfterRender: function (event, element) {
        if (!event.rendering) {
          $(element).find('.fc-title').parent().append('<div class="fc-extra-data">Consulta: '+event.tipo_consulta+'</div>');
          $(element).tooltip({
            title: '<b>' + event.title + '</b><br/><b>Consulta:</b> ' + event.tipo_consulta + '<br/><b>OS:</b> ' + event.obra_social,
            trigger: 'hover', 
            html: true, 
            container: "body"
          });
        }
      },
    };

    if (options) {
      options = $.extend( {}, default_options, options );
    }
    else {
      options = default_options;
    }

    $(selector).fullCalendar('destroy');
    $(selector).fullCalendar(options);
  }

  return {
    init: function(options) {
      if (typeof options == 'undefined') {
        console.log('Error: Falta definir las opciones!');
        return;
      }
      if (typeof options.selector == 'undefined') {
        console.log('Error: Falta definir el selector!');
        return;
      }
      if (typeof options.url_turnos == 'undefined') {
        console.log('Error: Falta definir url_turnos!');
        return;
      }
      if (typeof options.url_agendas == 'undefined') {
        console.log('Error: Falta definir url_agendas!');
        return;
      }
      if (typeof options.fullcalendar == 'undefined') {
        fullcalendar_options = null;
      }
      else {
        fullcalendar_options = options.fullcalendar;
      }
      selector = options.selector;
      url_turnos = options.url_turnos;
      url_agendas = options.url_agendas;
      initCalendar(fullcalendar_options);
    },
    agendas_disponibles: function() {
      return agendas_disponibles;
    },
    recargar: function() {
      if (selector) {
        $(selector).fullCalendar('refetchEvents');
      }
      else {
        console.log('Error: No se ha inicializado el calendario!');
      }
    }
  }
}();
