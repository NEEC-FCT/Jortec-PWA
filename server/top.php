<?php
header("Content-Type: application/json; charset=UTF-8");

require 'db.php';
//SELECT `pontos`, `nome` FROM `utilizadores` WHERE `nome` NOT LIKE '%@%' ORDER BY `utilizadores`.`pontos` DESC, `id` ASC LIMIT 10
$stt = $con->prepare("SELECT `pontos`, `nome` FROM `utilizadores` ORDER BY `utilizadores`.`pontos` DESC, `id` ASC  LIMIT 10");
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