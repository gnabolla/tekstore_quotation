<?php
// File path: views/quotations/view.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>View Quotation</h1>
        <div>
            <a href="<?= $baseUrl ?>/quotations" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Quotations
            </a>
            <a href="<?= $baseUrl ?>/quotations/pdf?id=<?= $quotation['id'] ?>" class="btn btn-info me-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Generate PDF
            </a>
            <a href="<?= $baseUrl ?>/quotations/edit?id=<?= $quotation['id'] ?>" class="btn btn-primary me-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <?php if ($quotation['status'] === 'approved'): ?>
                <a href="<?= $baseUrl ?>/delivery-receipts/create?quotation_id=<?= $quotation['id'] ?>" class="btn btn-success">
                    <i class="fas fa-truck"></i> Create Delivery Receipt
                </a>
            <?php endif; ?>
        </div>
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

    <div class="row">
        <!-- Quotation Header -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Quotation Details</h5>
                    <div>
                        <?php if ($quotation['status'] === 'pending'): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php elseif ($quotation['status'] === 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif ($quotation['status'] === 'declined'): ?>
                            <span class="badge bg-danger">Declined</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6>Business Information</h6>
                                <p class="mb-1">
                                    <strong>Tekstore Computer Parts and Accessories Trading</strong>
                                </p>
                                <p class="mb-1"><small>Fast and Quality Business Solution</small></p>
                                <p class="mb-1">Magsaysay Street, Bantug, Roxas, Isabela</p>
                            </div>

                            <div class="mb-3">
                                <h6>Client Information</h6>
                                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($quotation['client_name']) ?></p>
                                <?php if (!empty($quotation['client_email'])): ?>
                                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($quotation['client_email']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($quotation['client_address'])): ?>
                                    <p class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($quotation['client_address']) ?></p>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($quotation['notes'])): ?>
                                <div class="mb-3">
                                    <h6>Notes</h6>
                                    <p class="mb-1"><?= nl2br(htmlspecialchars($quotation['notes'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6>Quotation Information</h6>
                                <p class="mb-1"><strong>Quote Number:</strong> <?= htmlspecialchars($quotation['quote_number']) ?></p>
                                <p class="mb-1"><strong>Date:</strong> <?= date('F j, Y', strtotime($quotation['quote_date'])) ?></p>

                                <?php if (!empty($quotation['budget'])): ?>
                                    <p class="mb-1">
                                        <strong>Budget:</strong> ₱<?= number_format($quotation['budget'], 2) ?>
                                        <?php if ($quotation['total'] > $quotation['budget']): ?>
                                            <span class="badge bg-danger">Exceeds Budget</span>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($quotation['agency_name'])): ?>
                                <div class="mb-3">
                                    <h6>Agency Information</h6>
                                    <p class="mb-1"><strong>Agency:</strong> <?= htmlspecialchars($quotation['agency_name']) ?></p>
                                    <?php if (!empty($quotation['agency_address'])): ?>
                                        <p class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($quotation['agency_address']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($quotation['contact_person'])): ?>
                                        <p class="mb-1"><strong>Contact Person:</strong> <?= htmlspecialchars($quotation['contact_person']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($quotation['contact_number'])): ?>
                                        <p class="mb-1"><strong>Contact Number:</strong> <?= htmlspecialchars($quotation['contact_number']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($quotation['agency_email'])): ?>
                                        <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($quotation['agency_email']) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotation Items -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quotation Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price (₱)</th>
                                    <th>Markup (%)</th>
                                    <th>Final Price (₱)</th>
                                    <th>Amount (₱)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No items found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td class="text-end"><?= number_format($item['unit_price'], 2) ?></td>
                                            <td class="text-end"><?= number_format($item['markup_percentage'], 2) ?>%</td>
                                            <td class="text-end"><?= number_format($item['final_price'], 2) ?></td>
                                            <td class="text-end"><?= number_format($item['amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>₱<?= number_format($quotation['total'], 2) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Status</h5>
                </div>
                <div class="card-body">
                    <form action="<?= $baseUrl ?>/quotations/update-status" method="POST" class="d-flex align-items-center">
                        <input type="hidden" name="id" value="<?= $quotation['id'] ?>">
                        <select class="form-select me-3" name="status">
                            <option value="pending" <?= $quotation['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $quotation['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="declined" <?= $quotation['status'] === 'declined' ? 'selected' : '' ?>>Declined</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?>