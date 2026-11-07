<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit;
}

$stmt = $conn->prepare("SELECT role, password_changed FROM acc WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($role, $passwordChanged);
$stmt->fetch();
$stmt->close();

$newAdminEmail = $_SESSION['new_admin_email'] ?? null;
unset($_SESSION['new_admin_email']);

// Fetch filtered contact data
$filterFrom = $_GET['from_date'] ?? '';
$filterTo = $_GET['to_date'] ?? '';
$filterCategory = $_GET['category'] ?? '';

$contactQuery = "SELECT * FROM contacts WHERE 1=1";
if (!empty($filterFrom)) {
  $contactQuery .= " AND created_at >= '" . $conn->real_escape_string($filterFrom) . "'";
}
if (!empty($filterTo)) {
  $contactQuery .= " AND created_at <= '" . $conn->real_escape_string($filterTo) . " 23:59:59'";
}
if (!empty($filterCategory)) {
  $contactQuery .= " AND category = '" . $conn->real_escape_string($filterCategory) . "'";
}
$contactQuery .= " ORDER BY created_at DESC";
$contactResults = $conn->query($contactQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles.css" />
</head>

<body>
  <header>
    <h1>Welcome, <?= htmlspecialchars($_SESSION["email"]) ?></h1>
    <a href="logout.php" class="logout-btn">Logout</a>
  </header>

  <div class="container">
    <?php if ($newAdminEmail): ?>
      <div class="notice">
        ✅ <strong>New Admin Registered:</strong> <?= htmlspecialchars($newAdminEmail) ?><br>
        Default Password: <code>Default@123</code><br>
        Please instruct them to change it after first login.
      </div>
    <?php endif; ?>

    <?php if ($passwordChanged == 0 && $role === 'admin'): ?>
      <div class="notice warning">
        ⚠️ You are using the default password. Please change your password below.
      </div>
    <?php endif; ?>

    <section>
      <h2>Change Password</h2>
      <form method="POST" action="change_password.php" class="admin-form">
        <input type="password" name="current_password" placeholder="Current Password" required />
        <input type="password" name="new_password" placeholder="New Password" required />
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required />
        <button type="submit">Change Password</button>
      </form>
    </section>

    <?php if ($role === 'super_admin'): ?>
      <section>
        <h2>Manage Admin Accounts</h2>
        <form method="POST" action="register_admin.php" class="admin-form">
          <input type="email" name="email" placeholder="New Admin Email" required />
          <button type="submit">Add Admin</button>
        </form>

        <table>
          <thead>
            <tr>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $admins = $conn->query("SELECT id, email FROM acc WHERE role = 'admin'");
            while ($admin = $admins->fetch_assoc()):
            ?>
              <tr>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td>
                  <form method="POST" action="delete_admin.php" style="display:inline" onsubmit="return confirm('Are you sure to delete this admin?');">
                    <input type="hidden" name="id" value="<?= $admin['id'] ?>" />
                    <button type="submit" class="danger">Delete</button>
                  </form>
                  <form method="POST" action="reset_admin_password.php" style="display:inline" onsubmit="return confirm('Reset password to Default@123?');">
                    <input type="hidden" name="id" value="<?= $admin['id'] ?>" />
                    <button type="submit">Reset Password</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    <?php endif; ?>

    <section>
      <h2>Submitted CVs</h2>
      <input type="text" id="cvSearch" placeholder="Search by name or email" />

      <form method="POST" action="download_cvs.php" onsubmit="return checkSelection();">
        <div id="loadingMsg" style="color: green; display: none;">Preparing files... Please wait.</div>
        <table id="cvTable">
          <thead>
            <tr>
              <th><input type="checkbox" onclick="toggleAll(this)" /></th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Message</th>
              <th>CV</th>
              <th>Uploaded</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $cvQuery = $conn->query("SELECT * FROM cv_submissions ORDER BY upload_time DESC");
            while ($row = $cvQuery->fetch_assoc()):
            ?>
              <tr>
                <td><input type="checkbox" name="selected_cv[]" value="<?= $row['id'] ?>" /></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['message']) ?></td>
                <td>
                  <a href="uploads/<?= $row['filename'] ?>" target="_blank">View</a> |
                  <a href="uploads/<?= $row['filename'] ?>" download>Download</a>
                </td>
                <td><?= $row['upload_time'] ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <button type="submit">Download Selected</button>
      </form>
    </section>

    <section>
      <h2>Contact List</h2>
      <form method="GET" id="filterForm">
        <label>From: <input type="date" name="from_date" value="<?= htmlspecialchars($filterFrom) ?>" onchange="submitFilters()" /></label>
        <label>To: <input type="date" name="to_date" value="<?= htmlspecialchars($filterTo) ?>" onchange="submitFilters()" /></label>
        <label>
          Category:
          <select name="category" onchange="submitFilters()">
            <option value="">-- All --</option>
            <?php
            $categories = [
              "Banking Solutions",
              "Risk Management",
              "Legal Advisory",
              "Tax Planning",
              "Investment Planning",
              "Estate Planning",
              "other"
            ];
            foreach ($categories as $cat) {
              $selected = ($filterCategory === $cat) ? 'selected' : '';
              echo "<option value=\"$cat\" $selected>$cat</option>";
            }
            ?>
          </select>
        </label>
        <a href="dashboard.php" class="reset-btn">Reset</a>
        <button type="submit" formaction="download_contacts.php">Download Excel</button>
      </form>

      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Category</th>
            <th>Message</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($c = $contactResults->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($c['name']) ?></td>
              <td><?= htmlspecialchars($c['email']) ?></td>
              <td><?= htmlspecialchars($c['phone']) ?></td>
              <td><?= htmlspecialchars($c['category']) ?></td>
              <td><?= htmlspecialchars($c['message']) ?></td>
              <td><?= $c['created_at'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </div>

  <script>
    function toggleAll(master) {
      document.querySelectorAll('input[name="selected_cv[]"]').forEach(cb => cb.checked = master.checked);
    }

    document.getElementById('cvSearch').addEventListener('input', function() {
      const value = this.value.toLowerCase();
      document.querySelectorAll('#cvTable tbody tr').forEach(row => {
        const name = row.children[1].textContent.toLowerCase();
        const email = row.children[2].textContent.toLowerCase();
        row.style.display = (name.includes(value) || email.includes(value)) ? '' : 'none';
      });
    });

    function checkSelection() {
      const checked = document.querySelectorAll('input[name="selected_cv[]"]:checked');
      if (checked.length === 0) {
        alert("Please select at least one CV to download.");
        return false;
      }
      document.getElementById('loadingMsg').style.display = 'block';
      return true;
    }

    function submitFilters() {
      document.getElementById('filterForm').submit();
    }
  </script>
</body>

</html>