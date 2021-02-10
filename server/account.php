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

$stmt = $con->prepare("SELECT `nome`, `email`, `pontos`, `inscrito`  FROM `utilizadores` WHERE `id` = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
/* bind result variables */
$stmt->bind_result($nome, $email, $pontos, $inscrito);
if ($stmt->fetch()) {
  $stmt->close();
  $stt = $con->prepare("SELECT `id` FROM `utilizadores` ORDER BY `utilizadores`.`pontos` DESC");
  $stt->execute();
  $p = NULL;
  $stt->bind_result($p);
  $i = 0;
  while ($stt->fetch()) {
    $i++;
    if ($p == $uid) {
      break;
    }
  }
  $stt->close();
  $response["sucesso"] = "true";
  $response["nome"]   = $nome;
  $response["email"]   = $email;
  $response["pontos"]   = $pontos;
  $response["posicao"]   = $i;
  $response["inscrito"]   = $inscrito;
  echo json_encode($response);
} else {
  $response["sucesso"] = "false";
  $response["mensagem"]   = "Utilizador não encontrado.";
  echo json_encode($response);
  die();
}