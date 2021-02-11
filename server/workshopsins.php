<?php
header("Content-Type: application/json; charset=UTF-8");


if (!isset($_POST['token']) || !isset($_POST['uid']) || !isset($_POST['wid']) || !isset($_POST['inscricao'])) {
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
$wid = filter_var($_POST["wid"], FILTER_SANITIZE_STRING);
$inscricao  = filter_var($_POST["inscricao"], FILTER_SANITIZE_STRING);

if (!validateOperation($con, $uid, $token)) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Token inválido.');
  echo json_encode($result);
  die();
}

$id = intval(substr($wid, 2));
$stmt = $con->prepare("SELECT * FROM `workshops` WHERE `id` = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
/* bind result variables */
$stmt->bind_result($wid, $inscritos, $anunciado);

if ($stmt->fetch()) {
  if ($inscritos >= 25 && $inscricao == "true") {
    $result = array('sucesso' => 'false', 'mensagem' => 'Workshop cheio.');
    echo json_encode($result);
    die();
  }
} else {
  $result = array('sucesso' => 'false', 'mensagem' => 'Erro ao verificar estado do workshop');
  echo json_encode($result);
  die();
}
$stmt->close();
$delta = 0;

if ($inscricao == "true") {
  $stmt = $con->prepare("INSERT INTO `inscricoes` (`uid`, `wid`) VALUES (?, ?);");
  $delta = 1;
} else {
  $stmt = $con->prepare("DELETE FROM `inscricoes` WHERE `uid` = ? AND `wid` = ?;");
  $delta = -1;
}
$stmt->bind_param("ii", $uid, $id);
if ($stmt->execute()) {
  $stmt->close();
  $stmt = $con->prepare("UPDATE `workshops` SET `inscritos` = ? WHERE `id` = ?;");
  $novos = $inscritos + $delta;
  $stmt->bind_param("ii", $novos, $id);
  $stmt->execute();
  $result = array('sucesso' => 'true');
  echo json_encode($result);
  die();
}

$result = array('sucesso' => 'false', 'mensagem' => 'Erro ao inscrever/desinscrever');
echo json_encode($result);
die();