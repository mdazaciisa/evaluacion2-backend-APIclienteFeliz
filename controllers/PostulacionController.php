<?php
require_once './models/Postulacion.php';

class PostulacionController {
    private $conn;
    private $postulacion;

    public function __construct($db) {
        $this->conn = $db;
        $this->postulacion = new Postulacion($db);
    }

    public function crearPostulacion($data) {
        if (!isset($data['usuario_id']) || $this->obtenerRol($data['usuario_id']) !== 'Candidato') {
            http_response_code(403);
            echo json_encode(["mensaje" => "Solo los candidatos pueden postular."]);
            return;
        }

        if (!empty($data['candidato_id']) && !empty($data['oferta_laboral_id'])) {
            $data['estado_postulacion'] = 'Postulando';
            $data['comentario'] = $data['comentario'] ?? '';
            $resultado = $this->postulacion->crear($data);
            if ($resultado) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Postulación creada."]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al crear postulación."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos."]);
        }
    }

    public function actualizarEstadoPostulacion($id, $data) {
        if (!isset($data['usuario_id']) || $this->obtenerRol($data['usuario_id']) !== 'Reclutador') {
            http_response_code(403);
            echo json_encode(["mensaje" => "Solo reclutadores pueden cambiar el estado."]);
            return;
        }

        if (!empty($data['estado_postulacion']) && isset($data['comentario'])) {
            $resultado = $this->postulacion->actualizarEstado($id, $data['estado_postulacion'], $data['comentario']); // Llamar al método correspondiente
            if ($resultado) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Estado actualizado."]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al actualizar estado."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos."]);
        }
    }

    public function obtenerPostulacionesDeUsuario($candidato_id) {
        $resultado = $this->postulacion->obtenerPorUsuario($candidato_id);
        http_response_code(200);
        echo json_encode($resultado);
    }

    private function obtenerRol($usuario_id) {
        $query = "SELECT rol FROM Usuario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? $fila['rol'] : null;
    }

    public function actualizarPostulacion($id, $data) {
        $resultado = $this->postulacion->actualizarPostulacion($id, $data);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Postulación actualizada."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar postulación."]);
        }
    }
    
    public function eliminarPostulacion($id) {
        $resultado = $this->postulacion->eliminarPostulacion($id);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Postulación eliminada."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar postulación."]);
        }
    }
}
?>
