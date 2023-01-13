$(document).ready(function () {
    $('.submenu').hide();
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 11 mar 2018
     *	Nota: Se carga por default usuarios en el contenedor
     ***********************************************************************/
    // cargarHtml('admin/Usuarios');
    //por default voy a cargar el dashboard
    console.log(rol_id)
    if (rol_id != 4 && rol_id != 7)
        cargarHtml('admin/ReporteUso');
    else if (rol_id == 7) {
        cargarHtml("admin/DashboardCapacitacion")
    }
    else {
        cargarHtml('admin/Muro')
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 12 mar 2018
     *	Nota: Funcionalidad para submenus
     ***********************************************************************/
    $('.list-unstyled a').on('click', function (e) {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.list-unstyled').first().find('.show').removeClass("show");
        }
        // var $subMenu = $(this).next(".list-unstyled");
        $(this).addClass('show');
    })

    // $('.list-unstyled.collapse > a').on('click', function (e) {
    //     if (!$(this).next().hasClass('show')) {
    //         $(this).parents('.list-unstyled.collapse').first().find('.show').removeClass("show");
    //     }
    //     // var $subMenu = $(this).next(".list-unstyled");
    //     $(this).addClass('show');
    // })


    $('.list-unstyled a.dropdown-toggle').on('click', function (e) {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.list-unstyled').first().find('.show').removeClass("show");
        }
        var $subMenu = $(this).next(".list-unstyled");
        $subMenu.toggleClass('show');


        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
            $('.dropdown-submenu .show').removeClass("show");
        });

        return false;
    });
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });
    $("#select_type_user").change(function () {
        var datos = new FormData();
        datos.append('type_user', $(this).val())
        var config = {
            url: window.base_url + "User/SwitchBusiness",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: datos,
            success: function (response) {
                cargarHtml('admin/Usuarios');
            },
            error: function (response) {
                console.log(response)
                Swal.fire({
                    type: 'error',
                    title: '',
                    text: response.responseJSON.error_msg
                });
            }
        }
        $.ajax(config);
    });

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes
     *		   urisancer@gmail.com
     *	Fecha: 07 de Enero de 2021
     *	Nota: Funcion para cambiar la empresa en sesion para el admin maestro
     ***********************************************************************/
    if (rol_id == 1) {
        $("#session_company_id").change(function () {
            var datos = new FormData();
            datos.append('business_id', $(this).val())
            var config = {
                url: window.base_url + "User/SwitchAdminBusiness",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: datos,
                success: function (response) {
                    document.location.href = window.base_url + 'Admin/inicio';
                },
                error: function (response) {
                    console.log(response)
                    Swal.fire({
                        type: 'error',
                        title: '',
                        text: response.responseJSON.error_msg
                    });
                }
            }
            $.ajax(config);
        });
        CargarCompaniaSesion();
    }

    console.log($("#select_regiones"))
    if ($("#select_regiones") != undefined) {
        obtener_regiones();
    }
});

function obtener_regiones() {
    var config = {
        url: window.base_url + "ws/ObtenerRegiones",
        type: "POST",
        data: {},
        success: function (response) {
            console.log(response)
            generar_select_regiones(response.data)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

function generar_select_regiones(regiones) {
    var select = get("select_regiones")
    if (select) {
        var html = ""
        for (var i = 0; i < regiones.length; i++) {
            html += '<option value="' + regiones[i].id + '">' + regiones[i].nombre + '</option>'
        }
        select.innerHTML = html
        console.log(id_region)
        select.value = id_region
    }
}

function cambiar_region() {
    var id_region = get("select_regiones").value
    var config = {
        url: window.base_url + "User/CambiarGerenteRegion",
        type: "POST",
        data: { id_region: id_region },
        success: function (response) {
            document.location.href = window.base_url + 'Admin/inicio';
            console.log(response)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}


function get(id) {
    return document.getElementById(id)
}

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
$('#select_idioma').change(function () {
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 01 mar 2018
     *	Nota: Se carga el idioma y de nuevo se manda el index pero seteando
     *          el idioma y se carga el idioma en una variable global de
     *          javascript.
     ***********************************************************************/
    window.idioma = $(this).val();
    $('#body').load('admin/index_admin', { idioma: $(this).val() });
});
$('.menu').click(
    function (e) {
        e.preventDefault();
        $(this).closest("li").find("[class^='submenu']").slideToggle();
    }
);

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 02 mar 2018
 *	Nota: Función para cargar vistas para recargar todas las
 *      	dependencias
 ***********************************************************************/
function cargarHtml(file) {
    $('#contenedor_detalle').load(window.base_url + 'index.php/' + file, { idioma: window.idioma });
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/06/2019
 *	Nota: Funcion para cerrar la session
 ***********************************************************************/
function CerrarSesion() {
    var r = confirm("¿Estas seguro que deseas cerrar la sesion?");
    if (r) {
        document.location.href = window.base_url + 'Admin/CerrarSesion'
    }
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 07 de Enero de 2021
 *	Nota: Funcion para cargar el listado de empresas disponibles para el admin maestro
 ***********************************************************************/
function CargarCompaniaSesion() {
    $.ajax({
        url: window.base_url + "Business/BusinessList",
        type: 'POST',
        contentType: false,
        //data: datos,
        processData: false,
        cache: false,
        success: function (json) {
            var html = '<option value="">Seleccionar</option>';
            for (var key in json.data) {
                html += '<option value="' + json.data[key].id + '">' + json.data[key].business_name + '</option>';
            }
            $('#session_company_id').html(html).fadeIn();
            $('#session_company_id').val(empresa_id);
        },
        error: function (error) {
            console.log(error)
        }
    });
}