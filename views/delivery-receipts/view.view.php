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
        <!-- Delivery Receipt Card -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">DELIVERY RECEIPT</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Tekstore Computer Parts and Accessories Trading</h4>
                            <p class="mb-1"><small><em>Fast and Quality Business Solution</em></small></p>
                            <p class="mb-1">Magsaysay Street, Bantug, Roxas, Isabela</p>
                            <p class="mb-1">09166027454</p>
                            <p class="mb-1">tekstore.solution@gmail.com</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-2">
                                <div class="col-md-5">
                                    <strong>Receipt No:</strong>
                                </div>
                                <div class="col-md-7">
                                    <?= htmlspecialchars($receipt['receipt_number']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <strong>Date:</strong>
                                </div>
                                <div class="col-md-7">
                                    <?= date('d-M-Y', strtotime($receipt['receipt_date'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="bg-light p-2">
                                <strong>Payment for:</strong> <?= htmlspecialchars($receipt['payment_for'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12 mb-2">
                            <div class="bg-light p-2">
                                <strong>Customer Details:</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Name:</strong> <?= htmlspecialchars($receipt['client_name']) ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Address:</strong> <?= htmlspecialchars($receipt['delivery_address'] ?? '') ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Phone No:</strong> <?= htmlspecialchars($receipt['contact_number'] ?? '') ?>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>No. of Packages</th>
                                    <th>Description</th>
                                    <th>Price per Package</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No items found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                                            <td class="text-end">$<?= number_format($item['unit_price'] ?? $item['final_price'], 2) ?></td>
                                            <td class="text-end">$<?= number_format($item['total_price'] ?? ($item['quantity'] * ($item['final_price'] ?? 0)), 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end bg-light"><strong>Subtotal</strong></td>
                                    <td class="text-end">₱<?= number_format($receipt['subtotal'] ?? 0, 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end bg-light"><strong>Tax</strong></td>
                                    <td class="text-end">₱<?= number_format($receipt['tax_amount'] ?? 0, 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end bg-light"><strong>TOTAL</strong></td>
                                    <td class="text-end">₱<?= number_format($receipt['total_amount'] ?? 0, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <p><strong>NOTE:</strong> <?= htmlspecialchars($receipt['notes'] ?? 'If you have any questions about this invoice, please contact 09166027454 | tekstore.solution@gmail.com') ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <strong>Signature:</strong>
                            <div class="border-bottom my-3 py-3"></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <strong>Date:</strong>
                            <div class="border-bottom my-3 py-3"></div>
                        </div>
                        <div class="col-12 text-center">
                            <p><em>Thank you for your business!</em></p>
                        </div>
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
        
        <!-- Delivery Details -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Driver Name:</strong> <?= htmlspecialchars($receipt['driver_name'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Received By:</strong> <?= htmlspecialchars($receipt['received_by'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?>