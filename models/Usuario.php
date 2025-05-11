<?php
class Usuario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;        
    }

    public function obtenerUsuario($id) {
        try {
            $query = "SELECT * FROM Usuario WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por id: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerUsuarios() {
        try {
            $query = "SELECT id, nombre, apellido, email, contraseña, fecha_nacimiento, telefono, direccion, rol, fecha_registro, estado FROM Usuario";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as &$usuario) {
                // Comprobar campos vacíos y asignar valor por defecto
                foreach ($usuario as $key => $value) {
                    if (empty($value) && $value !== 0) {
                        $usuario[$key] = 'NO DISPONIBLE';
                    }
                }
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener datos de usuario: " . $e->getMessage());
            return [];
        }
    }

    public function insertarUsuario($data) {
        try {
            $query = "INSERT INTO Usuario (nombre, apellido, email, contraseña, fecha_nacimiento, telefono, direccion, rol) 
                      VALUES (:nombre, :apellido, :email, :password, :fecha_nacimiento, :telefono, :direccion, :rol)";
            $stmt = $this->conn->prepare($query);
   
            // Asegúrate de que estas variables sean definidas y sean variables
            $nombre = $data['nombre'];
            $apellido = $data['apellido'];
            $email = $data['email'];
            $password = $data['contraseña'];
            $fecha_nacimiento = isset($data['fecha_nacimiento']) ? $data['fecha_nacimiento'] : null;
            $telefono = isset($data['telefono']) ? $data['telefono'] : null;
            $direccion = isset($data['direccion']) ? $data['direccion'] : null;
            $rol = $data['rol'];
    
            // Vincular parámetros
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':rol', $rol);
    
            if ($stmt->execute()) {
                return true;
            } else {
                error_log("Error en la ejecución de la consulta: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error PDO al crear usuario: " . $e->getMessage());
            return false;
        }
    }
    
    
    
    

    public function actualizarUsuario($id, $data) {
        try {
            if (!empty($data['nombre']) && !empty($data['apellido']) && !empty($data['email']) && !empty($data['rol'])) {
                $query = "UPDATE Usuario SET 
                          nombre = :nombre, 
                          apellido = :apellido, 
                          email = :email";
                
                // Añadir campos opcionales a la consulta si existen
                if (isset($data['fecha_nacimiento'])) {
                    $query .= ", fecha_nacimiento = :fecha_nacimiento";
                }
                if (isset($data['telefono'])) {
                    $query .= ", telefono = :telefono";
                }
                if (isset($data['direccion'])) {
                    $query .= ", direccion = :direccion";
                }
                if (isset($data['estado'])) {
                    $query .= ", estado = :estado";
                }
                
                $query .= ", rol = :rol WHERE id = :id";
                
                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':rol', $data['rol']);
                
                // Bind de parámetros opcionales
                if (isset($data['fecha_nacimiento'])) {
                    $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
                }
                if (isset($data['telefono'])) {
                    $stmt->bindParam(':telefono', $data['telefono']);
                }
                if (isset($data['direccion'])) {
                    $stmt->bindParam(':direccion', $data['direccion']);
                }
                if (isset($data['estado'])) {
                    $stmt->bindParam(':estado', $data['estado']);
                }

                return $stmt->execute();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar datos de usuario: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizarParcialUsuario($id, $data) {
        try {
            $setClauses = [];
            $params = [':id' => $id];

            foreach ($data as $clave => $valor) {
                $setClauses[] = "$clave = :$clave";
                $params[":$clave"] = $valor;
            }

            if (empty($setClauses)) {
                return true;
            }

            $query = "UPDATE Usuario SET " . implode(', ', $setClauses) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            foreach ($params as $param => $valor) {
                $stmt->bindValue($param, $valor);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar parcialmente el usuario: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarUsuario($id) {
        try {
            $query = "DELETE FROM Usuario WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>