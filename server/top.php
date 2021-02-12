<?php
header("Content-Type: application/json; charset=UTF-8");

require 'db.php';

$stt = $con->prepare("SELECT `pontos`, `nome` FROM `utilizadores` ORDER BY `utilizadores`.`pontos` DESC LIMIT 10");
$stt->execute();
$stt->bind_result($pontos, $nome);
$response;
while ($stt->fetch()) {
  $one['nome'] = $nome;
  $one['pontos'] = $pontos;
  $response[] = $one;
}
$stt->close();

echo json_encode($response);