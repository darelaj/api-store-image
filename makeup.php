<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS makeup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi VARCHAR(255) NOT NULL,
    harga VARCHAR(255) NOT NULL,
    imageId VARCHAR(255) NOT NULL
  )"
);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

  // METHOD GET ALL IN DATABASE
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = mysqli_query($conn, "SELECT * FROM makeup WHERE userId = '$authorizationHeader'");

    $result = array();
    while ($row = mysqli_fetch_array($sql)) {
      array_push(
        $result,
        array(
          'id' => $row['id'],
          'userId' => $row['userId'],
          'judul' => $row['judul'],
          'deskripsi' => $row['deskripsi'],
          'harga' => $row['harga'],
          'imageId' => $row['imageId'],
          'mine' => $row['mine']
        )
      );
    }

    echo json_encode(
      $result
    );

  }

  // METHOD POST GAMBAR BARU
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($authorizationHeader)) {
      echo json_encode(array('status' => 'failed', 'message' => 'Anda Belum Login'));
      exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

      $userId = $_SERVER['HTTP_AUTHORIZATION'];
      $judul = $_POST['judul'] ?? '';
      $deskripsi = $_POST['deskripsi'] ?? '';
      $harga = $_POST['harga'] ?? '';

      if (empty($judul) || empty($deskripsi) || empty($harga)) {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Judul, Deskripsi, dan Harga harus diisi'
          )
        );
        exit;
      }

      $uploadDirectory = 'images/';

      $uniqueFileJudul = $userId . '-' . $judul . '-' . time() . '.jpg'; // Assuming JPEG format

      $fileJudulToDatabase = $userId . '-' . $judul . '-' . time();

      $destination = $uploadDirectory . $uniqueFileJudul;

      $query = mysqli_query($conn, "INSERT INTO makeup (userId, judul, deskripsi, harga, imageId, mine) VALUES ('$userId', '$judul', '$deskripsi', '$harga', '$fileJudulToDatabase', 1)");

      // Move the uploaded file to the specified destination
      if (move_uploaded_file($_FILES['image']['tmp_judul'], $destination) && $query) {
        // File upload successful
        echo json_encode(
          array(
            'status' => 'success',
            'message' => 'File uploaded successfully',
          )
        );
      } else {
        // Failed to save the file
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Failed to save file'
          )
        );
      }

    } else {
      // No file uploaded or error occurred during upload
      echo json_encode(
        array(
          'status' => 'failed',
          'message' => 'No file uploaded or error occurred during upload'
        )
      );
    }

  }

  if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $sql = mysqli_query($conn, "DELETE FROM makeup WHERE id=" . $_GET['id']);

    if ($sql) {
      echo json_encode(
        array(
          'status' => 'success',
          'message' => 'Data deleted successfully'
        )
      );
    } else {
      echo json_encode(
        array(
          'status' => 'failed',
          'message' => 'Failed to delete data'
        )
      );

    }

  }

  // echo json_encode(array('message' => 'Email: ' . $authorizationHeader));
} else {
  echo json_encode(
    array(
      'status' => 'failed',
      'message' => 'Authorization header not set'
    )
  );
}

?>