<?php
// Cấu hình kết nối cơ sở dữ liệu
$host = 'localhost';
$dbname = 'webbase';
$user = 'root'; // User mặc định của XAMPP
$pass = '0945350630';     // Password mặc định của XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Thiết lập chế độ báo lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Xử lý các hành động Insert, Update, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Lấy dữ liệu từ form
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
    $temp = isset($_POST['temp']) && $_POST['temp'] !== '' ? floatval($_POST['temp']) : null;
    $detail = $_POST['detail'] ?? '';

    try {
        if ($action === 'insert' && $id !== null) {
            $stmt = $pdo->prepare("INSERT INTO ice (id, temp, detail) VALUES (?, ?, ?)");
            $stmt->execute([$id, $temp, $detail]);
        } elseif ($action === 'update' && $id !== null) {
            $stmt = $pdo->prepare("UPDATE ice SET temp = ?, detail = ? WHERE id = ?");
            $stmt->execute([$temp, $detail, $id]);
        } elseif ($action === 'delete' && $id !== null) {
            $stmt = $pdo->prepare("DELETE FROM ice WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        // Refresh lại trang để tránh gửi lại form khi F5
        header("Location: ice.php");
        exit;
    } catch (PDOException $e) {
        $error = "Lỗi truy vấn: " . $e->getMessage();
    }
}

// Lấy danh sách dữ liệu từ bảng ice
try {
    $stmt = $pdo->query("SELECT * FROM ice ORDER BY id ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Lỗi truy vấn danh sách: " . $e->getMessage();
    $rows = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý ICE</title>
    
</head>
<body>

<div class="container">
    <h2>Quản lý dữ liệu bảng ICE</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="post" action="ice.php">
            <div class="form-group">
                <label for="id">ID:</label>
                <input type="number" name="id" id="id" required placeholder="Nhập ID (bắt buộc)">
            </div>
            <div class="form-group">
                <label for="temp">Temp:</label>
                <input type="number" step="any" name="temp" id="temp" placeholder="Nhập Temp">
            </div>
            <div class="form-group">
                <label for="detail">Detail:</label>
                <input type="text" name="detail" id="detail" maxlength="100" placeholder="Nhập Detail">
            </div>
            
            <button type="submit" name="action" value="insert" class="btn btn-insert">Thêm (Insert)</button>
            <button type="submit" name="action" value="update" class="btn btn-update">Sửa (Update)</button>
            <button type="submit" name="action" value="delete" class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa bản ghi này?');">Xóa (Delete)</button>
            <button type="button" onclick="clearForm()" class="btn btn-clear">Làm mới Form</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Temp</th>
                <th>Detail</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['temp'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['detail'] ?? '') ?></td>
                    <td>
                        <button type="button" class="btn" onclick="fillForm('<?= $row['id'] ?>', '<?= $row['temp'] ?>', '<?= htmlspecialchars(addslashes($row['detail'])) ?>')">Chọn</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
                <tr><td colspan="4" style="text-align: center;">Không có dữ liệu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
