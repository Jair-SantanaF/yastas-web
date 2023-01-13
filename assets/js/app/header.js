/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para enviar la peticion para iniciar la sesion
 ***********************************************************************/
function CerrarSesion() {
    Swal.fire({
        title: 'NUUP',
        text: "¿Estas seguro que deseas cerrar sesión?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $(location).attr('href',window.base_url+'app/CloseSession');
        }
    })
}
