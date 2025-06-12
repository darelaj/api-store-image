<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS makeup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    produk VARCHAR(255) NOT NULL,
    harga VARCHAR(255) NOT NULL,
    imageId VARCHAR(255) NOT NULL,
    mine INT NOT NULL DEFAULT 1
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
          'produk' => $row['produk'],
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

    if (isset($_POST['id'])) { // For updating data

      $id = $_POST['id'];
      $produk = $_POST['produk'] ?? '';

      $harga = $_POST['harga'] ?? '';

      if (empty($produk) || empty($harga)) {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Produk, dan Harga harus diisi'
          )
        );
        exit;
      }

      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $userId = $authorizationHeader;

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileProduk = $userId . '-' . $produk . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileProdukToDatabase = $userId . '-' . $produk . '-' . time();

        $destination = $uploadDirectory . $uniqueFileProduk;

        $query = mysqli_query($conn, "UPDATE makeup SET produk='$produk', harga='$harga', imageId='$fileProdukToDatabase' WHERE id=$id");

        // Move the uploaded file to the specified destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination) && $query) {
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
          exit;
        }

      } else {
        $query = mysqli_query($conn, "UPDATE makeup SET produk='$produk', harga='$harga' WHERE id=$id");

        if ($query) {
          echo json_encode(
            array(
              'status' => 'success',
              'message' => 'Data updated successfully'
            )
          );
        } else {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Failed to update data'
            )
          );
        }
      }
    } else {

      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $userId = $_SERVER['HTTP_AUTHORIZATION'];
        $produk = $_POST['produk'] ?? '';
        $harga = $_POST['harga'] ?? '';

        if (empty($produk) || empty($harga)) {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Produk, dan Harga harus diisi'
            )
          );
          exit;
        }

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileProduk = $userId . '-' . $produk . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileProdukToDatabase = $userId . '-' . $produk . '-' . time();

        $destination = $uploadDirectory . $uniqueFileProduk;

        $query = mysqli_query($conn, "INSERT INTO makeup (userId, produk, harga, imageId, mine) VALUES ('$userId', '$produk', '$harga', '$fileProdukToDatabase', 1)");

        // Move the uploaded file to the specified destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination) && $query) {
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
  }

  if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $id = $_GET['id'];
    $sql = mysqli_query($conn, "SELECT imageId FROM makeup WHERE id=$id");
    $row = mysqli_fetch_assoc($sql);

    if ($row) {
      $fileNameToDelete = $row['imageId'] . '.jpg';
      $filePath = __DIR__ . '/images/' . $fileNameToDelete;

      if (file_exists($filePath)) {
        unlink($filePath);
      }

      $deleteSql = mysqli_query($conn, "DELETE FROM makeup WHERE id=$id");

      if ($deleteSql) {
        echo json_encode(
          array(
            'status' => 'success',
            'message' => 'Data and image deleted successfully'
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