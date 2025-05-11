<?php
require_once './models/AntecedenteAcademico.php';

class AntecedenteAcademicoController {
    private $conn;
    private $antecedenteAcademico;

    public function __construct($db) {
        $this->conn = $db;
        $this->antecedenteAcademico = new AntecedenteAcademico($db);
    }

    public function crearAntecedente($data) {
        if (!isset($data['usuario_id']) || $this->obtenerRol($data['usuario_id']) !== 'Candidato') {
            http_response_code(403);
            echo json_encode(["mensaje" => "Solo los candidatos pueden registrar antecedentes académicos."]);
            return;
        }

        if (!empty($data['institucion']) && !empty($data['titulo_obtenido'])) {
            $resultado = $this->antecedenteAcademico->crear($data);
            if ($resultado) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Antecedente académico registrado."]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al registrar antecedente académico."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos."]);
        }
    }

    public function obtenerAntecedentes($candidato_id) {
        $antecedentes = $this->antecedenteAcademico->obtenerPorCandidato($candidato_id);
        http_response_code(200);
        echo json_encode($antecedentes);
    }

    public function actualizarAntecedente($id, $data) {
        $resultado = $this->antecedenteAcademico->actualizar($id, $data);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Antecedente académico actualizado."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar antecedente académico."]);
        }
    }
    
    public function eliminarAntecedente($id) {
        $resultado = $this->antecedenteAcademico->eliminar($id);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Antecedente académico eliminado."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar antecedente académico."]);
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