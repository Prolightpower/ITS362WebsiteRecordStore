<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../config.php';
requireAdmin();

$conn = getDB();
$msg = '';

// ---- Handle Edit User ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'edit_user') {
        $id       = (int)$_POST['user_id'];
        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $role     = $_POST['role'] === 'admin' ? 'admin' : 'user';

        if ($id === (int)$_SESSION['user_id']) {
            $msg = 'error:You cannot edit your own account from here.';
        } elseif (empty($username) || empty($email)) {
            $msg = 'error:Username and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'error:Invalid email address.';
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $email, $role, $id);
            $stmt->execute() ? $msg = 'success:User updated successfully.' : $msg = 'error:Could not update user.';
            $stmt->close();
        }
    }

    if ($_POST['action'] === 'delete_user') {
        $id = (int)$_POST['user_id'];
        if ($id === (int)$_SESSION['user_id']) {
            $msg = 'error:You cannot delete your own account.';
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute() ? $msg = 'success:User deleted.' : $msg = 'error:Could not delete user.';
            $stmt->close();
        }
    }

    if ($_POST['action'] === 'edit_album') {
        $id     = (int)$_POST['album_id'];
        $title  = trim($_POST['title']);
        $artist = trim($_POST['artist']);
        $genre  = trim($_POST['genre']);
        $year   = (int)$_POST['year'];
        $price  = (float)$_POST['price'];
        $stock  = (int)$_POST['stock'];
        $desc   = trim($_POST['description']);

        if (empty($title) || empty($artist)) {
            $msg = 'error:Title and artist are required.';
        } else {
            $stmt = $conn->prepare("UPDATE albums SET title=?, artist=?, genre=?, year=?, price=?, stock=?, description=? WHERE id=?");
            $stmt->bind_param("sssidisi", $title, $artist, $genre, $year, $price, $stock, $desc, $id);
            $stmt->execute() ? $msg = 'success:Album updated.' : $msg = 'error:Could not update album.';
            $stmt->close();
        }
    }

    if ($_POST['action'] === 'add_album') {
        $title  = trim($_POST['title']);
        $artist = trim($_POST['artist']);
        $genre  = trim($_POST['genre']);
        $year   = (int)$_POST['year'];
        $price  = (float)$_POST['price'];
        $stock  = (int)$_POST['stock'];
        $desc   = trim($_POST['description']);
        $colors = ['#1a3a5c','#5c3a1a','#3a1a5c','#2a4a2a','#4a2a2a','#1a2a4a','#2a3a2a','#0a0a1a'];
        $color  = $colors[array_rand($colors)];

        if (empty($title) || empty($artist)) {
            $msg = 'error:Title and artist are required.';
        } else {
            $stmt = $conn->prepare("INSERT INTO albums (title, artist, genre, year, price, stock, description, cover_color) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssidiss", $title, $artist, $genre, $year, $price, $stock, $desc, $color);
            $stmt->execute() ? $msg = 'success:Album added.' : $msg = 'error:Could not add album.';
            $stmt->close();
        }
    }

    if ($_POST['action'] === 'delete_album') {
        $id = (int)$_POST['album_id'];
        $stmt = $conn->prepare("DELETE FROM albums WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute() ? $msg = 'success:Album deleted.' : $msg = 'error:Could not delete album.';
        $stmt->close();
    }
}

// Fetch data
$users  = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$albums = $conn->query("SELECT * FROM albums ORDER BY title ASC");
$totalUsers  = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$totalAlbums = $conn->query("SELECT COUNT(*) c FROM albums")->fetch_assoc()['c'];
$totalStock  = $conn->query("SELECT SUM(stock) c FROM albums")->fetch_assoc()['c'];

[$msgType, $msgText] = $msg ? explode(':', $msg, 2) : ['', ''];

require_once __DIR__ . '/../header.php';
?>

<div class="admin-wrap">
    <div class="admin-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Manage users and the album catalogue.</p>
        </div>
        <a href="/RecordStore/index.php" class="btn-secondary">← Back to Shop</a>
    </div>

    <?php if ($msgText): ?>
        <div class="alert alert-<?php echo $msgType; ?>"><?php echo sanitize($msgText); ?></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-val"><?php echo (int)$totalUsers; ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-val"><?php echo (int)$totalAlbums; ?></div>
            <div class="stat-label">Albums Listed</div>
        </div>
        <div class="stat-card">
            <div class="stat-val"><?php echo (int)$totalStock; ?></div>
            <div class="stat-label">Units in Stock</div>
        </div>
    </div>

    <!-- ---- USERS TABLE ---- -->
    <div class="section-title">User Accounts</div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted">#<?php echo (int)$u['id']; ?></td>
                    <td><?php echo sanitize($u['username']); ?></td>
                    <td class="text-muted"><?php echo sanitize($u['email']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $u['role']; ?>">
                            <?php echo ucfirst($u['role']); ?>
                        </span>
                    </td>
                    <td class="text-muted"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                    <td style="display:flex;gap:8px;">
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <button class="btn-secondary btn-edit-user"
                                data-id="<?php echo $u['id']; ?>"
                                data-username="<?php echo sanitize($u['username']); ?>"
                                data-email="<?php echo sanitize($u['email']); ?>"
                                data-role="<?php echo $u['role']; ?>">Edit</button>
                            <button class="btn-danger btn-confirm-delete"
                                data-id="<?php echo $u['id']; ?>"
                                data-username="<?php echo sanitize($u['username']); ?>">Delete</button>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:12px;">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- ---- ALBUMS TABLE ---- -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);">
        <div class="section-title" style="margin-bottom:0;padding-bottom:0;border:none;">Album Catalogue</div>
        <button class="btn-secondary" onclick="openModal('addAlbumModal')">+ Add Album</button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Genre</th>
                    <th>Year</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($a = $albums->fetch_assoc()): ?>
                <tr>
                    <td><?php echo sanitize($a['title']); ?></td>
                    <td class="text-muted"><?php echo sanitize($a['artist']); ?></td>
                    <td><span class="badge badge-user"><?php echo sanitize($a['genre']); ?></span></td>
                    <td class="text-muted"><?php echo (int)$a['year']; ?></td>
                    <td class="text-gold">$<?php echo number_format($a['price'], 2); ?></td>
                    <td class="<?php echo $a['stock'] == 0 ? 'out-of-stock' : ''; ?>"><?php echo (int)$a['stock']; ?></td>
                    <td style="display:flex;gap:8px;">
                        <button class="btn-secondary btn-edit-album"
                            data-id="<?php echo $a['id']; ?>"
                            data-title="<?php echo sanitize($a['title']); ?>"
                            data-artist="<?php echo sanitize($a['artist']); ?>"
                            data-genre="<?php echo sanitize($a['genre']); ?>"
                            data-year="<?php echo (int)$a['year']; ?>"
                            data-price="<?php echo $a['price']; ?>"
                            data-stock="<?php echo (int)$a['stock']; ?>"
                            data-desc="<?php echo sanitize($a['description']); ?>">Edit</button>
                        <button class="btn-danger btn-delete-album"
                            data-id="<?php echo $a['id']; ?>"
                            data-title="<?php echo sanitize($a['title']); ?>">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== MODALS ===== -->

<!-- Edit User Modal -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal">
        <button class="modal-close">×</button>
        <h3>Edit User</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="edit_username" maxlength="50">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="edit_email" maxlength="100">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" id="edit_role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary modal-close-btn" onclick="closeModal('editUserModal')">Cancel</button>
                <button type="submit" class="btn-primary" style="width:auto;padding:10px 24px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal-overlay" id="deleteUserModal">
    <div class="modal">
        <button class="modal-close">×</button>
        <h3>Delete User</h3>
        <p style="color:var(--muted);font-size:14px;">Are you sure you want to delete <strong id="delete_username_label" style="color:var(--text);"></strong>? This cannot be undone.</p>
        <form method="POST">
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" name="user_id" id="delete_user_id">
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('deleteUserModal')">Cancel</button>
                <button type="submit" class="btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Album Modal -->
<div class="modal-overlay" id="addAlbumModal">
    <div class="modal">
        <button class="modal-close">×</button>
        <h3>Add Album</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_album">
            <div class="form-group"><label>Title</label><input type="text" name="title" maxlength="150"></div>
            <div class="form-group"><label>Artist</label><input type="text" name="artist" maxlength="100"></div>
            <div class="form-group"><label>Genre</label><input type="text" name="genre" maxlength="50"></div>
            <div class="form-group"><label>Year</label><input type="number" name="year" min="1900" max="2030" value="2024"></div>
            <div class="form-group"><label>Price ($)</label><input type="number" name="price" step="0.01" min="0"></div>
            <div class="form-group"><label>Stock</label><input type="number" name="stock" min="0" value="0"></div>
            <div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('addAlbumModal')">Cancel</button>
                <button type="submit" class="btn-primary" style="width:auto;padding:10px 24px;">Add Album</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Album Modal -->
<div class="modal-overlay" id="editAlbumModal">
    <div class="modal">
        <button class="modal-close">×</button>
        <h3>Edit Album</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_album">
            <input type="hidden" name="album_id" id="ea_id">
            <div class="form-group"><label>Title</label><input type="text" name="title" id="ea_title" maxlength="150"></div>
            <div class="form-group"><label>Artist</label><input type="text" name="artist" id="ea_artist" maxlength="100"></div>
            <div class="form-group"><label>Genre</label><input type="text" name="genre" id="ea_genre" maxlength="50"></div>
            <div class="form-group"><label>Year</label><input type="number" name="year" id="ea_year" min="1900" max="2030"></div>
            <div class="form-group"><label>Price ($)</label><input type="number" name="price" id="ea_price" step="0.01" min="0"></div>
            <div class="form-group"><label>Stock</label><input type="number" name="stock" id="ea_stock" min="0"></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="ea_desc"></textarea></div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('editAlbumModal')">Cancel</button>
                <button type="submit" class="btn-primary" style="width:auto;padding:10px 24px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Album Modal -->
<div class="modal-overlay" id="deleteAlbumModal">
    <div class="modal">
        <button class="modal-close">×</button>
        <h3>Delete Album</h3>
        <p style="color:var(--muted);font-size:14px;">Are you sure you want to remove <strong id="delete_album_label" style="color:var(--text);"></strong> from the catalogue?</p>
        <form method="POST">
            <input type="hidden" name="action" value="delete_album">
            <input type="hidden" name="album_id" id="delete_album_id">
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('deleteAlbumModal')">Cancel</button>
                <button type="submit" class="btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../footer.php';
?>
