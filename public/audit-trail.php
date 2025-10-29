<?php
require "include/header.php";
require_once "controller/AuditTrail.php";

// Check if user has audit access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_auditor']) || !$_SESSION['is_auditor']) {
    header("Location: dashboard/index.php");
    exit();
}

$auditTrail = AuditTrail::getInstance();

// Get filter values
$filters = [
    'user_id' => $_GET['user_id'] ?? null,
    'action' => $_GET['action'] ?? null,
    'table_name' => $_GET['table'] ?? null,
    'date_from' => $_GET['date_from'] ?? null,
    'date_to' => $_GET['date_to'] ?? null
];

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$result = $auditTrail->getLogs($filters, $page);

// Get filter options
$actions = $auditTrail->getActions();
$tables = $auditTrail->getTables();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Audit Trail</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Action</label>
                                    <select name="action" class="form-control">
                                        <option value="">All Actions</option>
                                        <?php foreach ($actions as $action): ?>
                                            <option value="<?php echo htmlspecialchars($action); ?>"
                                                    <?php echo $filters['action'] === $action ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($action); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Table</label>
                                    <select name="table" class="form-control">
                                        <option value="">All Tables</option>
                                        <?php foreach ($tables as $table): ?>
                                            <option value="<?php echo htmlspecialchars($table); ?>"
                                                    <?php echo $filters['table_name'] === $table ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($table); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                           value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                           value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Results Table -->
                    <?php if ($result['total'] > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Table</th>
                                        <th>Details</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result['logs'] as $log): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                            <td>
                                                <?php if ($log['user_id']): ?>
                                                    <?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?>
                                                    <br>
                                                    <small><?php echo htmlspecialchars($log['email']); ?></small>
                                                <?php else: ?>
                                                    System
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                                            <td><?php echo htmlspecialchars($log['table_name']); ?></td>
                                            <td>
                                                <?php
                                                    $changes = $auditTrail->formatChanges(
                                                        $log['old_values'],
                                                        $log['new_values']
                                                    );
                                                    foreach ($changes as $change):
                                                ?>
                                                    <div class="mb-1">
                                                        <strong><?php echo htmlspecialchars($change['field']); ?>:</strong>
                                                        <?php if ($change['old'] !== null): ?>
                                                            <span class="text-danger"><?php echo htmlspecialchars($change['old']); ?></span>
                                                            →
                                                        <?php endif; ?>
                                                        <span class="text-success"><?php echo htmlspecialchars($change['new']); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($result['pages'] > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $result['pages']; $i++): ?>
                                        <li class="page-item <?php echo $i === $result['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">No audit records found matching your criteria.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add datepicker functionality if needed
$(document).ready(function() {
    $('select').change(function() {
        this.form.submit();
    });
});
</script>

<?php require "include/footer.php"; ?>