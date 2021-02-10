<?php
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_POST['token']) || !isset($_POST['uid'])) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Por favor preenche todos os campos.');
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

$stmt = $con->prepare("SELECT `inscrito`  FROM `utilizadores` WHERE `id` = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
/* bind result variables */
$stmt->bind_result($inscrito);
if ($stmt->fetch()) {
  $stmt->close();
  if ($inscrito == 1) {
    $novo_inscrito = 0;
  } else {
    $novo_inscrito = 1;
  }
  $stt = $con->prepare("UPDATE `utilizadores` SET `inscrito` = ? WHERE `id` = ?");
  $stt->bind_param("ii", $novo_inscrito, $uid);
  if ($stt->execute()) {
    $stt->close();
    $response["sucesso"] = "true";
    $response["inscrito"]   = $novo_inscrito;
    echo json_encode($response);
    die();
  }
}
$response["sucesso"] = "false";
$response["mensagem"]   = "Erro na inscricao.";
echo json_encode($response);
die();