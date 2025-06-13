<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS pemain_bola (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    no_punggung VARCHAR(255) NOT NULL,
    imageId VARCHAR(255) NOT NULL
  )"
);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

  // METHOD GET ALL IN DATABASE
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = mysqli_query($conn, "SELECT * FROM pemain_bola WHERE userId = '$authorizationHeader'");

    $result = [];
    while ($row = mysqli_fetch_array($sql)) {
      array_push(
        $result,
        [
          'id' => $row['id'],
          'userId' => $row['userId'],
          'nama' => $row['nama'],
          'no_punggung' => $row['no_punggung'],
          'imageId' => $row['imageId']
        ]
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

    if (isset($_POST['id'])) { // unuk update data

      $id = $_POST['id'];
      $nama = $_POST['nama'] ?? '';
      $no_punggung = $_POST['no_punggung'] ?? '';

      if (empty($nama) || empty($no_punggung)) {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Nama pelanggan, Variant, dan Warna harus diisi'
          )
        );
        exit;
      }

      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) { // kalau ada gambar yang diupload

        $userId = $_SERVER['HTTP_AUTHORIZATION'];

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileName = $userId . '-' . $nama . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNameToDatabase = $userId . '-' . $nama . '-' . time();

        $destination = $uploadDirectory . $uniqueFileName;

        // Update the database with the new image and other details
        $query = mysqli_query($conn, "UPDATE pemain_bola SET nama='$nama', no_punggung='$no_punggung', imageId='$fileNameToDatabase' WHERE id='$id'");

        // Move the uploaded file to the specified destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination) && $query) { // File upload successful
          echo json_encode(
            array(
              'status' => 'success',
              'message' => 'File uploaded and data updated successfully',
            )
          );
        } else { // Failed to save the file or update the database
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Failed to save file or update data'
            )
          );
        }

      } else { // kalau tidak ada gambar yang diupload
        $query = mysqli_query($conn, "UPDATE pemain_bola SET nama='$nama', no_punggung='$no_punggung' WHERE id='$id'");

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
    } else { // untuk menambahkan data baru
      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $userId = $_SERVER['HTTP_AUTHORIZATION'];
        $nama = $_POST['nama'] ?? '';
        $no_punggung = $_POST['no_punggung'] ?? '';

        if (empty($nama) || empty($no_punggung)) {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Nama pelanggan, Variant, dan Warna harus diisi'
            )
          );
          exit;
        }

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileName = $userId . '-' . $nama . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNameToDatabase = $userId . '-' . $nama . '-' . time();

        $destination = $uploadDirectory . $uniqueFileName;

        $query = mysqli_query($conn, "INSERT INTO pemain_bola (userId, nama, no_punggung, imageId) VALUES ('$userId', '$nama', '$no_punggung', '$fileNameToDatabase')");

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
    $sql = mysqli_query($conn, "SELECT imageId FROM pemain_bola WHERE id=$id");
    $row = mysqli_fetch_assoc($sql);

    if ($row) {
      $fileNameToDelete = $row['imageId'] . '.jpg';
      $filePath = __DIR__ . '/images/' . $fileNameToDelete;

      if (file_exists($filePath)) {
        unlink($filePath);
      }

      $deleteSql = mysqli_query($conn, "DELETE FROM pemain_bola WHERE id=$id");

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