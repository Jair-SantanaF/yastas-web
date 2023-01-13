jQuery(document).ready(function ($) {
    obtenerUsuariosRegistrados(2);
    obtenerPostMasComentados(2);
    obtenerPostMasGustados(2);
    obtenerPodcastMasComentados(2);
    obtenerUsuariosMasActivos(2);
    elementos = document.getElementsByTagName("select");
    for (i = 0; i < elementos.length; i++) {
        elementos[i].value = 2;
    }
});

function obtenerUsuariosRegistrados(tipo) {
    $.ajax({
        url: window.base_url + "Analiticos/ObtenerCantidadUsuarios",
        type: 'GET',
        data: {
            tipo: tipo
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(error)
        },
        success: function (json) {
            var data = json.data;
            var config = {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            data[0].usuarios_registrados, data[1].invitados
                        ],
                        backgroundColor: [
                            colorName.blue,
                            colorName.skyblue,
                        ],
                        label: 'Dataset 1'
                    }],
                    labels: [
                        'Usuarios Registrados',
                        'Invitados'
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            };

            var ctx = document.getElementById('chart-area').getContext('2d');
            window.myPie = new Chart(ctx, config);
        }
    })
}

function obtenerPostMasComentados(tipo) {
    $.ajax({
        url: window.base_url + "Analiticos/ObtenerPostMasComentados",
        type: 'GET',
        data: {
            tipo: tipo
        },
        error: function (xhr, error, code) {
            console.log(error)
        },
        success: function (json) {
            var horizontalBarChartData = JSON.parse(JSON.stringify(getData(json.data, "Comentarios", "wall_description", "num_comentarios", "darkgreen")));

            var ctx = document.getElementById('post_comments').getContext('2d');
            if (window.my_post_comments_chart) window.my_post_comments_chart.destroy();
            window.my_post_comments_chart = createNewChart(ctx, horizontalBarChartData);
        }
    })
}

function obtenerPostMasGustados(tipo) {
    $.ajax({
        url: window.base_url + "Analiticos/ObtenerPostMasGustados",
        type: 'GET',
        data: {
            tipo: tipo
        },
        error: function (xhr, error, code) {
            console.log(error)
        },
        success: function (json) {
            var horizontalBarChartData = JSON.parse(JSON.stringify(getData(json.data, "Likes", "wall_description", "num_likes", "cyan")))

            var ctx = document.getElementById('post_likes').getContext('2d');

            if (window.my_post_likes_chart) window.my_post_likes_chart.destroy();

            window.my_post_likes_chart = createNewChart(ctx, horizontalBarChartData);

        }
    })
}



function obtenerPodcastMasComentados(tipo) {
    $.ajax({
        url: window.base_url + "Analiticos/ObtenerPodcastMasComentados",
        type: 'GET',
        data: {
            tipo: tipo
        },
        error: function (xhr, error, code) {
            console.log(error)
        },
        success: function (json) {

            var horizontalBarChartData = JSON.parse(JSON.stringify(getData(json.data, "Comentarios", "title", "num_comentarios", "red")))

            var ctx = document.getElementById('podcast_comments').getContext('2d');
            if (window.my_podcast_comment_chart) my_podcast_comment_chart.destroy();
            window.my_podcast_comment_chart = createNewChart(ctx, horizontalBarChartData);

        }
    })
}

function obtenerUsuariosMasActivos(tipo) {
    $.ajax({
        url: window.base_url + "Analiticos/ObtenerUsuariosMasActivos",
        type: 'GET',
        data: {
            tipo: tipo
        },
        error: function (xhr, error, code) {
            console.log(error)
            console.log(xhr)
            console.log(code)
        },
        success: function (json) {
            console.log(json.data)
            json.data = ordenarPorMasInteraccion(json.data);
            var _labels = getLabels(json.data, "name");
            var _likes_wall = getSeries(json.data, "num_likes_wall");
            var _comentarios_en_muro = getSeries(json.data, "num_comentarios_wall")
            var _comentarios_en_podcast = getSeries(json.data, "num_comentarios_podcast");
            var _comentarios_en_feedback = getSeries(json.data, "num_comentarios_feedback");

            var barChartData = {
                labels: _labels,
                datasets: [{
                    label: 'Likes en muro',
                    backgroundColor: colorName.blue,
                    data: _likes_wall
                }, {
                    label: 'Comentarios en muro',
                    backgroundColor: colorName.green,
                    data: _comentarios_en_muro
                }, {
                    label: 'Comentarios en podcast',
                    backgroundColor: colorName.yellow,
                    data: _comentarios_en_podcast
                }, {
                    label: 'Comentarios en feedback',
                    backgroundColor: colorName.darkturquoise,
                    data: _comentarios_en_feedback
                }]
            };

            var ctx = document.getElementById('usuarios_activos').getContext('2d');
            if (window.myBar) window.myBar.destroy();
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    title: {
                        display: false,
                        text: ''
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            stacked: true,
                            ticks: {
                                callback: function (value, index, values) {
                                    return value.substring(0, 10) + "...";
                                }
                            }
                        }],
                        yAxes: [{
                            stacked: true
                        }]
                    }
                }
            });
        }
    })
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

function ordenarPorMasInteraccion(data) {
    for (i = 0; i < data.length; i++) {
        for (j = 0; j < data.length - i - 1; j++) {
            var a = data[j]
            var b = data[j + 1]
            if (a.total_interacciones < b.total_interacciones) {
                data[j] = b
                data[j + 1] = a
            }
        }
    }
    return data;
}

function actualizarGrafica(seleccionado, id) {
    switch (id) {
        case 1:
            obtenerUsuariosRegistrados(seleccionado);
            break;
        case 2:
            obtenerPostMasComentados(seleccionado)
            break;
        case 3:
            obtenerPostMasGustados(seleccionado)
            break;
        case 4:
            obtenerPodcastMasComentados(seleccionado)
            break;
        case 5:
            obtenerUsuariosMasActivos(seleccionado)
            break;
    }
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

function prueba(){
    $.ajax({
        url: window.base_url + "Library/SetVisto",
        type: 'POST',
        data: {
           id : 8,
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(error)
            console.log(xhr)
        },
        success: function (json) {
            console.log(json)
        }
    })
}