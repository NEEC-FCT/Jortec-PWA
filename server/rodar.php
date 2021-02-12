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
$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
$ip = filter_var($ip, FILTER_SANITIZE_STRING);
if (!validateOperation($con, $uid, $token)) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Token inválido.');
  echo json_encode($result);
  die();
}

require 'roda.php';

if (!podeRodar($con, $uid)) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Já rodaste hoje!');
  echo json_encode($result);
} else {
  $today = date("Y-m-d");
  $valor = rand(1, 8) * 5;
  $stmt = $con->prepare("INSERT INTO `roda` (`uid`, `data`, `valor`, `ip`) VALUES (?, ?, ?, ?);");
  $stmt->bind_param("isis", $uid, $today, $valor, $ip);
  $deu = $stmt->execute();
  $stmt->close();
  if (!$deu) {
    $result = array('sucesso' => 'false', 'mensagem' => 'Erro ao rodar.');
    echo json_encode($result);
  } else {
    //get prev pontos
    $stmt = $con->prepare("SELECT `pontos` FROM `utilizadores` WHERE `id` = ?;");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $prevpontos = 0;
    $stmt->bind_result($prevpontos);
    $stmt->fetch();
    $novospontos = $prevpontos + $valor;
    $stmt->close();
    $stmt = $con->prepare("UPDATE `utilizadores` SET `pontos` = ? WHERE `id` = ? ;");
    $stmt->bind_param("ii", $novospontos, $uid);
    $stmt->execute();

    $result = array('sucesso' => 'true', 'mensagem' => 'Roda rodada.', 'valor' => $valor);
    echo json_encode($result);
  }
}