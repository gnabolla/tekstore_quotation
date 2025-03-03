<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/db_connect.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Delivery Receipts</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Receipt ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Customer</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query to fetch delivery receipts
                                $query = "SELECT dr.*, c.customer_name 
                                          FROM delivery_receipts dr 
                                          JOIN customers c ON dr.customer_id = c.id 
                                          ORDER BY dr.created_at DESC";
                                
                                // If the delivery_receipts table doesn't exist yet, you'll need to modify this query
                                // or handle the case where the table doesn't exist
                                
                                try {
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($receipts) > 0) {
                                        foreach ($receipts as $receipt) {
                                            ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <p class="text-xs font-weight-bold mb-0"><?= $receipt['id'] ?></p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($receipt['customer_name']) ?></p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($receipt['created_at']) ?></p>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm bg-gradient-success"><?= htmlspecialchars($receipt['status']) ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="pdf.php?id=<?= $receipt['id'] ?>" class="btn btn-link text-danger text-gradient px-3 mb-0" target="_blank">
                                                        <i class="far fa-file-pdf me-2"></i>View PDF
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">No delivery receipts found</td></tr>';
                                    }
                                } catch (PDOException $e) {
                                    echo '<tr><td colspan="5" class="text-center">Unable to fetch delivery receipts. The delivery receipts table may not exist yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
