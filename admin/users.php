<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Quản lý người dùng';

// Xử lý thay đổi trạng thái user
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $user_id = (int)$_GET['toggle_status'];
    
    // Không cho phép thay đổi trạng thái của chính mình
    if ($user_id == $_SESSION['user_id']) {
        setMessage('error', 'Không thể thay đổi trạng thái của chính mình!');
    } else {
        $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            $new_status = $user['status'] === 'active' ? 'banned' : 'active';
            $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            if ($stmt->execute([$new_status, $user_id])) {
                $status_text = $new_status === 'active' ? 'kích hoạt' : 'khóa';
                setMessage('success', "Đã $status_text tài khoản thành công!");
            } else {
                setMessage('error', 'Có lỗi xảy ra!');
            }
        }
    }
    
    header('Location: users.php');
    exit;
}

// Xử lý xóa user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Không cho phép xóa chính mình
    if ($user_id == $_SESSION['user_id']) {
        setMessage('error', 'Không thể xóa tài khoản của chính mình!');
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        if ($stmt->execute([$user_id])) {
            setMessage('success', 'Xóa người dùng thành công!');
        } else {
            setMessage('error', 'Có lỗi xảy ra khi xóa người dùng!');
        }
    }
    
    header('Location: users.php');
    exit;
}

// Lấy danh sách người dùng
$users = $pdo->query("
    SELECT u.*, 
           COUNT(r.id) as review_count,
           AVG(r.rating) as avg_rating
    FROM users u 
    LEFT JOIN reviews r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Quản lý người dùng</h1>
        </div>
    </div>
    
    <!-- Danh sách người dùng -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Đánh giá</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Bị khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="rating-stars">
                                    <?php echo displayStars(round($user['avg_rating'] ?: 0)); ?>
                                </div>
                                <small class="text-muted">(<?php echo $user['review_count']; ?> đánh giá)</small>
                            </td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <a href="?toggle_status=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm <?php echo $user['status'] === 'active' ? 'btn-warning' : 'btn-success'; ?> me-1"
                                           onclick="return confirm('Bạn có chắc chắn muốn <?php echo $user['status'] === 'active' ? 'khóa' : 'kích hoạt'; ?> tài khoản này?')">
                                            <i class="fas <?php echo $user['status'] === 'active' ? 'fa-ban' : 'fa-check'; ?>"></i>
                                        </a>
                                        <a href="?delete=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Không thể thao tác</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Tài khoản hiện tại</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Thống kê -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng người dùng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count($users); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Người dùng hoạt động
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count(array_filter($users, function($u) { return $u['status'] === 'active'; })); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tài khoản bị khóa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count(array_filter($users, function($u) { return $u['status'] === 'banned'; })); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Admin
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count(array_filter($users, function($u) { return $u['role'] === 'admin'; })); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 