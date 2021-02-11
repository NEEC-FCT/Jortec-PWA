<?php
header("Content-Type: application/json; charset=UTF-8");

require 'db.php';

$stmt = $con->prepare("SELECT * FROM `workshops` LIMIT 6");
$stmt->execute();
/* bind result variables */
$stmt->bind_result($wid, $inscritos, $anunciado);
$result;
while ($stmt->fetch()) {
  $work = array(
    "wid" => "ws" . $wid
  );
  if ($anunciado == 0) {
    $work["naoAnunciado"] = "true";
  }
  if ($inscritos >= 25) {
    $work["cheio"] = "true";
  }
  $result[] = $work;
}
$stmt->close();

if (!isset($_POST['token']) || !isset($_POST['uid'])) {
  echo json_encode($result);
  die();
}

if (strlen($_POST['token']) < 7 || !strlen($_POST['uid'])) {
  echo json_encode($result);
  die();
}

$token    = filter_var($_POST["token"], FILTER_SANITIZE_STRING);
$uid = filter_var($_POST["uid"], FILTER_SANITIZE_STRING);

if (!validateOperation($con, $uid, $token)) {
  echo json_encode($result);
  die();
}


$stmt = $con->prepare("SELECT `wid` FROM `inscricoes` WHERE `uid` = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$stmt->bind_result($wsid);
while ($stmt->fetch()) {
  $result[$wsid - 1]["inscrito"] = "true";
}
$stmt->close();


echo json_encode($result);