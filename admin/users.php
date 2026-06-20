<?php
$page_title = 'User Management';
require_once '../config.php';
requireAdmin();
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'add') {
    $username = clean($_POST['username'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $role = clean($_POST['role'] ?? 'user');
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email); $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) { $error = "Username or email already exists"; }
    else {
      $stmt->close();
      $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $username, $email, $password, $role);
      if ($stmt->execute()) { $success = "User added successfully"; addNotification("New user added: $username", "info"); }
      else { $error = "Failed to add user"; }
    }
    $stmt->close(); $conn->close();
  } elseif ($action === 'delete') {
    $user_id = (int)$_POST['user_id'];
    if ($user_id == $_SESSION['user_id']) { $error = "You cannot delete your own account"; }
    else {
      $conn = getDBConnection();
      $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
      $stmt->bind_param("i", $user_id);
      if ($stmt->execute()) { $success = "User deleted successfully"; } else { $error = "Failed to delete user"; }
      $stmt->close(); $conn->close();
    }
  } elseif ($action === 'change_role') {
    $user_id = (int)$_POST['user_id']; $new_role = clean($_POST['role']);
    if ($user_id == $_SESSION['user_id']) { $error = "You cannot change your own role"; }
    else {
      $conn = getDBConnection();
      $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
      $stmt->bind_param("si", $new_role, $user_id);
      if ($stmt->execute()) { $success = "User role updated"; } else { $error = "Failed to update role"; }
      $stmt->close(); $conn->close();
    }
  }
}
$conn = getDBConnection();
$result = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id ASC");
$users = [];
if ($result) { while ($row = $result->fetch_assoc()) { $users[] = $row; } $result->free(); }
$conn->close();
?>
<?php include '../includes/navbar.php'; ?>
<main class="md:ml-64 pt-20 px-8 pb-12 min-h-screen bg-surface-container-low">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
    <div>
      <h1 class="font-headline text-4xl font-bold text-on-surface tracking-tight">User Management</h1>
      <p class="text-on-surface-variant mt-2 font-body max-w-lg text-sm">Configure system access, modify operator permissions, and audit active sessions.</p>
    </div>
  </div>
  <?php if ($success): ?>
  <div class="mb-6 p-4 rounded-lg bg-tertiary-container/30 border border-tertiary/20 text-tertiary text-sm font-medium flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span> <?php echo $success; ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="mb-6 p-4 rounded-lg bg-error-container/20 border border-error/20 text-error text-sm font-medium flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span> <?php echo $error; ?></div>
  <?php endif; ?>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5">
      <span class="text-on-surface-variant text-xs uppercase tracking-widest font-bold">Total Operators</span>
      <div class="mt-2 flex items-baseline gap-2">
        <span class="font-headline text-3xl font-bold text-primary"><?php echo count($users); ?></span>
      </div>
    </div>
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5">
      <span class="text-on-surface-variant text-xs uppercase tracking-widest font-bold">Admins</span>
      <div class="mt-2 flex items-baseline gap-2">
        <span class="font-headline text-3xl font-bold text-tertiary"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'admin')); ?></span>
      </div>
    </div>
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5">
      <span class="text-on-surface-variant text-xs uppercase tracking-widest font-bold">Standard Users</span>
      <div class="mt-2 flex items-baseline gap-2">
        <span class="font-headline text-3xl font-bold text-secondary"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'user')); ?></span>
      </div>
    </div>
    <div class="bg-surface-container-high p-6 rounded-xl border border-white/5">
      <span class="text-on-surface-variant text-xs uppercase tracking-widest font-bold">System Load</span>
      <div class="mt-2 flex items-baseline gap-2">
        <span class="font-headline text-3xl font-bold text-on-surface-variant">Optimal</span>
      </div>
    </div>
  </div>
  <div class="bg-surface-container p-6 rounded-xl border border-white/5 mb-6">
    <h3 class="font-headline font-bold text-lg text-white mb-6">Add New Operator</h3>
    <form method="POST" action="">
      <input type="hidden" name="action" value="add">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="add-username">Username</label>
          <input class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="add-username" name="username" required placeholder="Username" type="text"/>
        </div>
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="add-email">Email</label>
          <input class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="add-email" name="email" required placeholder="Email" type="email"/>
        </div>
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="add-password">Password</label>
          <input class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="add-password" name="password" required placeholder="Password" type="password"/>
        </div>
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2" for="add-role">Role</label>
          <select class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-on-surface focus:outline-none focus:ring-2 focus:ring-on-primary-container/30 text-sm" id="add-role" name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <button type="submit" class="mt-4 px-6 py-2.5 bg-primary text-on-primary text-sm font-bold rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-primary/10 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">person_add</span> Add User
      </button>
    </form>
  </div>
  <div class="bg-surface-container rounded-xl border border-white/5 overflow-hidden">
    <div class="p-6 border-b border-white/5">
      <h3 class="font-headline font-bold text-lg text-white">All Operators</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead><tr class="bg-surface-container-high/50 text-on-surface-variant text-[10px] uppercase tracking-widest font-bold">
          <th class="px-6 py-4 border-b border-white/5">ID</th>
          <th class="px-6 py-4 border-b border-white/5">Username</th>
          <th class="px-6 py-4 border-b border-white/5">Email</th>
          <th class="px-6 py-4 border-b border-white/5">Role</th>
          <th class="px-6 py-4 border-b border-white/5">Created</th>
          <th class="px-6 py-4 border-b border-white/5 text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-white/5">
          <?php foreach ($users as $row): ?>
          <tr class="group hover:bg-white/[0.02] transition-colors">
            <td class="px-6 py-5 font-headline text-xs text-on-surface-variant">#<?php echo $row['id']; ?></td>
            <td class="px-6 py-5"><div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-lg bg-primary-container flex items-center justify-center text-primary font-bold"><?php echo strtoupper(substr($row['username'], 0, 2)); ?></div>
              <div><div class="text-sm font-semibold text-on-surface"><?php echo htmlspecialchars($row['username']); ?></div></div>
            </div></td>
            <td class="px-6 py-5 text-sm text-on-surface-variant"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="px-6 py-5">
              <form method="POST" action="" style="display: inline;">
                <input type="hidden" name="action" value="change_role">
                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                <select name="role" onchange="this.form.submit()" class="bg-surface-container-low border border-outline-variant/30 rounded px-2 py-1 text-xs text-on-surface focus:ring-1 focus:ring-primary/20">
                  <option value="user" <?php echo $row['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                  <option value="admin" <?php echo $row['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
              </form>
            </td>
            <td class="px-6 py-5 text-sm text-on-surface-variant"><?php echo $row['created_at']; ?></td>
            <td class="px-6 py-5 text-right">
              <?php if ($row['id'] != $_SESSION['user_id']): ?>
              <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                <button type="submit" class="p-2 rounded-lg text-on-surface-variant hover:bg-error/10 hover:text-error transition-all">
                  <span class="material-symbols-outlined text-sm">delete</span>
                </button>
              </form>
              <?php else: ?>
              <span class="text-[10px] text-on-surface-variant">Current</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php require_once '../includes/footer.php'; ?>
