<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "astroo";

$conn = new mysqli($servername, $username, $password, $dbname);

// Handle block/unblock/delete actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'block') {
        $conn->query("UPDATE subscriptions SET status = 'blocked' WHERE customer_id = $id");
    } elseif ($action === 'unblock') {
        $conn->query("UPDATE subscriptions SET status = 'active' WHERE customer_id = $id");
    } elseif ($action === 'delete') {
        $conn->query("DELETE FROM customer WHERE id = $id");
        $conn->query("DELETE FROM subscriptions WHERE customer_id = $id");
    }
    
    header("Location: admin_dashboard.php");
    exit();
}

// Get all subscribers
$sql = "SELECT c.*, s.status as sub_status, s.end_date, s.max_resolution, p.package_name, p.price 
        FROM customer c 
        LEFT JOIN subscriptions s ON c.id = s.customer_id 
        LEFT JOIN packages p ON s.package_id = p.id 
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Astro</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #1a1a2e;
            color: white;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            background: #e6007e;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 { margin: 0; }
        
        .logout-btn {
            background: white;
            color: #e6007e;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        
        th {
            background: #e6007e;
            padding: 15px;
            text-align: left;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        tr:hover { background: rgba(255,255,255,0.05); }
        
        .status-active { color: #00c853; font-weight: bold; }
        .status-blocked { color: #ff5252; font-weight: bold; }
        .status-expired { color: #ff9800; font-weight: bold; }
        
        .action-btn {
            padding: 8px 15px;
            border-radius: 15px;
            text-decoration: none;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .btn-block { background: #ff5252; color: white; }
        .btn-unblock { background: #00c853; color: white; }
        .btn-delete { background: #ff9800; color: white; }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            text-align: center;
        }
        
        .stat-box h3 { margin: 0; color: #e6007e; font-size: 32px; }
        .stat-box p { margin: 5px 0 0 0; color: #aaa; }
    </style>
</head>
<body>
    <div class="header">
        <h1>👑 Astro Admin Dashboard</h1>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    </div>
    
    <?php
    // Count stats
    $total = $conn->query("SELECT COUNT(*) as count FROM customer")->fetch_assoc()['count'];
    $active = $conn->query("SELECT COUNT(*) as count FROM subscriptions WHERE status='active'")->fetch_assoc()['count'];
    $blocked = $conn->query("SELECT COUNT(*) as count FROM subscriptions WHERE status='blocked'")->fetch_assoc()['count'];
    ?>
    
    <div class="stats">
        <div class="stat-box">
            <h3><?php echo $total; ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $active; ?></h3>
            <p>Active Subscriptions</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $blocked; ?></h3>
            <p>Blocked Users</p>
        </div>
    </div>
    
    <table>
<thead>
    <tr>
        <th>ID</th>
        <th>Astro ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Plan</th>
        <th>Price</th>
        <th>Max Resolution</th>
        <th>End Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): 
                $status_class = '';
                if ($row['sub_status'] == 'active') $status_class = 'status-active';
                elseif ($row['sub_status'] == 'blocked') $status_class = 'status-blocked';
                else $status_class = 'status-expired';
                
                $is_blocked = ($row['sub_status'] == 'blocked');
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['astro_id']); ?></td>
                <td><?php echo htmlspecialchars($row['full_name'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                <td><?php echo $row['package_name'] ?? '-'; ?></td>
<td>RM<?php echo $row['price'] ?? '0'; ?></td>
<td>
    <form method="POST" action="update_resolution.php" style="display:inline;">
        <input type="hidden" name="customer_id" value="<?php echo $row['id']; ?>">
        <select name="max_resolution" onchange="this.form.submit()" style="background:#333; color:white; border:1px solid #555; padding:3px; border-radius:3px;">
            <option value="720" <?php echo ($row['max_resolution'] ?? '1080') == '720' ? 'selected' : ''; ?>>720p</option>
            <option value="1080" <?php echo ($row['max_resolution'] ?? '1080') == '1080' ? 'selected' : ''; ?>>1080p</option>
            <option value="4k" <?php echo ($row['max_resolution'] ?? '1080') == '4k' ? 'selected' : ''; ?>>4K</option>
        </select>
    </form>
</td>
<td><?php echo $row['end_date'] ?? '-'; ?></td>
                <td class="<?php echo $status_class; ?>">
                    <?php echo ucfirst($row['sub_status'] ?? 'inactive'); ?>
                </td>
                <td>
                    <?php if ($is_blocked): ?>
                        <a href="?action=unblock&id=<?php echo $row['id']; ?>" class="action-btn btn-unblock">Unblock</a>
                    <?php else: ?>
                        <a href="?action=block&id=<?php echo $row['id']; ?>" class="action-btn btn-block">Block</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>