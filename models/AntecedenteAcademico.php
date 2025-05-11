<?php
class AntecedenteAcademico {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear($data) {
        try {
            $query = "INSERT INTO AntecedenteAcademico (candidato_id, institucion, titulo_obtenido, anio_ingreso, anio_egreso)
                      VALUES (:candidato_id, :institucion, :titulo_obtenido, :anio_ingreso, :anio_egreso)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':candidato_id', $data['candidato_id']);
            $stmt->bindParam(':institucion', $data['institucion']);
            $stmt->bindParam(':titulo_obtenido', $data['titulo_obtenido']);
            $stmt->bindParam(':anio_ingreso', $data['anio_ingreso']);
            $stmt->bindParam(':anio_egreso', $data['anio_egreso']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear antecedente académico: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorCandidato($id) {
        try {
            $query = "SELECT * FROM AntecedenteAcademico WHERE candidato_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener antecedentes académicos: " . $e->getMessage());
            return [];
        }
    }

    public function actualizar($id, $data) {
        try {
            $query = "UPDATE AntecedenteAcademico SET institucion = :institucion, titulo_obtenido = :titulo_obtenido,
                      anio_ingreso = :anio_ingreso, anio_egreso = :anio_egreso WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':institucion', $data['institucion']);
            $stmt->bindParam(':titulo_obtenido', $data['titulo_obtenido']);
            $stmt->bindParam(':anio_ingreso', $data['anio_ingreso']);
            $stmt->bindParam(':anio_egreso', $data['anio_egreso']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar antecedente académico: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $query = "DELETE FROM AntecedenteAcademico WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar antecedente académico: " . $e->getMessage());
            return false;
        }
    }
}