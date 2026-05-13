<?php
/**
 * Student — Profile Page
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireStudentLogin();

$uid  = $_SESSION['user_id'];
$user = getStudentById($pdo, $uid);

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name     = sanitize($_POST['name']     ?? '');
  $phone    = sanitize($_POST['phone']    ?? '');
  $address  = sanitize($_POST['address']  ?? '');
  $semester = sanitize($_POST['semester'] ?? '');
  $course   = sanitize($_POST['course']   ?? '');
  $newpass  = $_POST['new_password'] ?? '';
  $confirm  = $_POST['confirm_password'] ?? '';

  if (empty($name)) {
    $error = 'Name cannot be empty.';
  } else {
    $pdo->prepare("UPDATE users SET name=?, phone=?, address=?, semester=?, course=? WHERE id=?")
        ->execute([$name, $phone, $address, $semester, $course, $uid]);
    $_SESSION['user_name'] = $name;

    // Change password if requested
    if ($newpass) {
      if (strlen($newpass) < 6) {
        $error = 'New password must be at least 6 characters.';
      } elseif ($newpass !== $confirm) {
        $error = 'Passwords do not match.';
      } else {
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([hashPassword($newpass), $uid]);
        $success = 'Profile and password updated successfully!';
      }
    } else {
      $success = 'Profile updated successfully!';
    }
    $user = getStudentById($pdo, $uid);
  }
}

// Stats
$myBooks  = getStudentIssuedBooks($pdo, $uid);
$issued   = count(array_filter($myBooks, fn($b) => $b['status'] === 'issued'));
$returned = count(array_filter($myBooks, fn($b) => $b['status'] === 'returned'));
$overdue  = count(array_filter($myBooks, fn($b) => $b['status'] === 'overdue'));
$fineStmt = $pdo->prepare("SELECT SUM(amount) FROM fines WHERE user_id=? AND paid_status='unpaid'");
$fineStmt->execute([$uid]);
$myFines  = $fineStmt->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>My Profile — SmartLib</title>
  <link rel="stylesheet" href="../assets/css/style.css"/>
  <link rel="stylesheet" href="../assets/css/dashboard.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
<div class="dashboard-layout">
  <div class="sidebar-overlay"></div>
  <?php include '_sidebar.php'; ?>
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <button id="toggle-sidebar"><i class="fa-solid fa-bars"></i></button>
        <div><div class="page-title">My Profile</div><div class="breadcrumb">Student / Profile</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <?php if($error):   ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div><?php endif; ?>
      <?php if($success): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div><?php endif; ?>

      <div class="grid-2" style="align-items:start;">
        <!-- Profile Card -->
        <div>
          <div class="profile-card" style="margin-bottom:1.5rem;">
            <div class="profile-banner"></div>
            <div class="profile-body">
              <div class="profile-avatar-wrap">
                <div class="profile-avatar"><?= strtoupper(substr($user['name'],0,1)) ?></div>
              </div>
              <div class="profile-name"><?= htmlspecialchars($user['name']) ?></div>
              <div class="profile-role"><?= htmlspecialchars($user['course'] ?? 'BCA') ?> — <?= htmlspecialchars($user['semester'] ?? '') ?> Semester</div>
              <div class="profile-details">
                <div class="detail-item"><label>Email</label><p><?= htmlspecialchars($user['email']) ?></p></div>
                <div class="detail-item"><label>Phone</label><p><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p></div>
                <div class="detail-item"><label>Reg. No.</label><p><?= htmlspecialchars($user['reg_no'] ?? 'N/A') ?></p></div>
                <div class="detail-item"><label>Status</label><p><span class="badge badge-success"><?= ucfirst($user['status']) ?></span></p></div>
                <div class="detail-item"><label>Member Since</label><p><?= formatDate($user['created_at']) ?></p></div>
                <div class="detail-item"><label>Address</label><p><?= htmlspecialchars($user['address'] ?? 'N/A') ?></p></div>
              </div>
            </div>
          </div>

          <!-- Mini Stats -->
          <div class="card">
            <div class="card-header"><span class="card-title">📊 My Statistics</span></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
              <div style="text-align:center;padding:.75rem;background:var(--surface2);border-radius:var(--radius-sm);">
                <div style="font-size:1.75rem;font-weight:800;color:var(--primary);"><?= $issued ?></div>
                <div style="font-size:.8rem;color:var(--text-muted);">Currently Issued</div>
              </div>
              <div style="text-align:center;padding:.75rem;background:var(--surface2);border-radius:var(--radius-sm);">
                <div style="font-size:1.75rem;font-weight:800;color:var(--success);"><?= $returned ?></div>
                <div style="font-size:.8rem;color:var(--text-muted);">Returned</div>
              </div>
              <div style="text-align:center;padding:.75rem;background:var(--surface2);border-radius:var(--radius-sm);">
                <div style="font-size:1.75rem;font-weight:800;color:var(--danger);"><?= $overdue ?></div>
                <div style="font-size:.8rem;color:var(--text-muted);">Overdue</div>
              </div>
              <div style="text-align:center;padding:.75rem;background:var(--surface2);border-radius:var(--radius-sm);">
                <div style="font-size:1.75rem;font-weight:800;color:var(--warning);">₹<?= number_format($myFines,0) ?></div>
                <div style="font-size:.8rem;color:var(--text-muted);">Pending Fine</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Form -->
        <div class="card">
          <div class="card-header"><span class="card-title">✏️ Edit Profile</span></div>
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required />
            </div>
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled />
              <small class="text-muted">Email cannot be changed.</small>
            </div>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label class="form-label">Course</label>
                <select name="course" class="form-control">
                  <?php foreach(['BCA','MCA','BSc CS','BSc IT','BTech','Other'] as $c): ?>
                    <option value="<?= $c ?>" <?= ($user['course'] === $c) ? 'selected' : '' ?>><?= $c ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Semester</label>
              <select name="semester" class="form-control">
                <?php foreach(['1st','2nd','3rd','4th','5th','6th'] as $s): ?>
                  <option value="<?= $s ?>" <?= ($user['semester'] === $s) ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Address</label>
              <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <hr style="border-color:var(--border);margin:1.5rem 0;" />
            <p style="font-weight:700;margin-bottom:1rem;font-size:.95rem;">🔒 Change Password <small style="font-weight:400;color:var(--text-muted)">(leave blank to keep current)</small></p>

            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Min 6 chars" />
              </div>
              <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" />
              </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">
              <i class="fa-solid fa-floppy-disk"></i> Save Profile
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
