<html>
<head>
    <meta charset="utf-8">
    <title>Gettext en codeigniter</title>
</head>
<body>
<select id="select_idioma" class="form-control">
    <option value="">Seleccionar...</option>
    <option value="es_ES">Español</option>
    <option value="en_US">Ingles</option>
    <option value="fr_FR">Frances</option>
</select>
<?php
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 01 mar 2018
 *	Nota: Para cargar los nuevos idiomas o modificarlos, revisar la
 *          carpeta lenguage/locale/idioma/LC_MESSAGES/lang.po, el
 *          archivo se abrira con el programa de
 *          https://poedit.net/download para generar el archivo mo
 ***********************************************************************/
echo _("bienvenido a mi app");
echo "<br>";
echo sprintf(_("saludo usuario %s"),"Juan2");
echo "<br>";
echo sprintf(_("hola %s hoy es %s"),"Juan", date('Y-m-d'));
echo "<br>Pluralización: ";
//primer parámetro indice[0],segundo indice[1], tercero, más de uno plural
//el último reemplaza por el comodín sprintf
echo sprintf(ngettext("producto tienda", "productos tiendas", 1), 1);

?>
<script src="assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
<script>
    $('#select_idioma').change(function () {
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 01 mar 2018
         *	Nota: Se carga el idioma y de nuevo se manda el index pero seteando
         *          el idioma.
         ***********************************************************************/
        $('#body').load('idioma/index_idioma',{idioma:$(this).val()});
    });
</script>
</body>
</html>