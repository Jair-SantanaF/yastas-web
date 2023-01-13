/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para redireccionar a su vista
 ***********************************************************************/
function Redirect(func){
    $(location).attr('href',window.base_url+'app/'+func);
}