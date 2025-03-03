<?php
// File path: views/quotations/create.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Quotation</h1>
        <a href="<?= $baseUrl ?>/quotations" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Quotations
        </a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/quotations/store" method="POST" id="quotationForm">
        <div class="row">
            <!-- Quotation Header -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quotation Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="quote_number" class="form-label">Quote Number</label>
                                <input type="text" class="form-control" id="quote_number" name="quote_number" value="<?= htmlspecialchars($quoteNumber) ?>" readonly>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="quote_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="quote_date" name="quote_date" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="agency_id" class="form-label">Agency</label>
                                <select class="form-select" id="agency_id" name="agency_id">
                                    <option value="">-- Select Agency --</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= $agency['id'] ?>"><?= htmlspecialchars($agency['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">Client Name</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="client_email" class="form-label">Client Email</label>
                                <input type="email" class="form-control" id="client_email" name="client_email">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_address" class="form-label">Client Address</label>
                                <textarea class="form-control" id="client_address" name="client_address" rows="2"></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="budget" class="form-label">Budget (₱)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="budget" name="budget" placeholder="0.00">
                                <small class="text-muted">Maximum amount the client is willing to spend</small>
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
            
            <!-- Quotation Items -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quotation Items</h5>
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
                                        <th width="10%">Quantity</th>
                                        <th width="15%">Unit Price (₱)</th>
                                        <th width="10%">Markup (%)</th>
                                        <th width="15%">Final Price (₱)</th>
                                        <th width="15%">Amount (₱)</th>
                                        <th width="5%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Items will be added dynamically -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                        <td>₱<span id="totalAmount">0.00</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="col-md-12 d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                <a href="<?= $baseUrl ?>/quotations" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Quotation</button>
            </div>
        </div>
    </form>
</div>

<!-- Hidden row template for JS -->
<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <input type="text" class="form-control item-name" name="items[{index}][item_name]" required>
        </td>
        <td>
            <input type="number" min="1" step="1" class="form-control item-quantity" name="items[{index}][quantity]" value="1" required>
        </td>
        <td>
            <input type="number" min="0" step="0.01" class="form-control item-unit-price" name="items[{index}][unit_price]" placeholder="0.00" required>
        </td>
        <td>
            <input type="number" min="0" step="0.01" class="form-control item-markup" name="items[{index}][markup_percentage]" value="0" required>
        </td>
        <td>
            <input type="number" min="0" step="0.01" class="form-control item-final-price" name="items[{index}][final_price]" placeholder="0.00" readonly>
        </td>
        <td>
            <input type="number" min="0" step="0.01" class="form-control item-amount" name="items[{index}][amount]" placeholder="0.00" readonly>
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
        const totalAmountSpan = document.getElementById('totalAmount');
        const totalAmountInput = document.getElementById('total_amount');
        const itemRowTemplate = document.getElementById('itemRowTemplate').innerHTML;
        let itemCount = 0;
        
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
        
        // Setup event listeners for a row
        function setupRowListeners(row) {
            const unitPriceInput = row.querySelector('.item-unit-price');
            const markupInput = row.querySelector('.item-markup');
            const quantityInput = row.querySelector('.item-quantity');
            const finalPriceInput = row.querySelector('.item-final-price');
            const amountInput = row.querySelector('.item-amount');
            const removeBtn = row.querySelector('.remove-item');
            
            // Calculate final price when unit price or markup changes
            unitPriceInput.addEventListener('input', updateCalculations);
            markupInput.addEventListener('input', updateCalculations);
            quantityInput.addEventListener('input', updateCalculations);
            
            // Remove row
            removeBtn.addEventListener('click', function() {
                row.remove();
                calculateTotal();
            });
            
            // Initial calculations
            updateCalculations();
            
            function updateCalculations() {
                const unitPrice = parseFloat(unitPriceInput.value) || 0;
                const markup = parseFloat(markupInput.value) || 0;
                const quantity = parseInt(quantityInput.value) || 1;
                
                // Calculate final price with markup
                const finalPrice = unitPrice * (1 + (markup / 100));
                finalPriceInput.value = finalPrice.toFixed(2);
                
                // Calculate amount (final price * quantity)
                const amount = finalPrice * quantity;
                amountInput.value = amount.toFixed(2);
                
                // Recalculate total
                calculateTotal();
            }
        }
        
        // Calculate total amount
        function calculateTotal() {
            let total = 0;
            const amountInputs = itemsTable.querySelectorAll('.item-amount');
            
            amountInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            
            totalAmountSpan.textContent = total.toFixed(2);
            totalAmountInput.value = total.toFixed(2);
            
            // Check against budget
            const budget = parseFloat(document.getElementById('budget').value) || 0;
            if (budget > 0 && total > budget) {
                totalAmountSpan.classList.add('text-danger');
            } else {
                totalAmountSpan.classList.remove('text-danger');
            }
        }
        
        // Check for budget changes
        document.getElementById('budget').addEventListener('input', function() {
            calculateTotal();
        });
        
        // Add the first row by default
        addItemBtn.click();
    });
</script>

<?php include __DIR__ . '/../partials/foot.php' ?>