<?php
require "include/header.php";
require_once "controller/TransactionApproval.php";

// Check if user has approval privileges
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_approver']) || !$_SESSION['is_approver']) {
    header("Location: dashboard/index.php");
    exit();
}

$approval = TransactionApproval::getInstance();
$pendingTransactions = $approval->getPendingTransactions();
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Pending Transactions</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['status'])): ?>
                        <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?>">
                            <?php echo htmlspecialchars($_GET['message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($pendingTransactions)): ?>
                        <div class="alert alert-info">No pending transactions found.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Date</th>
                                        <th>Initiator</th>
                                        <th>From Account</th>
                                        <th>To Account</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingTransactions as $tx): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($tx['reference']); ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($tx['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($tx['source_account']); ?></td>
                                            <td><?php echo htmlspecialchars($tx['destination_account']); ?></td>
                                            <td><?php echo number_format($tx['amount'], 2) . ' ' . htmlspecialchars($tx['currency']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="showApproveModal('<?php echo $tx['reference']; ?>')">
                                                    Approve
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="showRejectModal('<?php echo $tx['reference']; ?>')">
                                                    Reject
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Transaction</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="controller/process-approval.php" method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to approve this transaction?</p>
                    <input type="hidden" name="reference" id="approveReference">
                    <input type="hidden" name="action" value="approve">
                    <?php
                        $security = Security::getInstance();
                        $csrf_token = $security->generateCSRFToken();
                    ?>
                    <input type="hidden" name="csrf_token" value="<?php echo $security->escapeOutput($csrf_token); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Transaction</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="controller/process-approval.php" method="POST" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="reference" id="rejectReference">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="csrf_token" value="<?php echo $security->escapeOutput($csrf_token); ?>">
                    
                    <div class="form-group">
                        <label for="reason">Rejection Reason</label>
                        <textarea class="form-control" name="reason" id="reason" required></textarea>
                        <div class="invalid-feedback">
                            Please provide a reason for rejection
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApproveModal(reference) {
    document.getElementById('approveReference').value = reference;
    $('#approveModal').modal('show');
}

function showRejectModal(reference) {
    document.getElementById('rejectReference').value = reference;
    $('#rejectModal').modal('show');
}
</script>

<?php require "include/footer.php"; ?>