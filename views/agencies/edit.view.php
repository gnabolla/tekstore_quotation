<?php
// File path: views/agencies/edit.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Agency</h1>
        <a href="<?= $baseUrl ?>/agencies" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Agencies
        </a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= $baseUrl ?>/agencies/update" method="POST">
                <input type="hidden" name="id" value="<?= $agency['id'] ?>">
                
                <div class="mb-3">
                    <label for="name" class="form-label">Agency Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($agency['name']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($agency['address']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($agency['email']) ?>">
                </div>
                
                <div class="mb-3">
                    <label for="contact_person" class="form-label">Contact Person</label>
                    <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?= htmlspecialchars($agency['contact_person']) ?>">
                </div>
                
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?= htmlspecialchars($agency['contact_number']) ?>">
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= $baseUrl ?>/agencies" class="btn btn-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Agency</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?>