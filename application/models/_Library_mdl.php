<?php
class Library_mdl extends CI_Model
{
    private $tableLibraryCategory = "library_category",
        $tableSubcategory = "library_subcategory",
        $tableLibraryElements = "library_elements_",
        $tableLibraryImages = "library_images",
        $tableQuizQuestions = "question_quiz",
        $tableUsersGroups = "users_groups",
        $table_groups = "groups",
        $tableLibraryGroups = "library_groups",
        $tablePodcasts = "podcast";

    function __construct()
    {
        parent::__construct();
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una categoría de elementos de la biblioteca
     ***********************************************************************/
    function SaveCategory($data){
        $dataa = array(
            "name" => $data["name"],
            "active" => 1,
            "business_id" => $data["business_id"]
        );

        if($this->db->insert($this->tableLibraryCategory, $dataa)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una categoría de elementos de la biblioteca
     ***********************************************************************/
    function EditCategory($data){
        $key = array('id' => $data["id"]);

        $dataa = array(
            "name" => $data["name"]
        );

        if($this->db->update($this->tableLibraryCategory, $dataa, $key)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un área de elementos de la biblioteca
     ***********************************************************************/
    function DeleteCategory($data){
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if($this->db->update($this->tableLibraryCategory, $dataa, $key)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener las categorias de biblioteca
     ***********************************************************************/
    function ListCategories($business_id){
        $this->db->select('id, name');
        $this->db->from($this->tableLibraryCategory);
        $this->db->where('business_id = ', $business_id);
        $this->db->where('active = ', 1);
        $this->db->order_by('order', 'ASC');

        $categories = $this->db->get()->result_array();
        if(count($categories) > 0){
            return $categories;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar un área de elementos de la biblioteca
     ***********************************************************************/
    function SaveSubcategory($data){
        $data['active'] = 1;
        if($this->db->insert($this->tableSubcategory, $data)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar un área de elementos de la biblioteca
     ***********************************************************************/
    function EditSubcategory($data){
        $key = array('id' => $data["id"]);
        unset($data['id']);
        if($this->db->update($this->tableSubcategory, $data, $key)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un área de elementos de la biblioteca
     ***********************************************************************/
    function DeleteSubcategory($data){
        $key = array('id' => $data["id"]);
        unset($data['id']);
        if($this->db->update($this->tableSubcategory, array('active' => 0), $key)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener las areas registradas por empresa.
     ***********************************************************************/
    function ListSubcategory($business_id,$category_id =''){
        $this->db->select('s.id, s.subcategory, s.category_id, a.name as category');
        $this->db->from($this->tableSubcategory.' s');
        $this->db->join($this->tableLibraryCategory.' a', 's.category_id = a.id');
        $this->db->where('s.business_id = ', $business_id);
        if($category_id !== ''){
            $this->db->where('s.category_id = ', $category_id);
        }
        $this->db->where('s.active = ', 1);
        $this->db->order_by('s.order', 'ASC');
        $categories = $this->db->get()->result_array();
        if(count($categories) > 0){
            return $categories;
        }else{
            return false;
        }
    }

    function savePodcast($data){
        $dataa = array(
            "title" => $data["title"],
            "description" => $data["description"],
            "preview" => $data["preview"],
            "type" => $data["type"],
            "audio" => $data["audio"],
            "duration" => $data["duration"],
            "business_id" => $data["business_id"],
            "date" => date("Y-m-d H:i:s")
        );

        if($this->db->insert($this->tablePodcasts, $dataa)){
            return true;
        }   else    {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una categoría de elementos de la biblioteca
     ***********************************************************************/
    function SaveElement($data){
        $dataa = array(
            'title' => $data["title"],
            'text' => $data["text"],
            'category_id' => $data["category_id"],
            'subcategory_id' => $data["subcategory_id"],

            'type' => $data["type"],
            //'file' => $data["file"],
            'link' => $data["link"],
            'image' => $data["image"],
            'type_video' => $data["type_video"],
            'question' => $data["question"],
            //'video' => $data["video"],

            "business_id" => $data["business_id"],
            "active" => 1,
            "date" => date("Y-m-d H:i:s")
        );

        if($data['file'] != '')
            $dataa['file'] = $data['file'];
        if($data['video'] != '')
            $dataa['video'] = $data['video'];

        if($this->db->insert($this->tableLibraryElements, $dataa)){
            $id_library = $this->db->insert_id();
            $this->db->update($this->tableQuizQuestions, array('connection_id'=>$id_library),array('id'=>$data['question']));
            return true;
        }else{
            return false;
        }
    }

    function editPodcast($data) {
        $key = array('id' => $data["id"]);

        $dataa = array(
			'title' => $data["title"],
			'description' => $data["description"]
        );

        if($this->db->update($this->tablePodcasts, $dataa, $key)){
            return true;
        }   else    {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una categoría de elementos de la biblioteca
     ***********************************************************************/
    function EditElement($data){
        $key = array('id' => $data["id"]);

        $dataa = array(
			'title' => $data["title"],
			'text' => $data["text"],
			'category_id' => $data["category_id"],
			'subcategory_id' => $data["subcategory_id"],
            'type' => $data["type"],
			//'file' => $data["file"],
			'link' => $data["link"],
            'type_video' => $data["type_video"],
            'question' => $data["question"]//,
			//'video' => $data["video"]
        );
        if($data["image"] !== ''){
            $dataa["image"] = $data["image"];
        }

        if($data['file'] != '')
            $dataa['file'] = $data['file'];
        if($data['video'] != '')
            $dataa['video'] = $data['video'];

        if($this->db->update($this->tableLibraryElements, $dataa, $key)){
            $this->db->update($this->tableQuizQuestions, array('connection_id'=>$data["id"]),array('id'=>$data['question']));
            return true;
        }else{
            return false;
        }
    }

    function deletePodcast($data){
        $key = array('id' => $data["id"]);
        if($this->db->delete($this->tablePodcasts, $key))   {
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un área de elementos de la biblioteca
     ***********************************************************************/
    function DeleteElement($data){
        $key = array('id' => $data["id"]);
        if($this->db->update($this->tableLibraryElements, array('active' => 0), $key)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los registros de la biblioteca registrados
     ***********************************************************************/
    function ListLibrary($data){
        if(isset($_SESSION['id_user'])) {
            $this->db->select('
                e.id, e.title, e.text, e.type, e.image, e.file, e.link, e.video, e.type_video, e.category_id,
                ifnull(e.subcategory_id,0) as subcategory_id, c.name as name_category, 
                ifnull(s.subcategory,"") as subcategory, ifnull(e.question,if(e.question="",0,e.question)) as question');
            $this->db->from($this->tableLibraryElements . ' as e');
            $this->db->join($this->tableLibraryCategory . ' as c', 'e.category_id = c.id');
            $this->db->join($this->tableSubcategory . ' as s', 's.id = e.subcategory_id','left');
            $this->db->where('e.active =', 1);
            $this->db->where('e.business_id = ', $data['business_id']);
            if (isset($data['category_id']) && $data['category_id'] !== '') {
                $this->db->where('e.category_id = ', $data['category_id']);
            }
            if (isset($data['subcategory_id']) && $data['subcategory_id'] !== '') {
                $this->db->where('e.subcategory_id = ', $data['subcategory_id']);
            }
            if (isset($data['id']) && $data['id'] !== '') {
                $this->db->where('e.id = ', $data['id']);
            }
            $library = $this->db->get()->result_array();
            if (count($library) > 0) {
                /***********************************************************************
                 *    Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
                 *           mario.martinez.f@hotmail.es
                 *    Nota: Recorremos la libreria para obtener las rutas del pdf y el
                 *          detalle de las imagenes
                 ***********************************************************************/
                foreach ($library as $index => $value) {
                    $library[$index]['video_id'] = '';
                    if ($value['image'] !== '') {
                        $library[$index]['image'] = base_url('uploads/business_'.$data['business_id'].'/library/') . $value['image'];
                    }
                    switch ($value['type_video']) {
                        case 'servidor':
                            $library[$index]['video'] = base_url('uploads/business_'.$data['business_id'].'/library/') . $value['video'];
                            break;
                        case 'youtube':
                            $library[$index]['video_id'] = $value['video'];
                            $library[$index]['video'] = 'https://youtu.be/' . $value['video'];
                            break;
                        case 'vimeo':
                            $library[$index]['video_id'] = $value['video'];
                            $library[$index]['video'] = 'https://player.vimeo.com/video/' . $value['video'];
                            break;
                        default:
                    }
                    if ($value['file'] !== '') {
                        if (!filter_var($value['file'], FILTER_VALIDATE_URL)) {
                            $library[$index]['file'] = base_url('uploads/business_'.$data['business_id'].'/library/') . $value['file'];
                        }

                    }
                }
                return $library;
            } else {
                return false;
            }
        }else{
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/3/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Validamos que el usuarios tenga un grupo asignado si no tiene
             *          un grupo asignado no podra ver nada en elearning
             ***********************************************************************/
            $this->db->select('ug.group_id');
            $this->db->from($this->tableUsersGroups.' as ug');
            $this->db->join($this->table_groups.' as g','ug.group_id = g.id');
            $this->db->where('ug.user_id',$data['user_id']);
            $this->db->where('g.active',1);
            $this->db->where('ug.active',1);
            $group = $this->db->get()->result_array();
            if(count($group) > 0) {
                $group = implode(',', array_map(function ($string) {

                    return $string['group_id'];

                }, $group));
                $this->db->select('e.id, e.title, e.text, e.type, e.image, e.file, e.link, e.video, e.type_video, e.category_id, ifnull(e.subcategory_id,0) as subcategory_id, c.name as name_category, ifnull(s.subcategory,"") as subcategory, if(e.question="",0,e.question) as question');
                $this->db->from($this->tableLibraryElements.' as e');
                $this->db->join($this->tableLibraryCategory.' as c', 'e.category_id = c.id');
                $this->db->join($this->tableSubcategory.' as s', 's.id = e.subcategory_id','left');
                $this->db->join($this->tableLibraryGroups.' lg', 'e.id = lg.library_id and lg.group_id in ('.$group.')');
                $this->db->where('e.active =', 1);
                $this->db->where('e.business_id = ',$data['business_id']);
                if(isset($data['category_id']) && $data['category_id'] !== ''){
                    $this->db->where('e.category_id = ',$data['category_id']);
                }
                if(isset($data['subcategory_id']) && $data['subcategory_id'] !== ''){
                    $this->db->where('e.subcategory_id = ',$data['subcategory_id']);
                }
                if (isset($data['id']) && $data['id'] !== '') {
                    $this->db->where('e.id = ', $data['id']);
                }
                $library = $this->db->get()->result_array();
                if(count($library) > 0){
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: Recorremos la libreria para obtener las rutas del pdf y el
                     *          detalle de las imagenes
                     ***********************************************************************/
                    foreach ($library as $index => $value){
                        $library[$index]['video_id'] = '';
                        if($value['image'] !== ''){
                            $library[$index]['image'] = base_url('uploads/business_'.$data['business_id'].'/library/').$value['image'];
                        }
                        switch ($value['type_video']){
                            case 'servidor':
                                $library[$index]['video'] = base_url('uploads/business_'.$data['business_id'].'/library/').$value['video'];
                                break;
                            case 'youtube':
                                $library[$index]['video_id'] = $value['video'];
                                $library[$index]['video'] = 'https://youtu.be/'.$value['video'];
                                break;
                            case 'vimeo':
                                break;
                            default:
                        }
                        if($value['file'] !== ''){
                            if ( !filter_var($value['file'], FILTER_VALIDATE_URL)) {
                                $library[$index]['file'] = base_url('uploads/business_'.$data['business_id'].'/library/').$value['file'];
                            }

                        }
                    }
                    return $library;
                }else{
                    return false;
                }
            }
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los catalogos de preguntas que se le
     *          pueden asignar a un elemento de biblioteca
     ***********************************************************************/
    function QuizLibrary($data){
        $this->db->select('id, name, connection_id');
        $this->db->from($this->tableQuizQuestions);
        $this->db->where('business_id =', $data['business_id']);
        $this->db->where('active =', 1);
        $this->db->where('category_id =', QUIZ_CATEGORY_LIBRARY);
        $this->db->where('connection_id =', 0);
        if(isset($data['library_id'])){
            $this->db->or_where('connection_id =', $data['library_id']);
        }
        return $this->db->get()->result_array();
    }
}
