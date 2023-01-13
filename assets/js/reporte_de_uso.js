jQuery(document).ready(function ($) {
    var fecha = new Date();
    var dia = 24 * 60 * 60 * 1000;
    var primer_dia = new Date(fecha.getTime() - (dia * (fecha.getDay() - 1)));
    var siete_dias = dia * 4;
    var ultimo_dia = new Date(primer_dia.getTime() + siete_dias);
    fecha_inicio = primer_dia.toISOString().slice(0, 10);
    fecha_fin = ultimo_dia.toISOString().slice(0, 10);
    document.getElementById("fecha_inicio").value = fecha_inicio;
    document.getElementById("fecha_fin").value = fecha_fin;
    obtenerReporteResultados();
    // obtenerPostMasComentados();

});

// const config = {
//     type: 'line',
//     data: [],
//     options: {
//         responsive: true,
//         plugins: {
//             legend: {
//                 position: 'top',
//             },
//             title: {
//                 display: true,
//                 text: ''
//             }
//         }
//     },
// };

var usuarios_biblioteca,
    elementos_biblioteca,
    usuarios_podcast,
    elementos_podcast,
    fecha_inicio,
    fecha_fin,
    nombre_usuario,
    grupos,
    id_grupo,
    elementos_wall,
    usuarios_wall,
    usuarios,
    usuarios_ranking,
    usuarios_cuestionarios,
    usuarios_feedback,
    usuarios_juegos,
    usuarios_comunidad,
    reporte,
    reporte_biblioteca,
    cuestionarios,
    juegos_productos,
    juegos_ruleta,
    juegos_serpientes_y_escaleras,
    juegos_profiler,
    juegos_run_pancho_run,
    juegos_retos,
    topics,
    usuarios_originales,
    usuarios_biblioteca_originales,
    retroalimentacion_total,
    post_mas_usados,
    invitados_comunidad,
    categorias_cuestionarios,
    podcast_mas_usados,
    grafica_library,
    reporte_ambiente_semanal,
    reporte_ambiente_mes,
    juegos,
    usuarios_totales,
    usuarios_nuevos,
    usuarios_regreso,
    sesiones_activas,
    sesiones_promedio,
    duracion_sesion,
    grafica_sesiones_activas,
    grafica_sesiones_usuarios,
    grafica_sesiones_duracion;

function obtenerReporteResultados() {
    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined
    id_grupo = id_grupo || undefined
    $.ajax({
        url: window.base_url + "Reporte_resultados/ReporteDeResultados",
        type: 'POST',
        data: {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            nombre_usuario: nombre_usuario,
            group_id: id_grupo
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
            document.getElementById("loader_background").style.display = 'none';
            document.getElementById("loader").style.display = 'none';
        },
        success: function (json) {
            reporte = json.data;
            document.getElementById("loader_background").style.display = 'none';
            document.getElementById("loader").style.display = 'none';
            asignarVariables(json.data);
            generarTablas();
            obtenerReporteAmbienteLaboralMes()
        }
    })
}

function obtenerReporteAmbienteLaboralMes() {
    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined
    id_grupo = id_grupo || undefined
    var fecha = new Date()
    var primer_dia = new Date(fecha.getFullYear(), fecha.getMonth(), 1)
    var ultimo_dia = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
    fecha_inicio = primer_dia.toISOString().slice(0, 10);
    fecha_fin = ultimo_dia.toISOString().slice(0, 10);

    $.ajax({
        url: window.base_url + "Reporte_resultados/ReporteAmbienteLaboral",
        type: 'POST',
        data: {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            nombre_usuario: nombre_usuario,
            group_id: id_grupo
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            reporte_ambiente_mes = json.data

            generarGraficaReporteAmbienteLaboral()
        }
    })
}

function generarGraficaReporteAmbienteLaboral() {
    if (reporte_ambiente_mes[0] == undefined)
        reporte_ambiente_mes = [{ promedio: 0, num_respuestas: 0 }];
    if (reporte_ambiente_semanal[0] == undefined)
        reporte_ambiente_semanal = [{ promedio: 0, num_respuestas: 0 }]
    var promedio_mes = reporte_ambiente_mes[0].promedio
    var promedio_semana = reporte_ambiente_semanal[0].promedio
    var carita_mes = obtenerCarita(promedio_mes)
    var carita_semana = obtenerCarita(promedio_semana)
    console.log(carita_mes)
    fecha = new Date(fecha_fin);
    if (carita_mes.includes('.png')) {
        document.getElementById("carita_mes").src = carita_mes;
        document.getElementById("num_respuestas_mes").innerHTML = "Numero de respuestas: " + reporte_ambiente_mes[0].num_respuestas
        document.getElementById("fecha_mes").innerHTML = fecha.getMonth() + "/" + fecha.getFullYear();
    }else{
        document.getElementById("num_respuestas_mes").innerHTML = "<b>Sin respuestas en este periodo</b>"
    }
    if (carita_semana.includes('.png')) {
        document.getElementById("carita_semana").src = carita_semana;
        document.getElementById("num_respuestas_semana").innerHTML = "Número de respuestas:" + reporte_ambiente_semanal[0].num_respuestas
        document.getElementById("fecha_semana").innerHTML = fecha.getMonth() + "/" + fecha.getFullYear();
    }else{
        document.getElementById("num_respuestas_semana").innerHTML = "<b>Sin respuestas en este periodo</b>"
    }



}

function obtenerCarita(promedio) {
    var carita = "http://kreativeco.com/nuup/assets/img/";
    switch (promedio) {
        case 1:
            carita += "cara_1_1.png"
            break;
        case 2:
            carita += "cara_2_1.png"
            break;
        case 3:
            carita += "cara_3_1.png"
            break;
        case 4:
            carita += "cara_4_1.png"
            break;
        case 5:
            carita += "cara_5_1.png"
            break;
    }
    return carita;
}

function generarTablas() {
    generarTablaBiblioteca();
    generarTablaPodcast();
    generarTablaWall()
    generarTablaRanking();
    generarTablaCuestionarios();
    generarTablaFeedback();
    generarTablaJuegos();
    generarTablaComunidad();
    generarTablaCuestionariosGeneral();
    generarTablaJuegosGeneral();
    generarTablaComunidadGeneral();
    establecer_en_circulos();
    generarGraficaLibrary();
    establecerDatosUsuarios()

    // ==================================
    generarGraficaWall();
}

function asignarVariables(data) {
    usuarios = data.usuarios;
    usuarios_biblioteca = data.usuarios_biblioteca;
    usuarios_originales = JSON.parse(JSON.stringify(data.usuarios));
    usuarios_biblioteca_originales = JSON.parse(JSON.stringify(data.usuarios_biblioteca));
    elementos_biblioteca = data.library;
    reporte_biblioteca = data.reporte_library;
    elementos_podcast = data.podcast;
    usuarios_podcast = data.reporte_podcast;
    elementos_wall = data.muro;
    usuarios_wall = data.reporte_wall;
    usuarios_ranking = data.reporte_ranking;
    usuarios_cuestionarios = data.participacion_quiz;
    cuestionarios = data.cuestionarios;
    usuarios_feedback = data.reporte_feedback;
    usuarios_juegos = data.reporte_juegos;
    juegos_productos = data.juegos_productos;
    juegos_ruleta = data.juegos_ruleta;
    juegos_serpientes_y_escaleras = data.juegos_serpientes_y_escaleras;
    juegos_profiler = data.juegos_perfilador;
    juegos_run_pancho_run = data.juegos_run_pancho_run
    juegos_retos = data.juegos_retos
    usuarios_comunidad = data.reporte_comunidad;
    invitados_comunidad = data.invitados_comunidad;
    topics = data.topics;
    categorias_cuestionarios = data.categorias_cuestionarios;
    podcast_mas_usados = JSON.parse(JSON.stringify(data.podcast_mas_usados));
    grafica_library = data.grafica_library;
    reporte_ambiente_semanal = data.reporte_ambiente_laboral_semana;
    juegos = data.juegos;
    usuarios_totales = data.total_usuarios
    usuarios_nuevos = data.usuarios_nuevos
    usuarios_regreso = data.usuarios_regreso
    sesiones_activas = data.sesiones_activas
    sesiones_promedio = data.promedio_sesiones
    duracion_sesion = data.duracion_sesion
    grafica_sesiones_activas = data.grafica_sesiones_activas;
    grafica_sesiones_usuarios = data.grafica_sesiones_usuario;
    grafica_duracion_sesion = data.grafica_duracion_sesion
    // reporte_ambiente_mes = data.reporte_ambiente_laboral_mes;
    
    if (!grupos)
        obtenerGruposRegistrados();
}

function establecerDatosUsuarios() {
    var div_total_usuarios = document.getElementById("total_usuarios")
    var div_usuarios_nuevos = document.getElementById("usuarios_nuevos")
    var div_usuarios_regreso = document.getElementById("usuarios_regreso")
    var div_sesiones_activas = document.getElementById("sesiones_activas")
    var div_sesiones_android = document.getElementById("sesiones_android")
    var div_sesiones_ios = document.getElementById("sesiones_ios")
    var div_sesiones_web = document.getElementById("sesiones_web")
    var div_sesiones_promedio = document.getElementById("sesiones_promedio")
    var div_promedio_android = document.getElementById("promedio_android")
    var div_promedio_ios = document.getElementById("promedio_ios")
    var div_promedio_web = document.getElementById("promedio_web")
    var div_duracion_sesion = document.getElementById("duracion_sesion")
    var div_duracion_android = document.getElementById("duracion_android")
    var div_duracion_ios = document.getElementById("duracion_ios")
    var div_duracion_web = document.getElementById("duracion_web")

    div_total_usuarios.innerHTML = usuarios_totales || 0
    div_usuarios_nuevos.innerHTML = usuarios_nuevos || 0
    div_usuarios_regreso.innerHTML = usuarios_regreso || 0
    div_sesiones_activas.innerHTML = sesiones_activas.sesiones_activas || 0
    div_sesiones_android.innerHTML = sesiones_activas.sesiones_android || 0
    div_sesiones_ios.innerHTML = sesiones_activas.sesiones_ios || 0
    div_sesiones_web.innerHTML = sesiones_activas.sesiones_web || 0
    div_sesiones_promedio.innerHTML = sesiones_promedio.promedio_sesiones || 0
    div_promedio_android.innerHTML = sesiones_promedio.promedio_android || 0
    div_promedio_ios.innerHTML = sesiones_promedio.promedio_ios || 0
    div_promedio_web.innerHTML = sesiones_promedio.promedio_web || 0

    div_duracion_sesion.innerHTML = convertirMinutosAHoras(duracion_sesion.duracion_sesion)

    div_duracion_android.innerHTML = convertirMinutosAHoras(duracion_sesion.duracion_android)

    div_duracion_ios.innerHTML = convertirMinutosAHoras(duracion_sesion.duracion_ios)

    div_duracion_web.innerHTML = convertirMinutosAHoras(duracion_sesion.duracion_web)

    generarGraficaSesionesActivas()
}

function generarGraficaSesionesActivas() {
    console.log(grafica_sesiones_activas)
    var fechas = []
    fechas.push(fecha_inicio);
    var f = new Date(fecha_inicio.replace(/-/g, '\/'))
    var ft = new Date();
    ft.setDate(f.getDate() + 1);
    console.log(fecha_inicio)
    console.log(f)
    console.log(ft)
    fechas.push(ft.toISOString().slice(0, 10))
    ft = new Date();
    ft.setDate(f.getDate() + 2);
    fechas.push(ft.toISOString().slice(0, 10))
    ft = new Date();
    ft.setDate(f.getDate() + 3);
    fechas.push(ft.toISOString().slice(0, 10))
    ft = new Date();
    ft.setDate(f.getDate() + 4);
    fechas.push(ft.toISOString().slice(0, 10))
    console.log(fechas)
    var sesiones = [];
    var sesiones_android = []
    var sesiones_ios = []
    var sesiones_web = []
    var sesiones_usuarios = []
    var sesiones_usuarios_android = []
    var sesiones_usuarios_ios = []
    var sesiones_usuarios_web = []
    var duracion = []
    var duracion_android = []
    var duracion_ios = []
    var duracion_web = []
    for (var i = 0; i < fechas.length; i++) {
        var g = grafica_sesiones_activas.totales.findIndex(f => { return f.fecha == fechas[i] })
        var g_android = grafica_sesiones_activas.android.findIndex(f => { return f.fecha == fechas[i] })
        var g_ios = grafica_sesiones_activas.ios.findIndex(f => { return f.fecha == fechas[i] })
        var g_web = grafica_sesiones_activas.web.findIndex(f => { return f.fecha == fechas[i] })
        var g_u = grafica_sesiones_usuarios.total.findIndex(f => { return f.fecha == fechas[i] })
        var g_u_android = grafica_sesiones_usuarios.android.findIndex(f => { return f.fecha == fechas[i] })
        var g_u_ios = grafica_sesiones_usuarios.ios.findIndex(f => { return f.fecha == fechas[i] })
        var g_u_web = grafica_sesiones_usuarios.web.findIndex(f => { return f.fecha == fechas[i] })
        var g_d = grafica_duracion_sesion.total.findIndex(f => { return f.fecha == fechas[i] })
        var g_d_android = grafica_duracion_sesion.android.findIndex(f => { return f.fecha == fechas[i] })
        var g_d_ios = grafica_duracion_sesion.ios.findIndex(f => { return f.fecha == fechas[i] })
        var g_d_web = grafica_duracion_sesion.web.findIndex(f => { return f.fecha == fechas[i] })
        if (g != -1) {
            sesiones.push(grafica_sesiones_activas.totales[g].sesiones_activas)
        } else {
            sesiones.push(0)
        }
        if (g_android != -1) {
            sesiones_android.push(grafica_sesiones_activas.android[g_android].sesiones_activas)
        } else {
            sesiones_android.push(0)
        }
        if (g_ios != -1) {
            sesiones_ios.push(grafica_sesiones_activas.ios[g_ios].sesiones_activas)
        } else {
            sesiones_ios.push(0)
        }
        if (g_web != -1) {
            sesiones_web.push(grafica_sesiones_activas.web[g_web].sesiones_activas)
        } else {
            sesiones_web.push(0)
        }
        if (g_u != -1) {
            sesiones_usuarios.push(grafica_sesiones_usuarios.total[g_u].sesiones)
        } else {
            sesiones_usuarios.push(0)
        }
        if (g_u_android != -1) {
            sesiones_usuarios_android.push(grafica_sesiones_usuarios.android[g_u_android].sesiones)
        } else {
            sesiones_usuarios_android.push(0)
        }
        if (g_u_ios != -1) {
            sesiones_usuarios_ios.push(grafica_sesiones_usuarios.ios[g_u_ios].sesiones)
        } else {
            sesiones_usuarios_ios.push(0)
        }
        if (g_u_web != -1) {
            sesiones_usuarios_web.push(grafica_sesiones_usuarios.web[g_u_web].sesiones)
        } else {
            sesiones_usuarios_web.push(0)
        }
        if (g_d != -1) {
            duracion.push(grafica_duracion_sesion.total[g_d].tiempo_sesion)
        } else {
            duracion.push(0)
        }
        if (g_d_android != -1) {
            duracion_android.push(grafica_duracion_sesion.android[g_d_android].tiempo_sesion)
        } else {
            duracion_android.push(0)
        }
        if (g_d_ios != -1) {
            duracion_ios.push(grafica_duracion_sesion.ios[g_d_ios].tiempo_sesion)
        } else {
            duracion_ios.push(0)
        }
        if (g_d_web != -1) {
            duracion_web.push(grafica_duracion_sesion.web[g_d_web].tiempo_sesion)
        } else {
            duracion_web.push(0)
        }
    }
    console.log(JSON.parse(JSON.stringify(sesiones)))
    var sesionesData = {
        labels: fechas,
        datasets: [{
            label: '',
            fill: false,
            borderColor: colorName.blue,
            data: sesiones
        },
        {
            label: '',
            fill: false,
            borderColor: colorName.green,
            data: sesiones_android
        }, {
            label: '',
            fill: false,
            borderColor: colorName.yellow,
            data: sesiones_ios
        }, {
            label: '',
            fill: false,
            borderColor: colorName.red,
            data: sesiones_web
        }
        ]
    };

    var ctx = document.getElementById('sesiones_activas_chart').getContext('2d');
    if (window.myLinearSesiones) window.myLinearSesiones.destroy();
    window.myLinearSesiones = new Chart(ctx, {
        type: 'line',
        data: sesionesData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: ''
                }
            }
        }
    });

    var sesionesDataUsuarios = {
        labels: fechas,
        datasets: [{
            label: '',
            fill: false,
            borderColor: colorName.blue,
            data: sesiones_usuarios
        },
        {
            label: '',
            fill: false,
            borderColor: colorName.green,
            data: sesiones_usuarios_android
        }, {
            label: '',
            fill: false,
            borderColor: colorName.yellow,
            data: sesiones_usuarios_ios
        }, {
            label: '',
            fill: false,
            borderColor: colorName.red,
            data: sesiones_usuarios_web
        }
        ]
    };
    var ctxUsuarios = document.getElementById('sesiones_promedio_chart').getContext('2d');
    if (window.myLinearSesionesUsuarios) window.myLinearSesionesUsuarios.destroy();
    window.myLinearSesionesUsuarios = new Chart(ctxUsuarios, {
        type: 'line',
        data: sesionesDataUsuarios,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: ''
                }
            }
        }
    });
    var sesionesDataDuracion = {
        labels: fechas,
        datasets: [{
            label: '',
            fill: false,
            borderColor: colorName.blue,
            data: duracion
        },
        {
            label: '',
            fill: false,
            borderColor: colorName.green,
            data: duracion_android
        }, {
            label: '',
            fill: false,
            borderColor: colorName.yellow,
            data: duracion_ios
        }, {
            label: '',
            fill: false,
            borderColor: colorName.red,
            data: duracion_web
        }
        ]
    };
    console.log("duraciones", sesionesDataDuracion)
    var ctxDuracion = document.getElementById('duracion_sesion_chart').getContext('2d');
    if (window.myLinearDuracion) window.myLinearDuracion.destroy();
    window.myLineaDuracion = new Chart(ctxDuracion, {
        type: 'line',
        data: sesionesDataDuracion,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: ''
                }
            }
        }
    });
}

function convertirMinutosAHoras(minutos) {
    var hour = Math.floor(minutos / 60);
    hour = (hour < 10) ? '0' + hour : hour;
    var minute = Math.floor(minutos % 60);
    minute = (minute < 10) ? '0' + minute : minute;
    return hour + ":" + minute
}

function generarGraficaLibrary() {
    grafica_library = grafica_library.slice(0, 10);
    var horizontalBarChartData = JSON.parse(JSON.stringify(getData(grafica_library, "Uso de biblioteca", "title", "veces_visto", "black")))

    var ctx = document.getElementById('library_chart').getContext('2d');

    if (window.my_post_likes_chart) window.my_post_likes_chart.destroy();

    window.my_post_likes_chart = createNewChart(ctx, horizontalBarChartData);
}

function getData(data, _label, clave_labels, clave_series, color) {
    var _labels = getLabels(data, clave_labels)
    var _series = getSeries(data, clave_series);
    return {
        labels: _labels,
        datasets: [{
            label: _label,
            backgroundColor: colorName[color],
            borderColor: colorName[color],
            borderWidth: 1,
            data: _series
        }]
    }
}

function getLabels(data, clave) {
    var labels = []
    for (i = 0; i < data.length; i++) {
        labels.push(data[i][clave])
    }
    return labels
}

function getSeries(data, clave) {
    var series = []
    for (i = 0; i < data.length; i++) {
        series.push(data[i][clave])
    }
    return series
}

function createNewChart(ctx, data) {
    return new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            elements: {
                rectangle: {
                    borderWidth: 2,
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'top',
            },
            title: {
                display: false,
                text: ''//el titulo esta directo en contenedor_grafica
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        suggestedMax: 5,
                        stepSize: 2
                    }
                }],
                xAxes: [{
                    ticks: {
                        callback: function (value, index, values) {
                            return value.substring(0, 10) + "...";
                        }
                    }
                }]
            }
        }
    });
}

function generarTablaBiblioteca() {
    var tabla = document.getElementById("tbl_biblioteca");
    var html = "";
    if (usuarios_biblioteca.length && elementos_biblioteca) {
        for (i = 0; i < usuarios_biblioteca.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios[i]))
            var header;
            if (i === 0) {
                header = '<tr><th class="fondo_negro"></th>'
            }
            if (!html.includes(usuario.name)) {
                html += '<tr><td class="fondo_negro">' + usuario.name + '</td>'
                for (j = 0; j < elementos_biblioteca.length; j++) {
                    var elemento = JSON.parse(JSON.stringify(elementos_biblioteca[j]))
                    if (i === 0) {
                        header += '<th class="rotate fondo_negro"><div><span>' + elemento.title.substring(0, 25) + '</span></div></th>'
                    }
                    html += '<td>' + getCheckLibreria(elemento.id, usuario.id) + '</td>'
                }
                html += "</tr>";
            }
        }
        tabla.innerHTML = header + "</tr>" + html;
    }
    else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN BIBLIOTECA</td></tr>"
    }
}

function getCheckLibreria(id_elemento, id_usuario) {
    var check = "";
    var veces_visto = 0;
    for (k = 0; k < reporte_biblioteca.length; k++) {
        var usuario = reporte_biblioteca[k]
        if (usuario.id === id_usuario && usuario.library_id === id_elemento && usuario.veces_visto > 0) {
            veces_visto = usuario.veces_visto
        }
    }
    check = "<label>" + veces_visto + "v</label>";
    return check;
}

function obtenerGruposRegistrados() {
    $.ajax({
        url: window.base_url + "Groups/GroupsRegister",
        type: 'GET',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            grupos = json.data;
            generarSelectConGrupos();
        }
    })
}

function generarTablaPodcast() {
    var tabla = document.getElementById("tbl_podcast");
    var html = "";
    if ((usuarios_podcast.comentarios || usuarios_podcast.uso || usuarios_podcast.calificaciones) && elementos_podcast.length > 0 && usuarios.length > 0) {
        for (i = 0; i < usuarios.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios[i]))
            var header;
            if (i === 0) {
                header = '<tr><th class="fondo_negro"></th>'
            }
            if (!html.includes(usuario.name)) {
                html += '<tr><td class="fondo_negro">' + usuario.name + '</td>'
                for (j = 0; j < elementos_podcast.length; j++) {
                    var elemento = JSON.parse(JSON.stringify(elementos_podcast[j]))
                    if (i === 0) {
                        header += '<th class="rotate fondo_negro"><div><span>' + (elemento.title || "").substring(0, 25) + '</span></div></th>'
                    }
                    html += '<td>' + getCheckPodcast(elemento.id, usuario.id) + '</td>'
                }
                html += "</tr>";
            }
        }
        tabla.innerHTML = header + "</tr>" + html;
    } else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN PODCAST</td></tr>"
    }
}

function getCheckPodcast(id_elemento, id_usuario) {
    var num_comentarios = 0, score = 0, veces_visto = 0;
    for (k = 0; k < usuarios_podcast.comentarios.length; k++) {
        var usuario = usuarios_podcast.comentarios[k]
        if (usuario.user_id === id_usuario && usuario.podcast_id === id_elemento) {
            num_comentarios = usuario.num_comentarios
        }
    }
    for (k = 0; k < usuarios_podcast.calificaciones.length; k++) {
        var usuario = usuarios_podcast.calificaciones[k]
        if (usuario.user_id === id_usuario && usuario.podcast_id === id_elemento) {
            score = usuario.score
        }
    }
    for (k = 0; k < usuarios_podcast.uso.length; k++) {
        var usuario = usuarios_podcast.uso[k]
        if (usuario.id === id_usuario && usuario.podcast_id === id_elemento) {
            veces_visto = usuario.veces_visto
        }
    }

    return '<label>' + veces_visto + 'v ' + score + 'cal ' + num_comentarios + 'c</label>';
}

function generarTablaWall() {
    var tabla = document.getElementById("tbl_wall");
    var html = "";

    if (usuarios_wall && elementos_wall.length > 0 && usuarios.length > 0) {
        for (i = 0; i < usuarios.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios[i]))
            var header;
            if (i === 0) {
                header = '<tr><th class="fondo_negro"></th>'
                for (var k = 0; k < elementos_wall.length; k++) {
                    var elemento = JSON.parse(JSON.stringify(elementos_wall[k]))
                    header += '<th class="rotate fondo_negro"><div><span>' + elemento.wall_description.substring(0, 25) + '</span></div></th>'

                }
            }

            if (!html.includes(usuario.name)) {
                html += '<tr><td class="fondo_negro">' + usuario.name + '</td>'
                for (j = 0; j < elementos_wall.length; j++) {
                    var elemento = JSON.parse(JSON.stringify(elementos_wall[j]))
                    html += '<td>' + getCheckWall(elemento.id, usuario.id) + '</td>'
                }
                html += "</tr>";
            }

        }
        tabla.innerHTML = header + "</tr>" + html;
    } else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN MURO</td></tr>"
    }
}

function getCheckWall(id_elemento, id_usuario) {
    var check = "";
    var num_comentarios = 0;
    var clave = "comentarios"

    var bandera = false;
    for (var k = 0; k < usuarios_wall.comentarios.length; k++) {
        var usuario_ = usuarios_wall.comentarios[k]
        if (usuario_.user_id === id_usuario && usuario_.post_id === id_elemento) {
            num_comentarios = usuario_.num_comentarios
        }
    }
    clave = "likes"
    for (var p = 0; p < usuarios_wall.likes.length; p++) {
        var usuario_ = usuarios_wall.likes[p]
        if (usuario_.user_id === id_usuario && usuario_.post_id === id_elemento) {
            bandera = true;
        }
    }
    var double = "<i class='fas fa-check-double' style='color:blue'>&#x2714;&#x2714;</i>";
    var simple = "<i class='fa fa-check' style='color:green'></i>";
    var none = "<i class='fa fa-times' style='color:red'></i>"
    if (bandera)
        check = simple
    else
        check = none
    check += "<label>" + num_comentarios + "c</label>"
    return check;
}

// 
// PARA RANKING
// 

function generarTablaRanking() {
    var tabla = document.getElementById("tbl_ranking");
    var html = "";// "<tr><td>Usuario</td><td>Puntuación</td></tr>";
    if (usuarios_ranking.length > 0) {
        for (i = 0; i < usuarios_ranking.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios_ranking[i]))
            html += '<tr><td>' + usuario.name + '</td><td><strong>' + usuario.score + '</strong></td></tr>'
        }
        tabla.innerHTML = html;
    } else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN MURO</td></tr>"
    }
}



// 
// para cuestionarios
//

function establecerCuestionariosPorCategoria() {
    for (var i = 0; i < categorias_cuestionarios.length; i++) {
        var categoria = categorias_cuestionarios[i];
        categoria.num_cuestionarios = cuestionarios.filter(c => { return c.category_id === categoria.id }).length
    }
}

function generarTablaCuestionarios() {
    var tabla = document.getElementById("tbl_cuestionarios");
    establecerCuestionariosPorCategoria()
    var html = "";
    var html_c = "<tr><th class='fondo_negro'></th>"
    for (var i = 0; i < categorias_cuestionarios.length; i++) {
        var categoria = categorias_cuestionarios[i]
        if (categoria.num_cuestionarios > 0)
            html_c += "<th class='fondo_negro' span=" + categoria.num_cuestionarios + ">" + categoria.name + "</th>"
    }
    html_c += "</tr>"
    if (usuarios_cuestionarios.length) {
        for (i = 0; i < usuarios.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios[i]))
            var header;
            if (i === 0) {
                header = '<tr><th class="fondo_negro"></th>'
            }
            if (!html.includes(usuario.name)) {
                html += '<tr><td  class="fondo_negro">' + usuario.name + '</td>'
                for (j = 0; j < cuestionarios.length; j++) {
                    var elemento = JSON.parse(JSON.stringify(cuestionarios[j]))
                    if (i === 0) {
                        header += '<th class="rotate fondo_negro"><div><span>' + elemento.name.substring(0, 25) + '</span></div></th>'
                    }
                    html += '<td>' + getCheckCuestionarios(elemento.id, usuario.id) + '</td>'
                }
                html += "</tr>";
            }
        }
        tabla.innerHTML = header + "</tr>" + html_c + html;
    }
    else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN CUESTIONARIOS</td></tr>"
    }
}

function getCheckCuestionarios(id_elemento, id_usuario) {
    var check = "";
    var bandera = false;
    for (k = 0; k < usuarios_cuestionarios.length; k++) {
        var usuario = usuarios_cuestionarios[k]
        if (usuario.user_id === id_usuario && usuario.id === id_elemento) {
            bandera = true
        }
    }
    var double = "<i class='fas fa-check-double' style='color:blue'>&#x2714;&#x2714;</i>";
    var simple = "<i class='fa fa-check' style='color:green'></i>";
    var none = "<i class='fa fa-times' style='color:red'></i>"
    if (bandera) {
        check = simple
    } else {
        check = none
    }
    return check;
}

// 
// para reporte de feedback
// 

function generarTablaFeedback() {
    var tabla = document.getElementById("tbl_feedback");
    obtenerRetroalimentacionTotal();
    var html = "<tr><td class='fondo_negro'>Usuario</td><td class='fondo_negro'>Comentarios</td></tr>";
    if (usuarios_feedback.length) {
        for (i = 0; i < usuarios.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios[i]))
            var usuario_feedback = usuarios_feedback.filter(u => { return u.user_id === usuario.id })[0];
            if (usuario_feedback)
                html += "<tr><td class='fondo_negro'>" + usuario.name + "</td><td>" + usuario_feedback.num_comentarios + "</td></tr>"
        }
        tabla.innerHTML = html;
    }
    else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN FEEDBACK</td></tr>"
    }
}

function obtenerRetroalimentacionTotal() {
    retroalimentacion_total = 0;
    for (var i = 0; i < usuarios_feedback.length; i++) {
        var f = usuarios_feedback[i];
        if (usuarios.some(u => { return u.id === f.user_id })) {
            retroalimentacion_total += f.num_comentarios;
        }
    }
    document.getElementById("retroalimentacion").innerHTML = retroalimentacion_total;
}

// 
// para reporte de juegos
// 

function generarTablaJuegos() {

    var tabla = document.getElementById("tbl_juegos");
    if (usuarios_juegos.length > 0) {
        var juegos_categorias = [
            { name: "Juegos Productos", colspan: juegos_productos.length },
            { name: "Juegos Ruleta", colspan: juegos_ruleta.length },
            { name: "Juegos Serpientes y Escaleras", colspan: juegos_serpientes_y_escaleras.length },
            { name: "Juegos Profiler", colspan: juegos_profiler.length },
            { name: "Juegos Run Pancho Run", colspan: juegos_run_pancho_run.length }
        ];


        var html = "<tr><th class='fondo_negro'></th>";
        for (i = 0; i < juegos_categorias.length; i++) {
            html += "<th class='fondo_negro' colspan='" + juegos_categorias[i].colspan + "'>" + juegos_categorias[i].name + "</th>";
        }
        html += "</tr><tr><th class='fondo_negro'></th>"

        if (juegos_productos.length > 0) {
            for (i = 0; i < juegos_productos.length; i++) {
                html += "<th class='rotate fondo_negro'><div><span>" + (juegos_productos[i].description) + "</span></div></th>"
            }
        } else {
            html += "<th class='fondo_negro'></th>"
        }

        if (juegos_ruleta.length > 0) {
            for (i = 0; i < juegos_ruleta.length; i++) {
                html += "<th class='rotate fondo_negro'><div><span>" + juegos_ruleta[i].name + "</span></div></th>"
            }
        } else {
            html += "<th class='fondo_negro'></th>"
        }

        if (juegos_serpientes_y_escaleras.length > 0) {
            for (i = 0; i < juegos_serpientes_y_escaleras.length; i++) {
                html += "<th class='rotate fondo_negro'><div><span>" + juegos_serpientes_y_escaleras[i].game_name + "</span></div></th>"
            }
        } else {
            html += "<th class='fondo_negro'></th>"
        }
        if (juegos_profiler.length > 0) {
            for (i = 0; i < juegos_profiler.length; i++) {
                html += "<th class='rotate fondo_negro'><div><span>" + juegos_profiler[i].history + "</span></div></th>"
            }
        } else {
            html += "<th class='fondo_negro'></th>"
        }

        html += "</tr>"

        for (i = 0; i < usuarios.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios[i]))
            if (!html.includes(usuario.name)) {
                html += '<tr><td class="fondo_negro">' + usuario.name + '</td>'
                if (juegos_productos.length > 0) {
                    for (j = 0; j < juegos_productos.length; j++) {
                        var elemento = JSON.parse(JSON.stringify(juegos_productos[j]))
                        html += '<td>' + getCheckJuegos(elemento.id, usuario.id, "Juegos Productos") + '</td>'
                    }
                } else {
                    html += "<td></td>"
                }
                if (juegos_ruleta.length > 0) {
                    for (j = 0; j < juegos_ruleta.length; j++) {
                        var elemento = JSON.parse(JSON.stringify(juegos_ruleta[j]))
                        html += '<td>' + getCheckJuegos(elemento.id, usuario.id, "Juegos Ruleta") + '</td>'
                    }
                } else {
                    html += "<td></td>"
                }
                if (juegos_serpientes_y_escaleras.length > 0) {
                    for (j = 0; j < juegos_serpientes_y_escaleras.length; j++) {
                        var elemento = JSON.parse(JSON.stringify(juegos_serpientes_y_escaleras[j]))
                        html += '<td>' + getCheckJuegos(elemento.id, usuario.id, "Juegos Serpientes y Escaleras") + '</td>'
                    }
                } else {
                    html += "<td></td>"
                }
                if (juegos_profiler.length > 0) {
                    for (j = 0; j < juegos_profiler.length; j++) {
                        var elemento = JSON.parse(JSON.stringify(juegos_profiler[j]));
                        html += '<td>' + getCheckJuegos(elemento.id, usuario.id, "Juegos Profiler") + '</td>'
                    }
                } else {
                    html += "<td></td>"
                }
                html += "</tr>";
            }

        }
        tabla.innerHTML = html;
    } else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN JUEGOS</td></tr>"
    }
}

function getCheckJuegos(id_elemento, id_usuario, categoria) {
    var check = "";
    var bandera = false;
    for (k = 0; k < usuarios_juegos.length; k++) {
        var usuario = usuarios_juegos[k]
        if (usuario.id === id_usuario && usuario.game_id === id_elemento && usuario.categoria === categoria) {
            bandera = true;
        }
    }
    var double = "<i class='fas fa-check-double' style='color:blue'>&#x2714;&#x2714;</i>";
    var simple = "<i class='fa fa-check' style='color:green'></i>";
    var none = "<i class='fa fa-times' style='color:red'></i>"
    if (bandera)
        check = simple
    else
        check = none
    return check;
}


// 
// PARA COMUNIDAD DE APRENDIZAJE
// 

function generarTablaComunidad() {
    var tabla = document.getElementById("tbl_comunidad");
    html = "";
    if (usuarios_comunidad.length > 0) {
        html = "<tr><th class='fondo_negro'></th>";
        for (i = 0; i < topics.length; i++) {
            html += "<th class='rotate fondo_negro'><div><span>" + topics[i].name + "</div></span></th>"
        }
        html += "</tr>"

        for (i = 0; i < usuarios.length; i++) {
            var usuario = JSON.parse(JSON.stringify(usuarios[i]))
            if (!html.includes(usuario.name)) {
                html += '<tr><td class="fondo_negro">' + usuario.name + '</td>'
                for (j = 0; j < topics.length; j++) {
                    var elemento = JSON.parse(JSON.stringify(topics[j]))

                    html += '<td>' + getCheckTopics(elemento.id, usuario.id) + '</td>'
                }
                html += "</tr>";
            }
        }
        tabla.innerHTML = html;
    }
    else {
        tabla.innerHTML = "<tr><td>SIN REGISTROS DE USO EN CUESTIONARIOS</td></tr>"
    }
}

function getCheckTopics(id_elemento, id_usuario) {
    var check = "";
    var bandera = false;
    var numero_mensajes = 0;
    for (k = 0; k < usuarios_comunidad.length; k++) {
        var usuario = usuarios_comunidad[k]
        if (usuario.id === id_usuario && usuario.id_topic === id_elemento) {
            numero_mensajes = usuario.numero_mensajes
        }
    }
    check = "<label>" + numero_mensajes + "m</label>"
    return check;
}


//
// interaccion con  podcast
//


function establecer_en_circulos() {
    var arreglo = obtenerArregloReal();
    console.log("este es el arreglo", arreglo)
    var _labels = getLabels(arreglo, "title");
    var _likes = getSeries(arreglo, "promedio");
    var _comentarios = getSeries(arreglo, "num_comentarios")
    var _vistoPodcast = getSeries(arreglo, "veces_visto");

    // _visto = [100]
    // console.log("este es el valor de visto", _visto)
    var barChartData = {
        labels: _labels,
        datasets: [{
            label: 'Calificaciones',
            fill: false,
            borderColor: colorName.blue,
            backgroundColor: colorName.blue,
            data: _likes
        }, {
            label: 'Comentarios',
            fill: false,
            borderColor: colorName.green,
            backgroundColor : colorName.green,
            data: _comentarios
        }, {
            label: 'Veces visto',
            fill: false,
            borderColor: colorName.yellow,
            backgroundColor : colorName.yellow,
            data: _vistoPodcast
        }
        ]
    };

    var ctxPodcast = document.getElementById('podcast_chart').getContext('2d');
    if (window.myLinearBar) window.myLinearBar.destroy();
    window.myLinearBar = new Chart(ctxPodcast, {
        type: 'bar',
        data: barChartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Chart.js Line Chart'
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        suggestedMax: 5,
                        stepSize: 2
                        // userCallback: function(label, index, labels) {
                        //     // when the floored value is the same as the value we have a whole number
                        //     if (Math.floor(label) === label) {
                        //         return label;
                        //     }
       
                        // },
                    }
                }],
            }
        }
    });


}

function obtenerArregloReal() {
    var arreglo = []
    arreglo = JSON.parse(JSON.stringify(podcast_mas_usados["comentarios"]))
    for (var i = 0; i < podcast_mas_usados["score"].length; i++) {
        var p = JSON.parse(JSON.stringify(podcast_mas_usados["score"][i]))
        if (arreglo.some(a => { return a.id === p.id })) {
            var indice = obtenerIndicePodcast(p.id, arreglo);
            arreglo[indice].promedio = p.promedio;
        } else {
            p.num_comentarios = 0;
            arreglo.push(p);
        }
    }

    for (var i = 0; i < podcast_mas_usados["vistos"].length; i++) {
        var p = JSON.parse(JSON.stringify(podcast_mas_usados["vistos"][i]))
        if (arreglo.some(a => { return a.id === p.id })) {
            var indice = obtenerIndicePodcast(p.id, arreglo);
            arreglo[indice].veces_visto = p.veces_visto;
        } else {
            p.num_comentarios = 0;
            p.promedio = 0;
            arreglo.push(p);
        }
    }
    arreglo = obtenerPodcastMasInteracciones(arreglo);
    return arreglo;
}

function obtenerPodcastMasInteracciones(arreglo) {
    for (var i = 0; i < arreglo.length; i++) {
        var a = JSON.parse(JSON.stringify(arreglo[i]))
        a.interacciones = a.num_comentarios + a.promedio + a.veces_visto;
        arreglo[i] = JSON.parse(JSON.stringify(a))
    }
    for (var i = 0; i < arreglo.length - 1; i++) {
        for (var j = 0; j < arreglo.length - i - 1; j++) {
            if (arreglo[j + 1]) {
                var a = JSON.parse(JSON.stringify(arreglo[j].interacciones))
                var b = JSON.parse(JSON.stringify(arreglo[j + 1].interacciones))
                if (a < b) {
                    var temp = JSON.parse(JSON.stringify(arreglo[j]))
                    arreglo[j] = JSON.parse(JSON.stringify(arreglo[j + 1]))
                    arreglo[j + 1] = temp
                }
            }
        }
    }
    return arreglo;
}

function obtenerIndicePodcast(id, arreglo) {
    var indice = -1;
    for (var j = 0; j < arreglo.length; j++) {
        if (arreglo[j].id === id) {
            indice = j;
        }
    }
    return indice;
}

//
// para mostrar post con mas interacciones en el muro
//
//


function obtenerPostMasComentados(tipo) {
    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined
    id_grupo = id_grupo || undefined
    $.ajax({
        url: window.base_url + "Analiticos/ObtenerPostMasComentados",
        type: 'GET',
        data: {
            tipo: tipo,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            nombre_usuario: nombre_usuario
        },
        error: function (xhr, error, code) {
            console.log('error',error)
            console.log('xhr',xhr)
        },
        success: function (json) {
            console.log('obtenerPostMasComentados', json);
            post_mas_usados = json.data;
            if(post_mas_usados.comentarios.length > 0 || post_mas_usados.likes.length > 0){
                construir_tabla_post_general();
            }else{
                let tabla = document.getElementById("tabla_post_general");
                tabla.innerHTML = "<tr>Sin interacciones en muro durante el periodo</tr>"   
            }
        }
    })
}

function obtenerNombrePost(arreglo) {
    var nombres = [];
    for (var i = 0; i < 10; i++) {
        if (arreglo[i] !== undefined) {
            nombres.push(arreglo[i].wall_description);
        }
    }
    return nombres;
}

function construir_tabla_post_general() {
    var post = obtenerPostComMasInteracciones();
    //aqui ordenar por mayor numero de interacciones
    var tabla = document.getElementById("tabla_post_general");
    var nombres = obtenerNombrePost(post);
    var html = "<tr>";
    for (var i = 0; i < nombres.length; i++) {
        html += "<th class='rotate fondo_negro'><div><span>" + nombres[i].substring(0, 20) + "</span></div></th>"
    }
    html += "</tr><tr>"
    //el for estaba asi pero se supone que lo limite a solo 10 elementos
    //post.length
    for (var i = 0; i < 10; i++) {
        if (post[i]) {
            var likes = post[i].num_likes || 0;
            var comentarios = post[i].num_comentarios || 0;
            html += "<td style='border : solid 1px #eee;'><p>" + likes + "<img class='pr-1 pb-1' src='./../assets/img/img_check_full.png'></p>" +
                "<p>" + comentarios + "c</p></td>";
        }
    }
    html += "</tr>"
    console.log("lo que tiene el wall",html)
    tabla.innerHTML = html;
}

function obtenerPostComMasInteracciones() {
    var arreglo = [];
    arreglo = post_mas_usados.likes || [];
    comentarios = post_mas_usados.comentarios;
    for (var i = 0; i < comentarios.length; i++) {
        var c = comentarios[i];
        var indice = obtenerIndice(c.id);
        if (indice !== -1) {
            arreglo[indice].num_comentarios = c.num_comentarios;
            arreglo[indice].num_interacciones = c.num_comentarios + arreglo[indice].num_likes;
        }
        else {
            c.num_interacciones = c.num_comentarios;
            arreglo.push(c)
        }
    }
    return arreglo;
}

function obtenerIndice(id) {
    var indice = -1;
    for (var i = 0; i < post_mas_usados.likes.length; i++) {
        if (post_mas_usados.likes[i].id === id) {
            indice = i;
        }
    }
    return indice;
}

function generarTablaCuestionariosGeneral() {
    var participacion_cuestionarios = obtenerParticipacionCuestionarios()
    var table = document.getElementById("tabla_cuestionarios_general");
    var html = "";
    if (participacion_cuestionarios.length > 0) {
        for (var i = 0; i < participacion_cuestionarios.length; i++) {
            var p = participacion_cuestionarios[i];
            var double = "<i class='fas fa-check-double' style='color:blue'>&#x2714;&#x2714;</i>";
            var simple = "<i class='fa fa-check' style='color:green'></i>";
            var none = "<i class='fa fa-times' style='color:red'></i>";
            var check = p.bandera === 2 ? double : p.bandera === 1 ? simple : none;
            html += "<tr><td class='fondo_negro'>" + p.name + "</td><td style='border: solid 1px #ccc'>" + check + "</td></tr>"
        }

    } else {
        html = '<tr><td>Sin participación en cuestionarios</td></tr>'
    }
    table.innerHTML = html;
}

function obtenerParticipacionCuestionarios() {
    var participacion = [];
    if (usuarios_cuestionarios.length > 0)
        for (var i = 0; i < categorias_cuestionarios.length; i++) {
            var categoria = categorias_cuestionarios[i];
            var bandera = 0;
            for (var j = 0; j < usuarios.length; j++) {
                var u = usuarios[j];
                if (usuarios_cuestionarios.some(uc => { return uc.user_id === u.id && uc.category_id === categoria.id })) {
                    bandera += usuarios_cuestionarios.filter(uc => { return uc.user_id === u.id && uc.category_id === categoria.id }).length;
                }
            }
            var cuestionarios_en_categoria = usuarios.length//obtenerCuestionariosEnCategoria(categoria.id);
            //aqui me quede hoy       
            if (bandera === cuestionarios_en_categoria && cuestionarios_en_categoria > 0) {
                bandera = 2;
            } else if (bandera > 0) {
                bandera = 1;
            } else {
                bandera = 0;
            }
            participacion.push({ name: categoria.name, bandera: bandera });
            bandera = 0
        }
    return participacion;
}

function obtenerCuestionariosEnCategoria(id) {
    var filtrados = []
    for (var i = 0; i < usuarios_cuestionarios.length; i++) {
        var cuestionario = usuarios_cuestionarios[i]
        if (!filtrados.some(f => { return f.id === cuestionario.id }) && cuestionario.category_id === id) {
            filtrados.push(cuestionario);
        }
    }
    return filtrados.length;
}

function generarTablaComunidadGeneral() {
    var participacion_comunidad = obtenerParticipacionComunidad();
    participacion_comunidad.sort((a, b) => b.bandera > a.bandera ? 1 : a.bandera > b.bandera ? -1 : 0)
    participacion_comunidad.splice(10, participacion_comunidad.length)
    console.log("esta es la participacion en comunidad", participacion_comunidad)
    var tabla = document.getElementById("tabla_chat_general");
    var html = "";
    for (var i = 0; i < participacion_comunidad.length; i++) {
        var p = participacion_comunidad[i];
        var double = "<i class='fas fa-check-double' style='color:blue'>&#x2714;&#x2714;</i>";
        var simple = "<i class='fa fa-check' style='color:green'></i>";
        var none = "<i class='fa fa-times' style='color:red'></i>";
        var check = p.bandera === 2 ? double : p.bandera === 1 ? simple : none;
        html += "<tr><td class='fondo_negro'>" + p.name + "</td><td style='border: solid 1px #ccc'>" + check + "</td></tr>"
    }
    tabla.innerHTML = html;
}

function obtenerParticipacionComunidad() {
    console.log("estos son los invitados a la comunidad", invitados_comunidad)
    var participacion = [];
    console.log("estos son los topics", topics)
    for (var i = 0; i < topics.length; i++) {
        var topic = topics[i];
        var bandera = 0;
        for (var j = 0; j < usuarios.length; j++) {
            var u = usuarios[j];
            if (invitados_comunidad.some(inv => { return inv.id_user === u.id && inv.id_topic === topic.id })) {
                if (usuarios_comunidad.some(uc => { return uc.id === u.id && uc.id_topic === topic.id })) {
                    bandera += 1;
                }
            }
        }
        var usuarios_invitados = obtenerUsuariosInvitados(topic.id);
        if (bandera === usuarios_invitados && usuarios_invitados > 0) {
            bandera = 2;
        } else if (bandera > 0) {
            bandera = 1;
        }
        else {
            bandera = 0;
        }
        participacion.push(JSON.parse(JSON.stringify({ name: topic.name, bandera: bandera })));
        bandera = 0;
    }
    return participacion;
}

function obtenerUsuariosInvitados(id) {
    var invitados = invitados_comunidad.filter(u => { return u.id_topic === id });
    return invitados.length;
}


function generarTablaJuegosGeneral() {
    var participacion_juegos = obtenerParticipacionJuegos();
    var tabla = document.getElementById("tabla_juegos_general");
    var html = "";
    for (var i = 0; i < participacion_juegos.length; i++) {
        var p = participacion_juegos[i];
        var double = "<i class='fas fa-check-double' style='color:blue'>&#x2714;&#x2714;</i>";
        var simple = "<i class='fa fa-check' style='color:green'></i>";
        var none = "<i class='fa fa-times' style='color:red'></i>";
        var check = p.bandera == 2 ? double : p.bandera === 1 ? simple : none;
        html += "<tr><td class='fondo_negro'>" + p.name + "</td><td style='border: solid 1px #ccc'>" + check + "</td></tr>"
    }
    tabla.innerHTML = html;
}


function obtenerParticipacionJuegos() {
    var participacion = []
    var bandera = 0;
    var indice = juegos.findIndex(j => j.name.includes("Productos"));
    if (indice !== -1) {
        if (juegos_productos.length > 0) {
            for (var i = 0; i < juegos_productos.length; i++) {
                var juego = juegos_productos[i];
                for (var j = 0; j < usuarios.length; j++) {
                    var u = usuarios[j];
                    if (usuarios_juegos.some(p => { return p.categoria === "Juegos Productos" && p.id === u.id && p.game_id === juego.id })) {
                        bandera += 1;
                    }
                }
            }
            if (bandera === usuarios.length * juegos_productos.length) {
                bandera = 2;
            }
            else if (bandera > 0) {
                bandera = 1;
            } else {
                bandera = 0;
            }
        }
        participacion.push({ name: juegos[indice].name, bandera: bandera });
    }
    bandera = 0;

    indice = juegos.findIndex(j => j.name.includes("Ruleta"));
    console.log("el indice que se recupera en ruleta", indice);
    if (indice !== -1) {
        if (juegos_ruleta.length > 0) {
            for (var i = 0; i < juegos_ruleta.length; i++) {
                var juego = juegos_ruleta[i];
                for (var j = 0; j < usuarios.length; j++) {
                    var u = usuarios[j];
                    if (usuarios_juegos.some(p => { return p.categoria === "Juegos Ruleta" && p.id === u.id && p.game_id === juego.id })) {
                        bandera += 1;
                    }
                }
            }
            if (bandera === usuarios.length * juegos_ruleta.length) {
                bandera = 2;
            }
            else if (bandera > 0) {
                bandera = 1;
            } else {
                bandera = 0;
            }
        }

        participacion.push({ name: juegos[indice].name, bandera: bandera });
    }
    bandera = 0;

    indice = juegos.findIndex(j => j.name.includes("Serpientes"));
    if (indice !== -1) {
        if (juegos_serpientes_y_escaleras.length > 0) {
            for (var i = 0; i < juegos_serpientes_y_escaleras.length; i++) {
                var juego = juegos_serpientes_y_escaleras[i];
                for (var j = 0; j < usuarios.length; j++) {
                    var u = usuarios[j];
                    if (usuarios_juegos.some(p => { return p.categoria === "Juegos Serpientes y Escaleras" && p.id === u.id && p.game_id === juego.id })) {
                        bandera += 1;
                    }
                }
            }
            if (bandera === usuarios.length * juegos_serpientes_y_escaleras.length) {
                bandera = 2;
            }
            else if (bandera > 0) {
                bandera = 1;
            } else {
                bandera = 0;
            }
        }
        participacion.push({ name: juegos[indice].name, bandera: bandera });
    }
    bandera = 0

    indice = juegos.findIndex(j => j.name.includes("Perfilador"));
    if (indice !== -1) {
        if (juegos_profiler.length > 0) {
            for (var i = 0; i < juegos_profiler.length; i++) {
                var juego = juegos_profiler[i];
                for (var j = 0; j < usuarios.length; j++) {
                    var u = usuarios[j];
                    if (usuarios_juegos.some(p => { return p.categoria === "Juegos Profiler" && p.id === u.id && p.game_id === juego.id })) {
                        bandera += 1;
                    }
                }
            }
            if (bandera === usuarios.length * juegos_profiler.length) {
                bandera = 2;
            } else if (bandera > 0) {
                bandera = 1;
            } else {
                bandera = 0;
            }
        }
        participacion.push({ name: juegos[indice].name, bandera: bandera });
    }
    bandera = 0;
    indice = juegos.findIndex(j => j.name.includes("Run"));
    if (indice !== -1) {
        if (juegos_run_pancho_run.length > 0) {
            for (var i = 0; i < juegos_run_pancho_run.length; i++) {
                var juego = juegos_run_pancho_run[i];
                for (var j = 0; j < usuarios.length; j++) {
                    var u = usuarios[j];
                    if (usuarios_juegos.some(p => { return p.categoria === "Juegos Run Pancho Run" && p.id === u.id && p.game_id === juego.id })) {
                        bandera += 1;
                    }
                }
            }
            if (bandera === usuarios.length * juegos_run_pancho_run.length) {
                bandera = 2;
            } else if (bandera > 0) {
                bandera = 1;
            } else {
                bandera = 0;
            }
        }
        participacion.push({ name: juegos[indice].name, bandera: bandera });
    }

    bandera = 0;
    indice = juegos.findIndex(j => j.name.includes("Retos"));
    if (indice !== -1) {
        if (juegos_retos.length > 0) {
            for (var i = 0; i < juegos_retos.length; i++) {
                var juego = juegos_retos[i];
                for (var j = 0; j < usuarios.length; j++) {
                    var u = usuarios[j];
                    if (usuarios_juegos.some(p => { return p.categoria === "Juegos Retos" && p.id === u.id && p.game_id === juego.id })) {
                        bandera += 1;
                    }
                }
            }
            if (bandera === usuarios.length * juegos_retos.length) {
                bandera = 2;
            } else if (bandera > 0) {
                bandera = 1;
            } else {
                bandera = 0;
            }
        }
        participacion.push({ name: juegos[indice].name, bandera: bandera });
    }
    return participacion;
}

//
//aqui hay otras funciones extras
//

function generarSelectConGrupos() {
    var html = "<option value=''>Todos</option>";
    var select = document.getElementById("grupos")
    for (i = 0; i < grupos.length; i++) {
        var grupo = grupos[i];
        html += "<option value=" + grupo.id + ">" + grupo.name + "</option>"
    }
    select.innerHTML = html;
}

function filtrar() {
    document.getElementById("loader_background").style.display = 'inherit';
    document.getElementById("loader").style.display = 'inherit';
    onDateSelected();
    buscarPorUsuario();
}

function onDateSelected() {
    fecha_inicio = document.getElementById("fecha_inicio").value;
    fecha_fin = document.getElementById("fecha_fin").value;
    if (fecha_inicio !== "" && fecha_fin !== "") {

        obtenerReporteResultados();
        generarGraficaWall();
    }
}

function buscarPorUsuario() {
    nombre_usuario = document.getElementById("nombre_usuario").value;
    if (nombre_usuario !== "") {
        usuarios = usuarios_originales.filter(n => { return n.name.toLowerCase().includes(nombre_usuario.toLowerCase()) });
        usuarios_biblioteca = usuarios_biblioteca_originales.filter(n => { return n.name.toLowerCase().includes(nombre_usuario.toLowerCase()) });
    } else {
        usuarios = JSON.parse(JSON.stringify(usuarios_originales));
        usuarios_biblioteca = JSON.parse(JSON.stringify(usuarios_biblioteca_originales));
    }
    if (usuarios.length === 0 || nombre_usuario === "") {
        obtenerReporteResultados();
        obtenerPostMasComentados();
    } else {
        generarTablas();
    }
}

function buscarPorGrupo() {
    id_grupo = document.getElementById("grupos").value;
    obtenerReporteResultados();
}

function abrirModal(opcion) {
    $('#modal_detalles').modal('show');
    eliminarClases();
    determinarVisible(opcion);
}

function determinarVisible(opcion) {
    switch (opcion) {
        case 1:
            document.getElementById("retroalimentacion_container").classList.add("visible");
            document.getElementById("modal_title").innerHTML = " Retroalimentación"
            break;
        case 2:
            document.getElementById("podcast_container").classList.add("visible");
            document.getElementById("modal_title").innerHTML = " uso de Podcast"
            break;
        case 3:
            document.getElementById("wall_container").classList.add("visible");
            document.getElementById("modal_title").innerHTML = " uso de Muro"
            break;
        case 4:
            document.getElementById("cuestionarios_container").classList.add("visible");
            document.getElementById("modal_title").innerHTML = " participación en Cuestionarios"
            break;
        case 5:
            document.getElementById("juegos_container").classList.add("visible");
            document.getElementById("modal_title").innerHTML = " participación en Juegos"
            break;
        case 6:
            document.getElementById("comunidad_container").classList.add("visible");
            document.getElementById("modal_title").innerHTML = " participación en Comunidad de Aprendizaje"
            break;
    }
}

function eliminarClases() {
    var contenedores = document.getElementsByClassName("detalles");
    for (var i = 0; i < contenedores.length; i++) {
        contenedores[i].classList.remove("visible");
    }
}

function descargarReporteRanking() {
    fecha_inicio = document.getElementById("fecha_inicio").value
    fecha_fin = document.getElementById("fecha_fin").value

    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined

    var url = window.base_url + "Reporte_resultados/DescargarReporteRanking/" + nombre_usuario + "/" + fecha_inicio + "/" + fecha_fin;
    window.open(url, '_blank');
    window.open(url);
}

function descargarReportePodcast() {

    fecha_inicio = document.getElementById("fecha_inicio").value
    fecha_fin = document.getElementById("fecha_fin").value

    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined

    console.log(fecha_inicio)
    console.log(fecha_fin)

    var url = window.base_url + "Reporte_resultados/DescargarReportePodcast/" + nombre_usuario + "/" + fecha_inicio + "/" + fecha_fin;
    window.open(url, '_blank');
    window.open(url);
}

function descargarReporteComunidad() {

    fecha_inicio = document.getElementById("fecha_inicio").value
    fecha_fin = document.getElementById("fecha_fin").value

    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined

    var url = window.base_url + "Reporte_resultados/DescargarReporteComunidad/" + nombre_usuario + "/" + fecha_inicio + "/" + fecha_fin;
    window.open(url, '_blank');
    window.open(url);
}


function descargarReporteJuegos() {
    fecha_inicio = document.getElementById("fecha_inicio").value
    fecha_fin = document.getElementById("fecha_fin").value

    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined

    var url = window.base_url + "Reporte_resultados/DescargarReporteJuegos/" + nombre_usuario + "/" + fecha_inicio + "/" + fecha_fin;
    window.open(url, '_blank');
    window.open(url);
}

var colorName = {
    "aliceblue": 'rgba(240, 248, 255,0.5)',
    "antiquewhite": 'rgba(250, 235, 215,0.5)',
    "aqua": 'rgba(0, 255, 255,0.5)',
    "aquamarine": 'rgba(127, 255, 212,0.5)',
    "azure": 'rgba(240, 255, 255,0.5)',
    "beige": 'rgba(245, 245, 220,0.5)',
    "bisque": 'rgba(255, 228, 196,0.5)',
    "black": 'rgba(0, 0, 0,0.5)',
    "blanchedalmond": 'rgba(255, 235, 205,0.5)',
    "blue": 'rgba(0, 0, 255,0.5)',
    "blueviolet": 'rgba(138, 43, 226,0.5)',
    "brown": 'rgba(165, 42, 42,0.5)',
    "burlywood": 'rgba(222, 184, 135,0.5)',
    "cadetblue": 'rgba(95, 158, 160,0.5)',
    "chartreuse": 'rgba(127, 255, 0,0.5)',
    "chocolate": 'rgba(210, 105, 30,0.5)',
    "coral": 'rgba(255, 127, 80,0.5)',
    "cornflowerblue": 'rgba(100, 149, 237,0.5)',
    "cornsilk": 'rgba(255, 248, 220,0.5)',
    "crimson": 'rgba(220, 20, 60,0.5)',
    "cyan": 'rgba(0, 255, 255,0.5)',
    "darkblue": 'rgba(0, 0, 139,0.5)',
    "darkcyan": 'rgba(0, 139, 139,0.5)',
    "darkgoldenrod": 'rgba(184, 134, 11,0.5)',
    "darkgray": 'rgba(169, 169, 169,0.5)',
    "darkgreen": 'rgba(0, 100, 0,0.5)',
    "darkgrey": 'rgba(169, 169, 169,0.5)',
    "darkkhaki": 'rgba(189, 183, 107,0.5)',
    "darkmagenta": 'rgba(139, 0, 139,0.5)',
    "darkolivegreen": 'rgba(85, 107, 47,0.5)',
    "darkorange": 'rgba(255, 140, 0,0.5)',
    "darkorchid": 'rgba(153, 50, 204,0.5)',
    "darkred": 'rgba(139, 0, 0,0.5)',
    "darksalmon": 'rgba(233, 150, 122,0.5)',
    "darkseagreen": 'rgba(143, 188, 143,0.5)',
    "darkslateblue": 'rgba(72, 61, 139,0.5)',
    "darkslategray": 'rgba(47, 79, 79,0.5)',
    "darkslategrey": 'rgba(47, 79, 79,0.5)',
    "darkturquoise": 'rgba(0, 206, 209,0.5)',
    "darkviolet": 'rgba(148, 0, 211,0.5)',
    "deeppink": 'rgba(255, 20, 147,0.5)',
    "deepskyblue": 'rgba(0, 191, 255,0.5)',
    "dimgray": 'rgba(105, 105, 105,0.5)',
    "dimgrey": 'rgba(105, 105, 105,0.5)',
    "dodgerblue": 'rgba(30, 144, 255,0.5)',
    "firebrick": 'rgba(178, 34, 34,0.5)',
    "floralwhite": 'rgba(255, 250, 240,0.5)',
    "forestgreen": 'rgba(34, 139, 34,0.5)',
    "fuchsia": 'rgba(255, 0, 255,0.5)',
    "gainsboro": 'rgba(220, 220, 220,0.5)',
    "ghostwhite": 'rgba(248, 248, 255,0.5)',
    "gold": 'rgba(255, 215, 0,0.5)',
    "goldenrod": 'rgba(218, 165, 32,0.5)',
    "gray": 'rgba(128, 128, 128,0.5)',
    "green": 'rgba(0, 128, 0,0.5)',
    "greenyellow": 'rgba(173, 255, 47,0.5)',
    "grey": 'rgba(128, 128, 128,0.5)',
    "honeydew": 'rgba(240, 255, 240,0.5)',
    "hotpink": 'rgba(255, 105, 180,0.5)',
    "indianred": 'rgba(205, 92, 92,0.5)',
    "indigo": 'rgba(75, 0, 130,0.5)',
    "ivory": 'rgba(255, 255, 240,0.5)',
    "khaki": 'rgba(240, 230, 140,0.5)',
    "lavender": 'rgba(230, 230, 250,0.5)',
    "lavenderblush": 'rgba(255, 240, 245,0.5)',
    "lawngreen": 'rgba(124, 252, 0,0.5)',
    "lemonchiffon": 'rgba(255, 250, 205,0.5)',
    "lightblue": 'rgba(173, 216, 230,0.5)',
    "lightcoral": 'rgba(240, 128, 128,0.5)',
    "lightcyan": 'rgba(224, 255, 255,0.5)',
    "lightgoldenrodyellow": 'rgba(250, 250, 210,0.5)',
    "lightgray": 'rgba(211, 211, 211,0.5)',
    "lightgreen": 'rgba(144, 238, 144,0.5)',
    "lightgrey": 'rgba(211, 211, 211,0.5)',
    "lightpink": 'rgba(255, 182, 193,0.5)',
    "lightsalmon": 'rgba(255, 160, 122,0.5)',
    "lightseagreen": 'rgba(32, 178, 170,0.5)',
    "lightskyblue": 'rgba(135, 206, 250,0.5)',
    "lightslategray": 'rgba(119, 136, 153,0.5)',
    "lightslategrey": 'rgba(119, 136, 153,0.5)',
    "lightsteelblue": 'rgba(176, 196, 222,0.5)',
    "lightyellow": 'rgba(255, 255, 224,0.5)',
    "lime": 'rgba(0, 255, 0,0.5)',
    "limegreen": 'rgba(50, 205, 50,0.5)',
    "linen": 'rgba(250, 240, 230,0.5)',
    "magenta": 'rgba(255, 0, 255,0.5)',
    "maroon": 'rgba(128, 0, 0,0.5)',
    "mediumaquamarine": 'rgba(102, 205, 170,0.5)',
    "mediumblue": 'rgba(0, 0, 205,0.5)',
    "mediumorchid": 'rgba(186, 85, 211,0.5)',
    "mediumpurple": 'rgba(147, 112, 219,0.5)',
    "mediumseagreen": 'rgba(60, 179, 113,0.5)',
    "mediumslateblue": 'rgba(123, 104, 238,0.5)',
    "mediumspringgreen": 'rgba(0, 250, 154,0.5)',
    "mediumturquoise": 'rgba(72, 209, 204,0.5)',
    "mediumvioletred": 'rgba(199, 21, 133,0.5)',
    "midnightblue": 'rgba(25, 25, 112,0.5)',
    "mintcream": 'rgba(245, 255, 250,0.5)',
    "mistyrose": 'rgba(255, 228, 225,0.5)',
    "moccasin": 'rgba(255, 228, 181,0.5)',
    "navajowhite": 'rgba(255, 222, 173,0.5)',
    "navy": 'rgba(0, 0, 128,0.5)',
    "oldlace": 'rgba(253, 245, 230,0.5)',
    "olive": 'rgba(128, 128, 0,0.5)',
    "olivedrab": 'rgba(107, 142, 35,0.5)',
    "orange": 'rgba(255, 165, 0,0.5)',
    "orangered": 'rgba(255, 69, 0,0.5)',
    "orchid": 'rgba(218, 112, 214,0.5)',
    "palegoldenrod": 'rgba(238, 232, 170,0.5)',
    "palegreen": 'rgba(152, 251, 152,0.5)',
    "paleturquoise": 'rgba(175, 238, 238,0.5)',
    "palevioletred": 'rgba(219, 112, 147,0.5)',
    "papayawhip": 'rgba(255, 239, 213,0.5)',
    "peachpuff": 'rgba(255, 218, 185,0.5)',
    "peru": 'rgba(205, 133, 63,0.5)',
    "pink": 'rgba(255, 192, 203,0.5)',
    "plum": 'rgba(221, 160, 221,0.5)',
    "powderblue": 'rgba(176, 224, 230,0.5)',
    "purple": 'rgba(128, 0, 128,0.5)',
    "rebeccapurple": 'rgba(102, 51, 153,0.5)',
    "red": 'rgba(255, 0, 0,0.5)',
    "rosybrown": 'rgba(188, 143, 143,0.5)',
    "royalblue": 'rgba(65, 105, 225,0.5)',
    "saddlebrown": 'rgba(139, 69, 19,0.5)',
    "salmon": 'rgba(250, 128, 114,0.5)',
    "sandybrown": 'rgba(244, 164, 96,0.5)',
    "seagreen": 'rgba(46, 139, 87,0.5)',
    "seashell": 'rgba(255, 245, 238,0.5)',
    "sienna": 'rgba(160, 82, 45,0.5)',
    "silver": 'rgba(192, 192, 192,0.5)',
    "skyblue": 'rgba(135, 206, 235,0.5)',
    "slateblue": 'rgba(106, 90, 205,0.5)',
    "slategray": 'rgba(112, 128, 144,0.5)',
    "slategrey": 'rgba(112, 128, 144,0.5)',
    "snow": 'rgba(255, 250, 250,0.5)',
    "springgreen": 'rgba(0, 255, 127,0.5)',
    "steelblue": 'rgba(70, 130, 180,0.5)',
    "tan": 'rgba(210, 180, 140,0.5)',
    "teal": 'rgba(0, 128, 128,0.5)',
    "thistle": 'rgba(216, 191, 216,0.5)',
    "tomato": 'rgba(255, 99, 71,0.5)',
    "turquoise": 'rgba(64, 224, 208,0.5)',
    "violet": 'rgba(238, 130, 238,0.5)',
    "wheat": 'rgba(245, 222, 179,0.5)',
    "white": 'rgba(255, 255, 255,0.5)',
    "whitesmoke": 'rgba(245, 245, 245,0.5)',
    "yellow": 'rgba(255, 255, 0,0.5)',
    "yellowgreen": 'rgba(154, 205, 50,0.5)'
};



// ==========================================================================================================
// ==========================================================================================================
// ==========================================================================================================


// generar grafica de muro
function generarGraficaWall() {
    $.ajax({
        url: window.base_url + "Analiticos/ObtenerPostMasComentados",
        type: 'GET',
        data: {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            nombre_usuario: nombre_usuario
        },
        error: function (xhr, error, code) {
            console.log('error', error)
        },
        success: function (json) {
            post_mas_usados = json.data;
            var horizontalBarChartData = JSON.parse(JSON.stringify(getDataWall(json.data, "Interacciones muro", "title", "num_comentarios", "red")))

            var ctx = document.getElementById('wall_chart').getContext('2d');
            if(window.my_podcast_comment_chart){
                window.my_podcast_comment_chart.destroy();
            }else{
                window.my_podcast_comment_chart = createNewChart(ctx, horizontalBarChartData);
            }
        }
    });

}

function getDataWall(data, _label, clave_labels, clave_series, color) {
    var _labels = getLabels(data, clave_labels)
    var _series = getSeries(data, clave_series);
    return {
        datasets: [
            {
                label: ["Interacciones comentarios"],
                backgroundColor: colorName[color],
                borderColor: colorName[color],
                borderWidth: 1,
                data: [post_mas_usados.comentarios.length]
            },
            {
                label: ["Interacciones likes"],
                backgroundColor: "rgba(0, 0, 255, 0.5)",
                borderColor: "rgba(0, 0, 255, 1)",
                borderWidth: 1,
                data: [post_mas_usados.likes.length]
            }
        ]
    }
}


// funcion para descargar un reporte csv de muro
function descargarReporteMuro() {

    fecha_inicio = document.getElementById("fecha_inicio").value
    fecha_fin = document.getElementById("fecha_fin").value

    nombre_usuario = nombre_usuario || undefined
    fecha_inicio = fecha_inicio || undefined
    fecha_fin = fecha_fin || undefined

    var url = window.base_url + "Reporte_resultados/DescargarReporteMuro/" + nombre_usuario + "/" + fecha_inicio + "/" + fecha_fin;
    window.open(url, '_blank');
    window.open(url);
}