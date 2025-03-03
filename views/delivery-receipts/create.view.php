<?php
// File path: views/delivery-receipts/create.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create Delivery Receipt</h1>
        <a href="<?= $baseUrl ?>/quotations/view?id=<?= $quotationId ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Quotation
        </a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/delivery-receipts/store" method="POST" id="deliveryReceiptForm">
        <input type="hidden" name="quotation_id" value="<?= $quotationId ?>">
        
        <div class="row">
            <!-- Receipt Header -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Delivery Receipt Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="receipt_number" class="form-label">Receipt Number</label>
                                <input type="text" class="form-control" id="receipt_number" name="receipt_number" value="<?= htmlspecialchars($receiptNumber) ?>" readonly>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="receipt_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="receipt_date" name="receipt_date" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="quotation_number" class="form-label">Quotation Number</label>
                                <input type="text" class="form-control" id="quotation_number" value="<?= htmlspecialchars($quotation['quote_number']) ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">Client Name</label>
                                <input type="text" class="form-control" id="client_name" value="<?= htmlspecialchars($quotation['client_name']) ?>" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="delivery_address" class="form-label">Delivery Address</label>
                                <textarea class="form-control" id="delivery_address" name="delivery_address" rows="2"><?= htmlspecialchars($quotation['client_address']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="received_by" class="form-label">Received By</label>
                                <input type="text" class="form-control" id="received_by" name="received_by" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="driver_name" class="form-label">Driver Name</label>
                                <input type="text" class="form-control" id="driver_name" name="driver_name">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_details" class="form-label">Vehicle Details</label>
                                <input type="text" class="form-control" id="vehicle_details" name="vehicle_details" placeholder="e.g., Plate Number, Vehicle Type">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Items -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Items to Deliver</h5>
                        <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th width="40%">Item Name</th>
                                        <th width="15%">Quantity</th>
                                        <th width="15%">Unit</th>
                                        <th width="25%">Remarks</th>
                                        <th width="5%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Items will be added dynamically or from quotation items -->
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr class="item-row">
                                            <td>
                                                <input type="text" class="form-control item-name" name="items[<?= $index ?>][item_name]" value="<?= htmlspecialchars($item['item_name']) ?>" required>
                                                <input type="hidden" name="items[<?= $index ?>][quotation_item_id]" value="<?= $item['id'] ?>">
                                            </td>
                                            <td>
                                                <input type="number" min="1" step="1" class="form-control item-quantity" name="items[<?= $index ?>][quantity]" value="<?= $item['quantity'] ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control item-unit" name="items[<?= $index ?>][unit]" value="pcs">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control item-remarks" name="items[<?= $index ?>][remarks]">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="col-md-12 d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                <a href="<?= $baseUrl ?>/quotations/view?id=<?= $quotationId ?>" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Delivery Receipt</button>
            </div>
        </div>
    </form>
</div>

<!-- Hidden row template for JS -->
<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <input type="text" class="form-control item-name" name="items[{index}][item_name]" required>
            <input type="hidden" name="items[{index}][quotation_item_id]" value="">
        </td>
        <td>
            <input type="number" min="1" step="1" class="form-control item-quantity" name="items[{index}][quantity]" value="1" required>
        </td>
        <td>
            <input type="text" class="form-control item-unit" name="items[{index}][unit]" value="pcs">
        </td>
        <td>
            <input type="text" class="form-control item-remarks" name="items[{index}][remarks]">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsTable = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
        const addItemBtn = document.getElementById('addItemBtn');
        const itemRowTemplate = document.getElementById('itemRowTemplate').innerHTML;
        let itemCount = <?= count($items) ?>;
        
        // Add item row
        addItemBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.innerHTML = itemRowTemplate.replace(/{index}/g, itemCount);
            itemsTable.appendChild(newRow);
            
            // Set up event listeners for the new row
            setupRowListeners(newRow);
            itemCount++;
        });
        
        // Setup event listeners for existing rows
        document.querySelectorAll('.item-row').forEach(row => {
            setupRowListeners(row);
        });
        
        // Setup event listeners for a row
        function setupRowListeners(row) {
            const removeBtn = row.querySelector('.remove-item');
            
            // Remove row
            removeBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    row.remove();
                }
            });
        }
    });
</script>

<?php include __DIR__ . '/../partials/foot.php' ?>