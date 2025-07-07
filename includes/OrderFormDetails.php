<div class="modal fade" id="OrderFormModal" tabindex="-1" aria-labelledby="OrderFormModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content" id="place-order-form">

      <div class="modal-header">
        <h1 class="modal-title fs-5" id="OrderFormModalLabel">Place Your Order</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


      <div class="modal-body">
        <input type="hidden" name="total_amount" id="total_amount">
        <div class="mb-3">
          <label for="shipping_address" class="form-label">Shipping Address</label>
          <textarea class="form-control" name="shipping_address" id="shipping_address" rows="3" required></textarea>
        </div>


        <div class="mb-3">
          <label for="payment_method" class="form-label">Payment Method</label>
          <select class="form-select" name="payment_method" id="payment_method" required>
            <option value="">Select Payment Method</option>
            <option value="COD">Cash On Delivery</option>
            <option value="Gcash">GCash</option>
          </select>
        </div>

        <div class="mb-3 d-none" id="gcash_number_container">
          <label for="payment_method" class="form-label">Gcash Number</label>
          <input type="text" name="gcash_number" id="gcash_number" maxlength="11" class="form-control" placeholder="Enter your Gcash number">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Place Order</button>
        </div>
    </form>
  </div>
</div>