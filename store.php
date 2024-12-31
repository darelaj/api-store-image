<?php

include_once 'connection.php';

// if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
// $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $sql = mysqli_query($conn, "SELECT * FROM images");

  $result = array();
  while ($row = mysqli_fetch_array($sql)) {
    array_push(
      $result,
      array(
        'id' => $row['id'],
        'imageId' => $row['imageId']
      )
    );
  }

  echo json_encode(
    $result
  );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    // $userId = $_SERVER['HTTP_AUTHORIZATION'];
    $uploadDirectory = __DIR__ . '/images/';

    $fileNameToDatabase = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);

    $uniqueFileName = $fileNameToDatabase;

    $destination = $uploadDirectory . $uniqueFileName;


    // Move the uploaded file to the specified destination
    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {

      $query = mysqli_query($conn, "INSERT INTO images (imageId) VALUES ('$fileNameToDatabase')");
      if (!$query) {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Failed to save file'
          )
        );
        exit;
      }
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

  $id = $_GET['id'];
  $sql = mysqli_query($conn, "SELECT imageId FROM images WHERE id=$id");
  $row = mysqli_fetch_assoc($sql);

  if ($row) {
    $fileNameToDelete = $row['imageId'] . '.jpg';
    $filePath = __DIR__ . '/images/' . $fileNameToDelete;

    if (file_exists($filePath)) {
      unlink($filePath);
    }

    $deleteSql = mysqli_query($conn, "DELETE FROM images WHERE id=$id");

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
  } else {
    echo json_encode(
      array(
        'status' => 'failed',
        'message' => 'Data not found'
      )
    );
  }
}

// }

?>