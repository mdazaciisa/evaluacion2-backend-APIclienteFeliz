<?php
require_once './models/Usuario.php';

class UsuarioController {
    private $conn;
    private $usuario;

    public function __construct($db) {
        $this->conn = $db;
        $this->usuario = new Usuario($db);
    }

    public function obtenerUsuario($id) {
        $usuario = $this->usuario->obtenerUsuario($id);
        if ($usuario) {
            echo json_encode($usuario);
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "Usuario no encontrado."]);
        }
    }

    public function obtenerUsuarios() {
        $usuarios = $this->usuario->obtenerUsuarios();
        http_response_code(200);
        echo json_encode(["data" => $usuarios]);
    }

    public function insertarUsuario($data) {
        // Verificar que $data es un array
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Los datos deben estar en formato JSON válido."]);
            return;
        }
        
        // Verificar campos obligatorios
        if (empty($data['nombre']) || empty($data['apellido']) || empty($data['email']) || empty($data['contraseña']) || empty($data['rol'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos. Por favor incluir nombre, apellido, contraseña, email y rol."]);
            return;
        }
    
        // Validar el rol
        $rolesPermitidos = ['Reclutador', 'Candidato'];
        if (!in_array($data['rol'], $rolesPermitidos)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "El rol debe ser 'Reclutador' o 'Candidato'."]);
            return;
        }
        
        // Llamar al método para insertar el usuario
        $resultado = $this->usuario->insertarUsuario($data);
        if ($resultado) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Usuario ingresado correctamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear usuario."]);
        }
    }
    
    

    public function actualizarUsuario($id, $data) {
        $resultado = $this->usuario->actualizarUsuario($id, $data);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Usuario actualizado correctamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar datos de usuario."]);
        }
    }

    public function eliminarUsuario($id) {
        $usuario = $this->usuario->obtenerUsuario($id);
        if ($usuario) {
            $resultado = $this->usuario->eliminarUsuario($id);
            if ($resultado) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Usuario eliminado correctamente."]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al eliminar el usuario."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "Usuario no encontrado para eliminar."]);
        }
    }

    public function actualizarParcialUsuario($id, $data) {
        $usuarioExistente = $this->usuario->obtenerUsuario($id);
        if (!$usuarioExistente) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Usuario no encontrado."]);
            return;
        }

        $resultado = $this->usuario->actualizarParcialUsuario($id, $data);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Usuario actualizado parcialmente."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar parcialmente datos de usuario."]);
        }
    }
}
?>