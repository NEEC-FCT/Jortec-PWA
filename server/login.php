<?php
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_POST['email']) || !isset($_POST['senha'])) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Por favor preenche todos os campos.');
  echo json_encode($result);
  die();
}

if (strlen($_POST['email']) < 7 || strlen($_POST['senha']) < 4) {
  $result = array('sucesso' => 'false', 'mensagem' => 'Por favor preenche todos os campos.');
  echo json_encode($result);
  die();
}

require 'db.php';

$email    = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
$password = filter_var($_POST["senha"], FILTER_SANITIZE_STRING);
$stmt = $con->prepare("SELECT `id`, `password`  FROM `utilizadores` WHERE `email` = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
/* bind result variables */
$stmt->bind_result($id, $senha);
if ($stmt->fetch()) {
  $stmt->close();
  if (password_verify($password, $senha)) {
    $response["sucesso"] = "true";
    $response["uid"]   = $id;
    $response["token"]   = generateToken($con, $id);
    echo json_encode($response);
  } else {
    $response["sucesso"] = "false";
    $response["mensagem"]   = "Email ou senha incorretos.";
    echo json_encode($response);
  }
} else {
  $response["sucesso"] = "false";
  $response["mensagem"]   = "Email ou senha incorretos.";
  echo json_encode($response);
  die();
}