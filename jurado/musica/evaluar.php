<?php
session_start();
require_once("../../includes/db_connection.php");

if ($_SESSION['rol'] !== 'jurado') {
    header("Location: ../../login.php");
    exit;
}

$id_jurado = $_SESSION['id'];
$area = $_SESSION['area'];
$participante_id = $_POST['participante_id'];

$stmt = $conn->prepare("INSERT INTO calificaciones 
(participante_id, jurado_id, pronunciacion, fluidez, vocabulario, afinacion, proyeccion_vocal, interpretacion) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$pronunciacion = $_POST['pronunciacion'] ?? null;
$fluidez = $_POST['fluidez'] ?? null;
$vocabulario = $_POST['vocabulario'] ?? null;
$afinacion = $_POST['afinacion'] ?? null;
$proyeccion_vocal = $_POST['proyeccion_vocal'] ?? null;
$interpretacion = $_POST['interpretacion'] ?? null;

$stmt->bind_param("iiiiiiii", $participante_id, $id_jurado, $pronunciacion, $fluidez, $vocabulario, $afinacion, $proyeccion_vocal, $interpretacion);
$stmt->execute();

if ($area === 'ingles') {
    header("Location: ingles/dashboard.php");
} else {
    header("Location: musica/dashboard.php");
}
exit;
