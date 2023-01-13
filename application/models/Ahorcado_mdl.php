<?php
class Ahorcado_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function obtenerFrase($business_id)
    {
        $this->db->select("*");
        $this->db->from("game_ahorcado");
        $this->db->where("business_id",$business_id);
        $this->db->order_by("RAND()");
        $this->db->limit(1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $arreglo = str_split($result[0]["frase"]);
            $claves = "";
            $ocultas = explode(',', $result[0]["ocultas"]);
            for ($i = 0; $i < count($arreglo); $i++) {
                $claves .= $arreglo[$i];
                if (in_array($arreglo[$i], $ocultas)) {
                    $claves .= "_";
                } else {
                }
            }
            unset($result["ocultas"]);
            $seleccionables = $ocultas;
            $j = 0;
            $abc = "abcdefghijklmnopqrstuvwxyz";
            $abc = str_split($abc);

            for ($i = 0; $i < count($abc); $i++) {
                $indice = random_int(0, 25);
                if (!in_array($abc[$indice], $seleccionables) && $j < 5) {
                    array_push($seleccionables, $abc[$indice]);
                    $j++;
                }
            }
            $result[0]["ocultas"] = $ocultas;
            shuffle($seleccionables);
            $result[0]["seleccionables"] = $seleccionables;
        }
        return $result;
    }

    function guardarPuntos($id_usuario, $id_frase, $puntos)
    {
        $this->db->select("*");
        $this->db->from("game_ahorcado_users");
        $this->db->where("id_usuario", $id_usuario);
        $this->db->where("id_frase", $id_frase);
        $result = $this->db->get()->result_array();
        $this->general_mdl->ModificarScoreUsuario($id_usuario, $puntos);
        if (count($result) > 0) {
            $puntos_anteriores = $result[0]["puntos"];
            $puntos_anteriores += $puntos;
            $this->db->set("puntos", $puntos_anteriores);
            $this->db->where("id", $result[0]["id"]);
            return $this->db->update("game_ahorcado_users");
        } else {
            $data = [];
            $data["id_usuario"] = $id_usuario;
            $data["id_frase"] = $id_frase;
            $data["puntos"] = $puntos;
            return $this->db->insert("game_ahorcado_users", $data);
        }
    }

    function obtenerFrases($business_id)
    {
        $this->db->select("*");
        $this->db->from("game_ahorcado");
        $this->db->where("business_id",$business_id);
        return $this->db->get()->result_array();
    }

    function guardarFrase($data)
    {
        return $this->db->insert("game_ahorcado", $data);
    }

    function editarFrase($data)
    {
        $this->db->set("frase",$data["frase"]);
        $this->db->set("ocultas",$data["ocultas"]);
        $this->db->where("id",$data["id"]);
        return $this->db->update("game_ahorcado");
    }

    function eliminarFrase($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete("game_ahorcado");
    }
}
