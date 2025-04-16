<?php
include 'db_connection.php';

// Delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM user WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// Update user
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    mysqli_query($conn, "UPDATE user SET username='$username', password='$password' WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// Edit mode
$edit_mode = false;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM user WHERE id=$id");
    $user_to_edit = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .admin-header {
      background-color: #343a40;
      color: white;
      padding: 20px;
      text-align: center;
    }
    .btn-logout {
      float: right;
      margin-top: -40px;
    }
    .table-container {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 20px;
      margin-top: 30px;
    }
  </style>
</head>
<body>

  <div class="admin-header">
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-danger btn-logout">Sign Out</a>
  </div>

  <div class="container table-container">
    <h4 class="mb-4">User Details</h4>

    <?php if ($edit_mode): ?>
      <div class="mb-4">
        <h5>Edit User</h5>
        <form method="post">
          <input type="hidden" name="id" value="<?= $user_to_edit['id'] ?>">
          <div class="row mb-3">
            <div class="col">
              <input type="text" name="username" class="form-control" value="<?= $user_to_edit['username'] ?>" required>
            </div>
            <div class="col">
              <input type="text" name="password" class="form-control" value="<?= $user_to_edit['password'] ?>" required>
            </div>
            <div class="col">
              <button type="submit" name="update" class="btn btn-primary">Update</button>
              <a href="admin.php" class="btn btn-secondary">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    <?php endif; ?>

    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Password</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $users = mysqli_query($conn, "SELECT * FROM user");
        while ($row = mysqli_fetch_assoc($users)) {
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['username']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['password']}</td>
                  <td>
                    <a href='admin.php?edit={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                    <a href='admin.php?delete={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete this user?');\">Delete</a>
                  </td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

</body>
</html>
