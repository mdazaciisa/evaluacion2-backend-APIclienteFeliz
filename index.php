<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

require_once './config/database.php';
require_once './controllers/AntecedenteAcademicoController.php';
require_once './controllers/AntecedenteLaboralController.php';
require_once './controllers/OfertaLaboralController.php';
require_once './controllers/PostulacionController.php';
require_once './controllers/UsuarioController.php';

//conexión base de datos
$database = new Database();
$db = $database->getConnection();

//instancias de controladores
$AntecedenteAcademicoController = new AntecedenteAcademicoController($db);
$AntecedenteLaboralController = new AntecedenteLaboralController($db);
$OfertaLaboralController = new OfertaLaboralController($db);
$PostulacionController = new PostulacionController($db);
$UsuarioController = new UsuarioController($db);

//obtener método http (get, post, put, delete)
$method = $_SERVER['REQUEST_METHOD'];

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $path);
$id = null;
$entidad = null;

//id en url amigable(ej: localhost/usuario/2)
if (isset($segments[2]) && is_numeric($segments[2])) {
    $id = $segments[2];
}

//id en url no amigable (ej: localhost/usuario/index.php?id=2)
if (!$id && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
}

//obtener entidad/tabla
if (isset($segments[1])) {
    $entidad = $segments[1];
}

//switch para manejar las operaciones HTTP
switch ($entidad) {
    case 'usuario':
    case 'Usuario':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $UsuarioController->obtenerUsuario($id);
                } else {
                    $UsuarioController->obtenerUsuarios();
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Error en el formato JSON proporcionado."]);
                    break;
                }
                $UsuarioController->insertarUsuario($data);
                break;
            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    if ($data === null) {
                        http_response_code(400);
                        echo json_encode(["mensaje" => "Error en el formato JSON proporcionado."]);
                        break;
                    }
                    $UsuarioController->actualizarUsuario($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para actualizar."]);
                }
                break;
            case 'PATCH':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    if ($data === null) {
                        http_response_code(400);
                        echo json_encode(["mensaje" => "Error en el formato JSON proporcionado."]);
                        break;
                    }
                    $UsuarioController->actualizarParcialUsuario($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para actualizar parcialmente."]);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $UsuarioController->eliminarUsuario($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para eliminar."]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido"]);
                break;
        }
        break;
    case 'postulacion':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $PostulacionController->obtenerPostulacionesDeUsuario($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID del candidato para consultar sus postulaciones."]);
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Error en el formato JSON proporcionado."]);
                    break;
                }
                $PostulacionController->crearPostulacion($data);
                break;
            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $PostulacionController->actualizarEstadoPostulacion($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para actualizar el estado."]);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $PostulacionController->eliminarPostulacion($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para eliminar la postulación."]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido para postulación"]);
                break;
        }
        break;

    case 'ofertalaboral':
        switch ($method) {
            case 'GET':
                $OfertaLaboralController->obtenerOfertas();
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                $OfertaLaboralController->crearOferta($data);
                break;
            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $OfertaLaboralController->actualizarOferta($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para actualizar la oferta."]);
                }
                break;
            case 'PATCH':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $OfertaLaboralController->desactivarOferta($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para desactivar la oferta."]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido para oferta laboral"]);
                break;
        }
        break;

    case 'antecedenteacademico':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $AntecedenteAcademicoController->obtenerAntecedentes($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID del candidato."]);
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                $AntecedenteAcademicoController->crearAntecedente($data);
                break;
            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $AntecedenteAcademicoController->actualizarAntecedente($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para actualizar el antecedente."]);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $AntecedenteAcademicoController->eliminarAntecedente($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para eliminar el antecedente académico."]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido para antecedente académico"]);
                break;
        }
        break;

    case 'antecedentelaboral':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $AntecedenteLaboralController->obtenerAntecedentes($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID del candidato."]);
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                $AntecedenteLaboralController->crearAntecedente($data);
                break;
            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $AntecedenteLaboralController->actualizarAntecedente($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para actualizar el antecedente."]);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $AntecedenteLaboralController->eliminarAntecedente($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Se requiere ID para eliminar el antecedente laboral."]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido para antecedente laboral"]);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["mensaje" => "Recurso no encontrado"]);
        break;
}
