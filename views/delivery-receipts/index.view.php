<?php
// File path: views/delivery-receipts/index.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Delivery Receipts</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Agency</th>
                            <th>Payment For</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deliveryReceipts)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No delivery receipts found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($deliveryReceipts as $receipt): ?>
                                <tr>
                                    <td><?= htmlspecialchars($receipt['receipt_number']) ?></td>
                                    <td><?= date('d-M-Y', strtotime($receipt['receipt_date'])) ?></td>
                                    <td><?= htmlspecialchars($receipt['client_name']) ?></td>
                                    <td><?= htmlspecialchars($receipt['agency_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($receipt['payment_for'] ?? 'N/A') ?></td>
                                    <td>₱<?= number_format($receipt['total_amount'] ?? 0, 2) ?></td>
                                    <td>
                                        <?php if ($receipt['status'] === 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($receipt['status'] === 'delivered'): ?>
                                            <span class="badge bg-success">Delivered</span>
                                        <?php elseif ($receipt['status'] === 'cancelled'): ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= $baseUrl ?>/delivery-receipts/view?id=<?= $receipt['id'] ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= $baseUrl ?>/delivery-receipts/edit?id=<?= $receipt['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= $baseUrl ?>/delivery-receipts/pdf?id=<?= $receipt['id'] ?>" class="btn btn-sm btn-secondary" title="Generate PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="<?= $baseUrl ?>/delivery-receipts/delete?id=<?= $receipt['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?>