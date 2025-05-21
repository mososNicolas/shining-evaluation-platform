<?php
session_start();
require_once("../../includes/db.php");

if ($_SESSION['rol'] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit(json_encode(['success' => false, 'error' => 'Acceso no autorizado']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participante_id = intval($_POST['participante_id'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';
    $jurado_id = isset($_POST['jurado_id']) ? intval($_POST['jurado_id']) : null;
    
    if ($participante_id <= 0 || !in_array($tipo, ['ingles', 'musica'])) {
        header("HTTP/1.1 400 Bad Request");
        exit(json_encode(['success' => false, 'error' => 'Parámetros inválidos']));
    }
    
    try {
        if ($tipo === 'ingles') {
            $sql = "DELETE FROM calificaciones_ingles WHERE participante_id = ?";
            if ($jurado_id) {
                $sql .= " AND jurado_id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ii", $participante_id, $jurado_id);
            } else {
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("i", $participante_id);
            }
        } else { // musica
            $sql = "DELETE FROM calificaciones_musica WHERE participante_id = ?";
            if ($jurado_id) {
                $sql .= " AND jurado_id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ii", $participante_id, $jurado_id);
            } else {
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("i", $participante_id);
            }
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'affected_rows' => $stmt->affected_rows]);
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

header("HTTP/1.1 400 Bad Request");
echo json_encode(['success' => false, 'error' => 'Método no permitido']);
?>