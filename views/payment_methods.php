<?php
// Get the root directory
$root = dirname(dirname(__FILE__));

// Include Payment model
require_once $root . '/models/Payment.php';

// Create Payment model instance
$paymentModel = new Payment($conn);

// Get user's payment methods
$paymentMethods = [];
if (isLoggedIn()) {
    $paymentMethods = $paymentModel->getPaymentMethods($_SESSION['user_id']);
}
?>

<div class="container py-5">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Payment Methods</h2>
        </div>
        <div class="col-auto">
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">
                <i class="fas fa-plus me-2"></i>Add Payment Method
            </a>
        </div>
    </div>

    <?php if (!isLoggedIn()): ?>
        <div class="alert alert-info">
            Please <a href="index.php?page=login" class="alert-link">login</a> to manage your payment methods.
        </div>
    <?php else: ?>
        <?php if (empty($paymentMethods)): ?>
            <div class="alert alert-info">
                You haven't added any payment methods yet. Click the "Add Payment Method" button to add one.
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach ($paymentMethods as $method): ?>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($method['type']); ?></h5>
                                        <p class="card-text text-muted small"><?php echo htmlspecialchars($method['last_four']); ?></p>
                                    </div>
                                    <button class="btn btn-danger btn-sm" onclick="deletePaymentMethod(<?php echo $method['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <?php if (!empty($method['expiry_date'])): ?>
                                    <p class="card-text small">
                                        <i class="fas fa-calendar me-2"></i>
                                        Expires: <?php echo htmlspecialchars($method['expiry_date']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Add Payment Method Modal -->
<div class="modal fade" id="addPaymentMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addPaymentMethodForm">
                    <div class="mb-3">
                        <label for="paymentType" class="form-label">Payment Type</label>
                        <select class="form-select" id="paymentType" name="paymentType" required>
                            <option value="">Select payment type</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cardNumber" class="form-label">Card Number</label>
                        <input type="text" class="form-control" id="cardNumber" name="cardNumber" 
                               placeholder="1234 5678 9012 3456" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expiryDate" class="form-label">Expiry Date</label>
                            <input type="text" class="form-control" id="expiryDate" name="expiryDate" 
                                   placeholder="MM/YY" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" 
                                   placeholder="123" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="billingAddress" class="form-label">Billing Address</label>
                        <textarea class="form-control" id="billingAddress" name="billingAddress" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="addPaymentMethod()">Add Payment Method</button>
            </div>
        </div>
    </div>
</div>

<script>
function addPaymentMethod() {
    const form = document.getElementById('addPaymentMethodForm');
    const formData = new FormData(form);
    
    fetch('api/payments/methods', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            location.reload();
        } else {
            alert(data.error || 'Failed to add payment method');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the payment method');
    });
}

function deletePaymentMethod(methodId) {
    if (!confirm('Are you sure you want to delete this payment method?')) {
        return;
    }
    
    fetch(`api/payments/methods/${methodId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            location.reload();
        } else {
            alert(data.error || 'Failed to delete payment method');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the payment method');
    });
}
</script>
