<?php
require_once './models/OfertaLaboral.php';

class OfertaLaboralController {
    private $conn;
    private $ofertaLaboral;

    public function __construct($db) {
        $this->conn = $db;
        $this->ofertaLaboral = new OfertaLaboral($db);
    }

    private function obtenerRol($usuario_id) {
        $query = "SELECT rol FROM Usuario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? $fila['rol'] : null;
    }

    public function crearOferta($data) {
        if (!isset($data['usuario_id']) || $this->obtenerRol($data['usuario_id']) !== 'Reclutador') {
            http_response_code(403);
            echo json_encode(["mensaje" => "Solo los reclutadores pueden crear ofertas."]);
            return;
        }

        if (!empty($data['titulo']) && !empty($data['reclutador_id'])) {
            $resultado = $this->ofertaLaboral->crear($data);
            if ($resultado) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Oferta creada exitosamente."]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al crear la oferta."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos."]);
        }
    }

    public function obtenerOfertas() {
        $ofertas = $this->ofertaLaboral->obtenerActivas();
        http_response_code(200);
        echo json_encode($ofertas);
    }

    public function actualizarOferta($id, $data) {
        if (!isset($data['usuario_id']) || $this->obtenerRol($data['usuario_id']) !== 'Reclutador') {
            http_response_code(403);
            echo json_encode(["mensaje" => "Solo los reclutadores pueden editar ofertas."]);
            return;
        }

        $resultado = $this->ofertaLaboral->actualizar($id, $data);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Oferta actualizada."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar la oferta."]);
        }
    }

    public function desactivarOferta($id, $data) {
        if (!isset($data['usuario_id']) || $this->obtenerRol($data['usuario_id']) !== 'Reclutador') {
            http_response_code(403);
            echo json_encode(["mensaje" => "Solo los reclutadores pueden desactivar ofertas."]);
            return;
        }

        $resultado = $this->ofertaLaboral->desactivar($id);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Oferta desactivada."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al desactivar la oferta."]);
        }
    }
}
?>