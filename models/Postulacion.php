<?php
class Postulacion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear($data) {
        try {
            $query = "INSERT INTO Postulacion (candidato_id, oferta_laboral_id, estado_postulacion, comentario)
                      VALUES (:candidato_id, :oferta_laboral_id, :estado_postulacion, :comentario)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':candidato_id', $data['candidato_id']);
            $stmt->bindParam(':oferta_laboral_id', $data['oferta_laboral_id']);
            $stmt->bindParam(':estado_postulacion', $data['estado_postulacion']);
            $stmt->bindParam(':comentario', $data['comentario']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear postulación: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorUsuario($candidato_id) {
        try {
            $query = "SELECT * FROM Postulacion WHERE candidato_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $candidato_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener postulaciones: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarEstado($id, $estado, $comentario) {
        try {
            $query = "UPDATE Postulacion SET estado_postulacion = :estado, comentario = :comentario WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':comentario', $comentario);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de postulación: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarPostulacion($id) {
        try {
            $query = "DELETE FROM Postulacion WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar postulación: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPostulacion($id, $data) {
        try {
            $query = "UPDATE Postulacion SET estado_postulacion = :estado_postulacion, comentario = :comentario WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':estado_postulacion', $data['estado_postulacion']);
            $stmt->bindParam(':comentario', $data['comentario']);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar postulación: " . $e->getMessage());
            return false;
        }
    }    
}