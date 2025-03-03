<?php
// File path: views/delivery-receipts/view.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>View Delivery Receipt</h1>
        <div>
            <a href="<?= $baseUrl ?>/delivery-receipts" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Delivery Receipts
            </a>
            <a href="<?= $baseUrl ?>/delivery-receipts/pdf?id=<?= $receipt['id'] ?>" class="btn btn-info me-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Generate PDF
            </a>
            <a href="<?= $baseUrl ?>/delivery-receipts/edit?id=<?= $receipt['id'] ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
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
        <!-- Receipt Header -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Delivery Receipt Details</h5>
                    <div>
                        <?php if ($receipt['status'] === 'pending'): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php elseif ($receipt['status'] === 'delivered'): ?>
                            <span class="badge bg-success">Delivered</span>
                        <?php elseif ($receipt['status'] === 'cancelled'): ?>
                            <span class="badge bg-danger">Cancelled</span>
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
                                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($receipt['client_name']) ?></p>
                                <?php if (!empty($receipt['delivery_address'])): ?>
                                    <p class="mb-1"><strong>Delivery Address:</strong> <?= htmlspecialchars($receipt['delivery_address']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($receipt['received_by'])): ?>
                                    <p class="mb-1"><strong>Received By:</strong> <?= htmlspecialchars($receipt['received_by']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($receipt['contact_number'])): ?>
                                    <p class="mb-1"><strong>Contact Number:</strong> <?= htmlspecialchars($receipt['contact_number']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($receipt['notes'])): ?>
                                <div class="mb-3">
                                    <h6>Notes</h6>
                                    <p class="mb-1"><?= nl2br(htmlspecialchars($receipt['notes'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6>Receipt Information</h6>
                                <p class="mb-1"><strong>Receipt Number:</strong> <?= htmlspecialchars($receipt['receipt_number']) ?></p>
                                <p class="mb-1"><strong>Date:</strong> <?= date('F j, Y', strtotime($receipt['receipt_date'])) ?></p>
                                <p class="mb-1"><strong>Quotation Number:</strong> <?= htmlspecialchars($receipt['quote_number']) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <h6>Delivery Information</h6>
                                <?php if (!empty($receipt['driver_name'])): ?>
                                    <p class="mb-1"><strong>Driver Name:</strong> <?= htmlspecialchars($receipt['driver_name']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($receipt['vehicle_details'])): ?>
                                    <p class="mb-1"><strong>Vehicle Details:</strong> <?= htmlspecialchars($receipt['vehicle_details']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($receipt['agency_name'])): ?>
                                <div class="mb-3">
                                    <h6>Agency Information</h6>
                                    <p class="mb-1"><strong>Agency:</strong> <?= htmlspecialchars($receipt['agency_name']) ?></p>
                                    <?php if (!empty($receipt['agency_address'])): ?>
                                        <p class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($receipt['agency_address']) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Receipt Items -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Delivered Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No items found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td><?= htmlspecialchars($item['unit']) ?></td>
                                            <td><?= htmlspecialchars($item['remarks']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
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
                    <form action="<?= $baseUrl ?>/delivery-receipts/update-status" method="POST" class="d-flex align-items-center">
                        <input type="hidden" name="id" value="<?= $receipt['id'] ?>">
                        <select class="form-select me-3" name="status">
                            <option value="pending" <?= $receipt['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="delivered" <?= $receipt['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $receipt['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Signature Area -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Signatures</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <div style="border-bottom: 1px solid #ddd; padding-top: 50px; margin-bottom: 10px;"></div>
                            <p><strong>Delivered by:</strong> <?= htmlspecialchars($receipt['driver_name'] ?: '____________________') ?></p>
                        </div>
                        <div class="col-md-6 text-center">
                            <div style="border-bottom: 1px solid #ddd; padding-top: 50px; margin-bottom: 10px;"></div>
                            <p><strong>Received by:</strong> <?= htmlspecialchars($receipt['received_by'] ?: '____________________') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?>