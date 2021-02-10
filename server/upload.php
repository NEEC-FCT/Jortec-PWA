<?php
header("Content-Type: application/json; charset=UTF-8");
if (!isset($_POST['token']) || !isset($_POST['uid']) || !isset($_FILES['cv']['name'])) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Por favor preenche todos os campos.2');
  echo json_encode($result);
  die();
}

if (strlen($_POST['token']) < 7 || !strlen($_POST['uid'])) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Por favor preenche todos os campos.');
  echo json_encode($result);
  die();
}

require 'db.php';

$token    = filter_var($_POST["token"], FILTER_SANITIZE_STRING);
$uid = filter_var($_POST["uid"], FILTER_SANITIZE_STRING);

if (!validateOperation($con, $uid, $token)) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Token inválido.');
  echo json_encode($result);
  die();
}

$target_dir = "uploads/pastaCVs/";
$target_file = $target_dir . $uid . '.pdf';
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


// Check file size
if ($_FILES["cv"]["size"] > 20000000) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Ficheiro muito grande.');
  echo json_encode($result);
  die();
}

// Check if $uploadOk is set to 0 by an error

if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
  $result = array('sucesso' => 'true', 'mensagem' => 'Ficheiro enviado com sucesso');
  echo json_encode($result);
  die();
} else {
  $result = array('sucesso' => 'false', 'mensagem' => 'Ocorreu um erro ao enviar o ficheiro');
  echo json_encode($result);
  die();
}