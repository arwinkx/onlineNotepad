<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Tehran'); 

$file = "notes.json";
if (!file_exists($file)) file_put_contents($file, "[]");

$notes = json_decode(file_get_contents($file), true);
$action = $_REQUEST['action'] ?? '';

switch ($action) {
  case 'read':
    echo json_encode($notes);
    break;

  case 'add':
    $text = trim($_POST['text'] ?? '');
    if ($text !== "") {
      $date = date('Y-m-d H:i:s');
      $id = bin2hex(random_bytes(6));
      $notes[] = ["text" => htmlspecialchars($text), "date" => $date, "id" => $id];
      file_put_contents($file, json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    echo json_encode(["status" => "ok"]);
    break;

  case 'edit':
    $id = $_POST['id'] ?? '';
    $text = trim($_POST['text'] ?? '');
    if (!$id || $text === "") exit;

    foreach ($notes as &$note) {
        if ($note['id'] === $id) {
            $note['text'] = htmlspecialchars($text);
            $note['date'] = date('Y-m-d H:i:s');
            break;
        }
    }

    file_put_contents($file, json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(["status" => "ok"]);
    break;

  case 'delete':
    $id = $_POST['id'] ?? '';
    if (!$id) exit;
    $notes = array_values(array_filter($notes, fn($n) => $n['id'] !== $id));
    file_put_contents($file, json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(["status" => "ok"]);
    break;
}
?>
