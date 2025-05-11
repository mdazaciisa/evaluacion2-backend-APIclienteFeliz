<?php
class OfertaLaboral {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear($data) {
        try {
            $query = "INSERT INTO OfertaLaboral (titulo, descripcion, ubicacion, salario, tipo_contrato, reclutador_id)
                      VALUES (:titulo, :descripcion, :ubicacion, :salario, :tipo_contrato, :reclutador_id)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':ubicacion', $data['ubicacion']);
            $stmt->bindParam(':salario', $data['salario']);
            $stmt->bindParam(':tipo_contrato', $data['tipo_contrato']);
            $stmt->bindParam(':reclutador_id', $data['reclutador_id']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear oferta laboral: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerActivas() {
        try {
            $query = "SELECT * FROM OfertaLaboral WHERE estado = 'Vigente'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener ofertas activas: " . $e->getMessage());
            return [];
        }
    }

    public function actualizar($id, $data) {
        try {
            $query = "UPDATE OfertaLaboral SET titulo = :titulo, descripcion = :descripcion,
                      ubicacion = :ubicacion, salario = :salario, tipo_contrato = :tipo_contrato
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':ubicacion', $data['ubicacion']);
            $stmt->bindParam(':salario', $data['salario']);
            $stmt->bindParam(':tipo_contrato', $data['tipo_contrato']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar oferta laboral: " . $e->getMessage());
            return false;
        }
    }

    public function desactivar($id) {
        try {
            $query = "UPDATE OfertaLaboral SET estado = 'Baja' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al desactivar oferta: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $query = "DELETE FROM OfertaLaboral WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar oferta laboral: " . $e->getMessage());
            return false;
        }
    }    
}