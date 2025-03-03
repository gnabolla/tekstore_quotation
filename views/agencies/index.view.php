<?php
// File path: views/agencies/index.view.php

include __DIR__ . '/../partials/head.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Agencies</h1>
        <a href="<?= $baseUrl ?>/agencies/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Agency
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Contact Person</th>
                            <th>Contact Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agencies)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No agencies found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($agencies as $agency): ?>
                                <tr>
                                    <td><?= $agency['id'] ?></td>
                                    <td><?= htmlspecialchars($agency['name']) ?></td>
                                    <td><?= htmlspecialchars($agency['address']) ?></td>
                                    <td><?= htmlspecialchars($agency['email']) ?></td>
                                    <td><?= htmlspecialchars($agency['contact_person']) ?></td>
                                    <td><?= htmlspecialchars($agency['contact_number']) ?></td>
                                    <td>
                                        <a href="<?= $baseUrl ?>/agencies/edit?id=<?= $agency['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= $baseUrl ?>/agencies/delete?id=<?= $agency['id'] ?>" class="btn btn-sm btn-danger btn-delete" title="Delete">
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