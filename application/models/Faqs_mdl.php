<?php
class Faqs_mdl extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Library_mdl', 'library');
        $this->load->model('Podcast_mdl', 'podcast');
    }


    public function buscar_en_resumen($busqueda, $user_id, $business_id, $id_categoria)
    {
        //$b = $this->preparar_busqueda($busqueda);
        /* $query = "select * from extractos
        where (etiquetas REGEXP ? or resumen REGEXP ?) and id_categoria = ?";
        $result = $this->db->query($query, array($b, $b, $id_categoria))->result_array(); */
        $query = null;
        if($busqueda || $id_categoria && $id_categoria != 1000000){
            $busqueda = "%".$busqueda."%";
            $query = "select * from extractos";
        }
        if($busqueda || $id_categoria && $id_categoria != 1000000){
            $query .= " where";
        }
        if($busqueda){
            $query .= " etiquetas LIKE ?";
            if($id_categoria && $id_categoria != 1000000){
                $query .= " and id_categoria = ?";
            }
        }else if($id_categoria && $id_categoria != 1000000){
            $query .= " id_categoria = ?";
        }
        if($busqueda && $id_categoria && $id_categoria != 1000000){
            $result = $this->db->query($query, array($busqueda, $id_categoria))->result_array();
        }else if($busqueda){
            $result = $this->db->query($query, array($busqueda))->result_array();
        }else if($id_categoria && $id_categoria != 1000000){
            $result = $this->db->query($query, array($id_categoria))->result_array();
        }
        if(!$query){
            $result = [];
        }
        /* función para comprobar que no se repitan elementos de libreria */
        $result = $this->ordenar($result, $busqueda);
        $ids = $this->obtener_ids($result);
        $data = [];
        $data["user_id"] = $user_id;
        $data["business_id"] = $business_id;

        $data["region_id"] = null;
        $data["asesor_id"] = null;
        $data["category_id"] = $id_categoria;
        $elementos = [];
        $elementos["library"] = [];
        $elementos["podcast"] = [];
        if (strlen($ids["library"]) > 0) {
            /* si hay en extractos */
            $data["ids_faqs_str"] = $ids["library"];
            $data["ids_faqs"] = explode(",", $ids["library"]);
        }
        if(array_key_exists("ids_faqs", $data) || $id_categoria){
            /* si hay por extractos o categoria */
            $elementos["library"] = $this->library->list_library_app($data);
            if(!$elementos["library"]){
                $elementos["library"] = [];
            }
        }
        /* buscar elementos igual por categoria */
        if (count($ids["podcast"]) > 0) {
            /* require_once 'application/models/Podcast_mdl.php';
            $podcast_mdl = new Podcast_mdl(); */
            $elementos["podcast"] = $this->podcast->ListPodcast($business_id, $user_id, false, $ids["podcast"]);
        }
        return $elementos;
    }

    function buscar_en_preguntas($busqueda, $id_categoria, $business_id)
    {
        //$b = $this->preparar_busqueda($busqueda);
        $b = $busqueda;
        $result = false;
        $query = "select id, pregunta, respuesta, id_categoria, concat('" . URL_FAQS . "business_" . $business_id . "/faqs/',imagen) as imagen from preguntas_frec";
        if($b || $id_categoria){
            $query .= " where";
        }
        if($b){
            $query .= " (etiquetas REGEXP ? or respuesta REGEXP ?)";
            if($id_categoria){
                $query .= " and id_categoria = ?";
            }
        }else if($id_categoria){
            $query .= " id_categoria = ?";
        }
        if($b && $id_categoria){
            $result = $this->db->query($query, array($b, $b, $id_categoria))->result_array();
        }else if($b){
            $result = $this->db->query($query, array($b, $b))->result_array();
        }else if($id_categoria){
            $result = $this->db->query($query, array($id_categoria))->result_array();
        }
        if($result){
            return $this->ordenar($result, $busqueda);
        }else{
            return [];
        }
    }

    function preparar_busqueda($busqueda)
    {
        $preposiciones = array(" a ", " ante ", " bajo ", " cabe ", " con ", " contra ", " de ", " desde ", " durante ", " en ", " entre ", " hacia ", " hasta ", " mediante ", " para ", " por ", " segun ", " sin ", " sobre ", " tras ", " versus ", " via ");
        $articulos = array(" el ", " la ", " las ", " los ");
        $busqueda = str_replace($preposiciones, "", $busqueda);
        $busqueda = str_replace($articulos, "", $busqueda);
        return $this->formatear_busqueda($busqueda);
    }

    function obtener_ids($result)
    {
        $ids = [];
        $ids["library"] = [];
        $ids["podcast"] = [];
        for ($i = 0; $i < count($result); $i++) {
            if ($result[$i]["tipo"] == 1){
                /* obtener elemntos solo no obligatarios con nel mismo nombre (por el tema de duplicados) que fueron creados primero directamen en admin bibloteca */
                $query = "SELECT id, title, capacitacion_obligatoria, active 
                FROM library_elements_ 
                WHERE title = '".$result[$i]["resumen"]."' 
                AND capacitacion_obligatoria = 0
                ORDER BY date ASC";
                $resultado = $this->db->query($query)->result_array();
                if(!empty($resultado)){
                    array_push($ids["library"], $resultado[0]["id"]);
                }
            }
            else{
                array_push($ids["podcast"], $result[$i]["library_id"]);
            }
        }
        $ids["library"] = join(",", $ids["library"]);
        /* $ids["podcast"] = join(",", $ids["podcast"]); */
        return $ids;
    }

    function formatear_busqueda($busqueda)
    {
        $busqueda = explode(" ", $busqueda);
        $busqueda =  join(" | ", $busqueda);
        $busqueda = " " . $busqueda . " "; //para que solo considere palabras completas
        return $busqueda;
    }

    function ordenar($result, $busqueda)
    {
        $busqueda = explode(" ", $busqueda);

        for ($i = 1; $i < count($result); $i++) {
            for ($j = 0; $j < (count($result) - $i); $j++) {
                if(array_key_exists("resumen", $result[$j])){
                    $a = $this->numero_coincidencias($busqueda, $result[$j]["resumen"]) + $this->numero_coincidencias($busqueda, $result[$j]["etiquetas"]);
                    $b = $this->numero_coincidencias($busqueda, $result[$j + 1]["resumen"]) + $this->numero_coincidencias($busqueda, $result[$j + 1]["etiquetas"]);
                    if ($a < $b) {
                        $temp = $result[$j];
                        $result[$j + 1]["coincidencias"] = $b;
                        $temp["coincidencias"] = $a;
                        $result[$j] = $result[$j + 1];
                        $result[$j + 1] = $temp;
                    }
                }
            }
        }
        return $result;
    }

    function numero_coincidencias($busqueda, $texto)
    {
        $texto = strtolower($texto);
        $texto = explode(" ", $texto);
        $cant = 0;
        for ($i = 0; $i < count($busqueda); $i++) {
            if (in_array(strtolower(str_replace(",", "", $busqueda[$i])), $texto) && strlen($busqueda[$i]) > 1)
                $cant++;
        }
        return $cant;
    }

    function obtener_categorias()
    {
        //$query = "select * from categorias_faqs";
        $query = "select * from library_category where business_id = 18 and active = 1;";
        $result = $this->db->query($query)->result_array();
        $resultParse = []; 
        foreach($result as $key => $obj){
           /*  if($result[$key]['nombre'] == 'Operación'){
                $result[$key]['nombre'] = 'Mi negocio';
            }
            else if($result[$key]['nombre'] == 'Capacitación'){
                $result[$key]['nombre'] = 'Mi punto Yastás';
            }
            else if($result[$key]['nombre'] == 'Terminal punto de venta'){
                $result[$key]['nombre'] = 'Productos';
            }
            else{
                unset($result[$key]);
            } */
            $array = array(
                "id" => $result[$key]["id"],
                "nombre" => $result[$key]["name"]
            );
            array_push($resultParse, $array);
        }
        /* agregar categoria manual para obtener mejores calificados */
        array_push($resultParse, ["id" => 1000000, "nombre" => "Mejor calificados"]); //solo debe salir en la app
        return $resultParse = array_values($resultParse);
    }

    function obtener_carrusel($business_id, $id_pregunta)
    {
        $query = "select id, orden,concat('" . URL_FAQS . "business_" . $business_id . "/faqs/',imagen) as imagen
        from carrusel_faqs where id_pregunta = ? order by orden";
        return $this->db->query($query, array($id_pregunta))->result_array();
    }

    function actualizar($data, $id_elemento, $tipo)
    {
        if ($this->comprobar_extracto($id_elemento, $tipo)) {
            $this->db->where("library_id", $id_elemento);
            $this->db->where("tipo", $tipo);
            return $this->db->update("extractos", $data);
        } else {
            $data["library_id"] = $id_elemento;
            $data["tipo"] = $tipo;
            return $this->db->insert("extractos", $data);
        }
    }

    function comprobar_extracto($id_elemento, $tipo)
    {
        $query = "select id from extractos where library_id = ? and tipo = ?";
        $result =  $this->db->query($query, array($id_elemento, $tipo))->result_array();
        if (count($result) > 0)
            return true;
        return false;
    }

    function obtener_preguntas($business_id)
    {
        $query = "select p.id, p.pregunta, p.respuesta,p.etiquetas, p.id_categoria, c.name as categoria,
         concat('" . URL_FAQS . "business_" . $business_id . "/faqs/',imagen) as imagen
          from preguntas_frec as p
          join library_category as c on c.id = p.id_categoria";
        $result = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["carrusel"] = $this->obtener_carrusel($business_id, $result[$i]["id"]);
        }
        return $result;
    }

    function guardar_pregunta($data)
    {
        $insert = $this->db->insert("preguntas_frec", $data);
        if($insert){
            return $insert_id = $this->db->insert_id();
        }else{
            return false;
        }
    }

    function eliminar_pregunta($id_pregunta)
    {
        $this->db->where("id", $id_pregunta);
        return $this->db->delete("preguntas_frec");
    }

    function actualizar_pregunta($data, $id_pregunta)
    {
        $this->db->where("id", $id_pregunta);
        return $this->db->update("preguntas_frec", $data);
    }

    function eliminar_imagen_carrusel($id_imagen)
    {
        $this->db->where("id", $id_imagen);
        return $this->db->delete("carrusel_faqs");
    }

    function agregar_imagen_carrusel($data)
    {
        $query = "SELECT count(*) as preguntas FROM carrusel_faqs WHERE id_pregunta = '".$data["id_pregunta"]."'";
        $count_preguntas = $this->db->query($query)->result_array();
        $data['orden'] = $count_preguntas[0]["preguntas"] + 1;
        return $this->db->insert("carrusel_faqs", $data);
    }

    function actualizar_orden_imagenes($id, $orden)
    {
       /*  for ($i = 0; $i < count($data); $i++) {
            $this->db->where("id", $data[$i]["id"]);
            $this->db->set("orden", $data[$i]["orden"]);
            $this->db->update("carrusel_faqs");
        }
        return true; */
        /* buscar imagen carrusel id */
        $query = "SELECT * FROM carrusel_faqs WHERE id = '".$id."'";
        $imagen_carrusel = $this->db->query($query)->result_array();
        /* obtener imagenes relacionadas al carrusel */
        if(count($imagen_carrusel) > 0){
            $query = "SELECT * FROM carrusel_faqs WHERE id_pregunta = '".$imagen_carrusel[0]['id_pregunta']."'";
            $imagenes_carrusel = $this->db->query($query)->result_array();
            /* comprobar si esta libre el orden que se solicito */
            if($orden != null || $orden != ''){
                $query = "SELECT * FROM carrusel_faqs 
                WHERE id_pregunta = '".$imagen_carrusel[0]['id_pregunta']."'
                AND orden = '".$orden."'
                ";
                $imagenes_carrusel_orden = $this->db->query($query)->result_array();
                if(count($imagenes_carrusel_orden) > 0){
                    /* invertire ordenes de resultado */
                    $query = "UPDATE carrusel_faqs SET orden = '".$imagen_carrusel[0]['orden']."' WHERE id = '".$imagenes_carrusel_orden[0]['id']."'";
                    $imagen_carrusel_anterior = $this->db->query($query);
                }
                /* actualizar orden */
                $query = "UPDATE carrusel_faqs SET orden = '".$orden."' WHERE id = '".$id."'"; 
                $imagen_carrusel_nuevo = $this->db->query($query);
            }else{
                return false;
            }
        }
        return $imagen_carrusel_nuevo;
    }

    function obtener_ultimo_carrusel($business_id){
        $query = "select p.id, p.pregunta, p.respuesta,p.etiquetas, p.id_categoria, c.name as categoria,
        concat('" . URL_FAQS . "business_" . $business_id . "/faqs/',p.imagen) as imagen from preguntas_frec as p
        join carrusel_faqs as ca on ca.id_pregunta = p.id
        join library_category as c on c.id = p.id_categoria
        order by p.id desc
        limit 1";
        $result = $this->db->query($query)->result_array();
        if(count($result) > 0){
            $id_pregunta = $result[0]["id"];
            $result[0]["carrusel"] =  $this->obtener_carrusel($business_id, $id_pregunta);
            return $result[0];
        }
        return [];
    }

}
