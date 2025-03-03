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
                                        <label for="receipt_number" class="form-label">Receipt No:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="receipt_number" name="receipt_number" value="<?= htmlspecialchars($receiptNumber) ?>" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="receipt_date" class="form-label">Date:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="date" class="form-control" id="receipt_date" name="receipt_date" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="payment_for" class="form-label bg-light p-2 w-100">Payment for:</label>
                                <input type="text" class="form-control" id="payment_for" name="payment_for">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="bg-light p-2">
                                    <strong>Customer Details:</strong>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" value="<?= htmlspecialchars($quotation['client_name']) ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="delivery_address" class="form-label">Address:</label>
                                <input type="text" class="form-control" id="delivery_address" name="delivery_address" value="<?= htmlspecialchars($quotation['client_address']) ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Phone No:</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Items -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="15%">No. of Packages</th>
                                        <th width="40%">Description</th>
                                        <th width="20%">Price per Package</th>
                                        <th width="20%">TOTAL</th>
                                        <th width="5%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Items will be added dynamically or from quotation items -->
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr class="item-row">
                                            <td>
                                                <input type="number" min="1" step="1" class="form-control item-quantity" name="items[<?= $index ?>][quantity]" value="<?= $item['quantity'] ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control item-name" name="items[<?= $index ?>][item_name]" value="<?= htmlspecialchars($item['item_name']) ?>" required>
                                                <input type="hidden" name="items[<?= $index ?>][quotation_item_id]" value="<?= $item['id'] ?>">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" step="0.01" class="form-control item-price" name="items[<?= $index ?>][unit_price]" value="<?= number_format($item['final_price'], 2, '.', '') ?>" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control item-total" name="items[<?= $index ?>][amount]" value="<?= number_format($item['final_price'] * $item['quantity'], 2, '.', '') ?>" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end bg-light"><strong>Subtotal</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" class="form-control" id="subtotal" name="subtotal" value="0.00" readonly>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end bg-light"><strong>Tax</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="number" step="0.01" class="form-control" id="tax" name="tax" value="0.00">
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end bg-light"><strong>TOTAL</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" class="form-control" id="total" name="total" value="0.00" readonly>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success" id="addItemBtn">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Notes:</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">If you have any questions about this invoice, please contact 222-555-7777 | uscompany@inc.com</textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="driver_name" class="form-label">Driver Name:</label>
                                <input type="text" class="form-control" id="driver_name" name="driver_name">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="received_by" class="form-label">Received By:</label>
                                <input type="text" class="form-control" id="received_by" name="received_by" required>
                            </div>
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
            <input type="number" min="1" step="1" class="form-control item-quantity" name="items[{index}][quantity]" value="1" required>
        </td>
        <td>
            <input type="text" class="form-control item-name" name="items[{index}][item_name]" required>
            <input type="hidden" name="items[{index}][quotation_item_id]" value="">
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" step="0.01" class="form-control item-price" name="items[{index}][unit_price]" value="0.00">
            </div>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" step="0.01" class="form-control item-total" name="items[{index}][amount]" value="0.00" readonly>
            </div>
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
    const taxInput = document.getElementById('tax');
    let itemCount = <?= count($items) ?>;
    
    // Calculate totals on page load
    calculateTotals();
    
    // Add item row
    addItemBtn.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = itemRowTemplate.replace(/{index}/g, itemCount);
        itemsTable.appendChild(newRow);
        
        // Set up event listeners for the new row
        setupRowListeners(newRow);
        itemCount++;
        
        // Recalculate totals
        calculateTotals();
    });
    
    // Setup event listeners for existing rows
    document.querySelectorAll('.item-row').forEach(row => {
        setupRowListeners(row);
    });
    
    // Listen for changes to the tax input
    taxInput.addEventListener('input', calculateTotals);
    
    // Setup event listeners for a row
    function setupRowListeners(row) {
        const quantityInput = row.querySelector('.item-quantity');
        const priceInput = row.querySelector('.item-price');
        const totalInput = row.querySelector('.item-total');
        const removeBtn = row.querySelector('.remove-item');
        
        // Calculate row total when quantity or price changes
        quantityInput.addEventListener('input', updateRowTotal);
        priceInput.addEventListener('input', updateRowTotal);
        
        // Remove row
        removeBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item?')) {
                row.remove();
                calculateTotals();
            }
        });
        
        function updateRowTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            
            totalInput.value = total.toFixed(2);
            
            // Recalculate all totals
            calculateTotals();
        }
    }
    
    // Calculate subtotal, tax, and total
    function calculateTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            subtotal += parseFloat(row.querySelector('.item-total').value) || 0;
        });
        
        // Tax is now editable and not automatically calculated
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const total = subtotal + tax;
        
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }
});
</script>

<?php include __DIR__ . '/../partials/foot.php' ?>