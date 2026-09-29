<?php
require 'admin_auth.php';

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
require 'admin_layout_top.php';
?>
<div class="header">
    <h1>Users</h1>
    <input type="text" placeholder="Search (UI only)">
</div>

<div class="box">
    <h2 style="margin-top:0;">All Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Joined</th>
        </tr>

        <?php if ($users && $users->num_rows): ?>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo $u['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">No users found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php require 'admin_layout_bottom.php'; ?>
