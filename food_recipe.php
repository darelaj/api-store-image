<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS food_recipe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi VARCHAR(255) NOT NULL,
    langkah TEXT NOT NULL,
    imageId VARCHAR(255) NOT NULL
  )"
);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

  // METHOD GET ALL IN DATABASE
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = mysqli_query($conn, "SELECT * FROM food_recipe WHERE userId = '$authorizationHeader'");

    $result = array();
    while ($row = mysqli_fetch_array($sql)) {
      array_push(
        $result,
        array(
          'id' => $row['id'],
          'userId' => $row['userId'],
          'judul' => $row['judul'],
          'deskripsi' => $row['deskripsi'],
          'langkah' => $row['langkah'],
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

    if (isset($_POST['id'])) {

      $id = $_POST['id'];
      $judul = $_POST['judul'] ?? '';
      $deskripsi = $_POST['deskripsi'] ?? '';
      $langkah = $_POST['langkah'] ?? '';

      if (empty($judul) || empty($deskripsi) || empty($langkah)) {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Judul, Deskripsi, dan Langkah-Langkah harus diisi'
          )
        );
        exit;
      }

      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $userId = $_SERVER['HTTP_AUTHORIZATION'];

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileJudul = $userId . '-' . $judul . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileJudulToDatabase = $userId . '-' . $judul . '-' . time();

        $destination = $uploadDirectory . $uniqueFileJudul;

        // Update the database with the new image and other details
        $query = mysqli_query($conn, "UPDATE food_recipe SET judul='$judul', deskripsi='$deskripsi', langkah='$langkah', imageId='$fileJudulToDatabase' WHERE id='$id'");

        // Move the uploaded file to the specified destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination) && $query) {
          // File upload successful
          echo json_encode(
            array(
              'status' => 'success',
              'message' => 'File uploaded and data updated successfully',
            )
          );
        } else {
          // Failed to save the file or update the database
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Failed to save file or update data'
            )
          );
        }

      } else {
        $query = mysqli_query($conn, "UPDATE food_recipe SET judul='$judul', deskripsi='$deskripsi', langkah='$langkah' WHERE id='$id'");

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
        $judul = $_POST['judul'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';
        $langkah = $_POST['langkah'] ?? '';

        if (empty($judul) || empty($deskripsi) || empty($langkah)) {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Judul, Deskripsi, dan Langkah-Langkah harus diisi'
            )
          );
          exit;
        }

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileJudul = $userId . '-' . $judul . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileJudulToDatabase = $userId . '-' . $judul . '-' . time();

        $destination = $uploadDirectory . $uniqueFileJudul;

        $query = mysqli_query($conn, "INSERT INTO food_recipe (userId, judul, deskripsi, langkah, imageId, mine) VALUES ('$userId', '$judul', '$deskripsi', '$langkah', '$fileJudulToDatabase', 1)");

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

    $sql = mysqli_query($conn, "DELETE FROM food_recipe WHERE id=" . $_GET['id']);

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