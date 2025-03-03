<?php
// File path: views/index.view.php

include 'partials/head.php' 
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Dashboard</h1>
            <p class="text-muted">Welcome to Tekstore Quotation System</p>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Quotations Count -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Quotations</h6>
                            <h2 class="mb-0"><?= $quotationCount ?></h2>
                        </div>
                        <i class="fas fa-file-invoice-dollar fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?= $baseUrl ?>/quotations" class="text-white text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Pending Quotations -->
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Pending Quotations</h6>
                            <h2 class="mb-0"><?= $pendingCount ?></h2>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?= $baseUrl ?>/quotations" class="text-dark text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Approved Quotations -->
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Approved Quotations</h6>
                            <h2 class="mb-0"><?= $approvedCount ?></h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?= $baseUrl ?>/quotations" class="text-white text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Agencies Count -->
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Agencies</h6>
                            <h2 class="mb-0"><?= $agencyCount ?></h2>
                        </div>
                        <i class="fas fa-building fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?= $baseUrl ?>/agencies" class="text-white text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Quotations</h5>
                    <a href="<?= $baseUrl ?>/quotations" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Quote #</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Agency</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentQuotations)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No quotations found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentQuotations as $quotation): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($quotation['quote_number']) ?></td>
                                            <td><?= htmlspecialchars($quotation['quote_date']) ?></td>
                                            <td><?= htmlspecialchars($quotation['client_name']) ?></td>
                                            <td><?= htmlspecialchars($quotation['agency_name'] ?? 'N/A') ?></td>
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
    </div>
</div>

<?php include 'partials/foot.php' ?>