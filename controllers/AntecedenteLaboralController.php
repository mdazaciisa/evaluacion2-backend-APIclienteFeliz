<?php
require_once './models/AntecedenteLaboral.php';

class AntecedenteLaboralController {
    private $conn;
    private $antecedenteLaboral;

    public function __construct($db) {
        $this->conn = $db;
        $this->antecedenteLaboral = new AntecedenteLaboral($db);
    }

    public function crearAntecedente($data) {
        if (!isset($data['usuario_id']) || $this->obtenerRol($data['usuario_id']) !== 'Candidato') {
            http_response_code(403);
            echo json_encode(["mensaje" => "Solo los candidatos pueden registrar antecedentes laborales."]);
            return;
        }

        if (!empty($data['empresa']) && !empty($data['cargo'])) {
            $resultado = $this->antecedenteLaboral->crear($data);
            if ($resultado) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Antecedente laboral registrado."]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al registrar antecedente laboral."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos."]);
        }
    }

    public function obtenerAntecedentes($candidato_id) {
        $antecedentes = $this->antecedenteLaboral->obtenerPorCandidato($candidato_id);
        http_response_code(200);
        echo json_encode($antecedentes);
    }

    public function actualizarAntecedente($id, $data) {
        $resultado = $this->antecedenteLaboral->actualizar($id, $data);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Antecedente laboral actualizado."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar antecedente laboral."]);
        }
    }
    
    public function eliminarAntecedente($id) {
        $resultado = $this->antecedenteLaboral->eliminar($id);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Antecedente laboral eliminado."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar antecedente laboral."]);
        }
    }

    private function obtenerRol($usuario_id) {
        $query = "SELECT rol FROM Usuario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? $fila['rol'] : null;
    }
}
?>