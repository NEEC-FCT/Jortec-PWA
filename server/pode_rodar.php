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

require 'roda.php';

$pode = podeRodar($con, $uid);

$result = array('sucesso' => 'true', 'roda' => $pode ? 'true' : 'false');
echo json_encode($result);