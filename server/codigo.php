<?php
header("Content-Type: application/json; charset=UTF-8");


if (!isset($_POST['token']) || !isset($_POST['uid']) || !isset($_POST['codigo'])) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Por favor preenche todos os campos.');
  echo json_encode($result);
  die();
}

if (strlen($_POST['token']) < 7 || !strlen($_POST['uid']) || !strlen($_POST['codigo'])) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Por favor preenche todos os campos.');
  echo json_encode($result);
  die();
}

require 'db.php';

$token    = filter_var($_POST["token"], FILTER_SANITIZE_STRING);
$uid = filter_var($_POST["uid"], FILTER_SANITIZE_STRING);
$chave = filter_var($_POST["codigo"], FILTER_SANITIZE_STRING);
if (!validateOperation($con, $uid, $token)) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Token inválido.');
  echo json_encode($result);
  die();
}

$stmtt = $con->prepare("SELECT * FROM `chaves_tentativas` WHERE `uid` = ? AND `tempo` > ?");
$tempoagora = time();
$stmtt->bind_param("ii", $uid, $tempoagora);
$stmtt->execute();
$stmtt->store_result();
if ($stmtt->num_rows != 0) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Acabaste de introduzir um código, espera um minuto e tenta outra vez.');
  echo json_encode($result);
  die();
}
$stmtt->close();


$stmt = $con->prepare("SELECT `pontos`, `multipla`, `usada` FROM `chaves` WHERE `chave` = ?;");
$stmt->bind_param("s", $chave);
$stmt->execute();
$prevpontos = 0;
$stmt->bind_result($pontos, $multipla, $usada);
if (!$stmt->fetch()) {
  $stmt->close();
  $result = array('sucesso' => 'false', 'mensagem' => 'O código introduzido não existe.');
  echo json_encode($result);
  $stmt = $con->prepare("INSERT INTO `chaves_tentativas` (`uid`, `tempo`) VALUES (?, ?);");

  $tempoagora += 60;
  $stmt->bind_param("ii", $uid, $tempoagora);
  $stmt->execute();
  $stmt->close();
  die();
}
$stmt->close();

if ($multipla == 0) {
  if ($usada == 0) {
    $stmt = $con->prepare("INSERT INTO `chaves_ativadas` (`uid`, `chave`) VALUES (?, ?);");
    $stmt->bind_param("is", $uid, $chave);
    $stmt->execute();
    $stmt->close();
    $stmt = $con->prepare("UPDATE `chaves` SET `usada` = '1' WHERE `chave` = ?;");
    $stmt->bind_param("s", $chave);
    $stmt->execute();
    $stmt->close();

    $stmt = $con->prepare("SELECT `pontos` FROM `utilizadores` WHERE `id` = ?;");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $prevpontos = 0;
    $stmt->bind_result($prevpontos);
    $stmt->fetch();
    $novospontos = $prevpontos + $pontos;
    $stmt->close();
    $stmt = $con->prepare("UPDATE `utilizadores` SET `pontos` = ? WHERE `id` = ? ;");
    $stmt->bind_param("ii", $novospontos, $uid);
    $stmt->execute();
    $result = array('sucesso' => 'true', 'pontos' => $pontos);
    echo json_encode($result);
    die();
  } else {
    $result = array('sucesso' => 'false', 'mensagem' => 'Este código era de apenas uma utilização e já foi utilizado.');
    echo json_encode($result);
    die();
  }
} else {
  $stmt = $con->prepare("SELECT `id` FROM `chaves_ativadas` WHERE `uid` = ? AND `chave` = ?;");
  $stmt->bind_param("is", $uid, $chave);
  $stmt->execute();
  if ($stmt->fetch()) {
    $result = array('sucesso' => 'false', 'mensagem' => 'Já introduziste este código!');
    echo json_encode($result);
    die();
  } else {
    $stmt->close();

    $stmt = $con->prepare("INSERT INTO `chaves_ativadas` (`uid`, `chave`) VALUES (?, ?);");
    $stmt->bind_param("is", $uid, $chave);
    $stmt->execute();
    $stmt->close();

    $stmt = $con->prepare("SELECT `pontos` FROM `utilizadores` WHERE `id` = ?;");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $prevpontos = 0;
    $stmt->bind_result($prevpontos);
    $stmt->fetch();
    $novospontos = $prevpontos + $pontos;
    $stmt->close();
    $stmt = $con->prepare("UPDATE `utilizadores` SET `pontos` = ? WHERE `id` = ? ;");
    $stmt->bind_param("ii", $novospontos, $uid);
    $stmt->execute();
    $result = array('sucesso' => 'true', 'pontos' => $pontos);
    echo json_encode($result);
  }
}