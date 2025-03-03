<?php
// File path: views/quotations/index.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quotations</h1>
        <a href="<?= $baseUrl ?>/quotations/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Quotation
        </a>
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
                            <th>Quote #</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Agency</th>
                            <th>Budget</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quotations)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No quotations found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quotations as $quotation): ?>
                                <tr>
                                    <td><?= htmlspecialchars($quotation['quote_number']) ?></td>
                                    <td><?= htmlspecialchars($quotation['quote_date']) ?></td>
                                    <td><?= htmlspecialchars($quotation['client_name']) ?></td>
                                    <td><?= htmlspecialchars($quotation['agency_name'] ?? 'N/A') ?></td>
                                    <td>₱<?= number_format($quotation['budget'], 2) ?></td>
                                    <td>₱<?= number_format($quotation['total'], 2) ?></td>
                                    <td>
                                        <?php if ($quotation['status'] === 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($quotation['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($quotation['status'] === 'declined'): ?>
                                            <span class="badge bg-danger">Declined</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= $baseUrl ?>/quotations/view?id=<?= $quotation['id'] ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= $baseUrl ?>/quotations/edit?id=<?= $quotation['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= $baseUrl ?>/quotations/pdf?id=<?= $quotation['id'] ?>" class="btn btn-sm btn-secondary" title="Generate PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="<?= $baseUrl ?>/quotations/delete?id=<?= $quotation['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Delete">
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