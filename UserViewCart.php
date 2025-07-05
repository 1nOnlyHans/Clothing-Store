<?php
include "./includes/navbar.php";
include "./includes/dashboard_session.php";
?>

<div class="container mt-5">
    <h1 class="text-center">My Cart</h1>
    <div class="card">
        <div class="card-body" id="container">

        </div>
    </div>
</div>
<?php
    include "./includes/OrderFormDetails.php";
?>
<script>
    $(document).ready(function() {
        getCartItems();

        function getCartItems() {
            $.ajax({
                method: "GET",
                url: "./controllers/GetCartItems.php",
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    const container = $('#container');
                    if (response.length > 0) {
                        const items = response.map((item) => `
                        <div class="row align-items-center mb-3 border-bottom pb-3">
                            <!-- Image column -->
                            <div class="col-md-2">
                            <div style="width: 100%; height: 100px; overflow: hidden;">
                                <img src="./public/uploads/variant_images/${item.image}"
                                    alt="Variant Image"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                    class="rounded">
                            </div>
                            </div>

                            <!-- Inline details with fixed widths -->
                            <div class="col-md-10">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-truncate">
                                <p class="mb-0" title="${item.product_name}">
                                    <strong>Product:</strong> ${item.product_name}
                                </p>
                                </div>
                                <div class="col-md-1 text-truncate">
                                <p class="mb-0"><strong>Size:</strong> ${item.size}</p>
                                </div>
                                <div class="col-md-1 text-truncate">
                                <p class="mb-0"><strong>Color:</strong> ${item.color}</p>
                                </div>
                                <div class="col-md-1 text-truncate">
                                <p class="mb-0"><strong>Qty:</strong> ${item.quantity}</p>
                                </div>
                                <div class="col-md-2 text-truncate">
                                <p class="mb-0"><strong>Unit:</strong> ₱${parseFloat(item.item_price).toFixed(2)}</p>
                                </div>
                                <div class="col-md-2 text-truncate">
                                <p class="mb-0"><strong>Total:</strong> ₱${parseFloat(item.total_price).toFixed(2)}</p>
                                </div>
                                <div class="col-md-2">
                                <form method="post" class="d-flex align-items-center gap-2 editQuantity-form mb-0">
                                    <input type="hidden" name="cartID" value="${item.id}">
                                    <input type="hidden" name="variantID" value="${item.variant_id}">
                                    <input type="number" name="quantity" value="${item.quantity}" min="1" max="${item.item_stocks}" 
                                    class="form-control form-control-sm" style="width: 70px;">
                                    <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                                </form>
                                <button class="btn btn-danger delete" data-id="${item.id}">Delete</button>
                                </div>
                            </div>
                            </div>
                        </div>
                        `).join("");
                        let total = 0;
                        response.forEach((item) => {
                            total += parseFloat(item.total_price);
                        });
                        const totalPriceLabel = `
                            <h1 class="text-center">Total Price: ${total}</h1>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" id="placeOrderBtn" data-bs-target="#OrderFormModal" data-totalPrice="${total}">
                                    Place Order
                                </button>
                            </div>
                        `;
                            container.empty();
                            container.append(items);
                            container.append(totalPriceLabel);
                    }
                    else{
                        container.empty();
                        container.append('<div class="alert alert-info bg-info text-white">Your cart is empty</div>');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $(document).on('submit','.editQuantity-form',function(event){
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                method: "post",
                url: "./controllers/EditCartItemQuantity.php",
                data: formData,
                dataType: "json",
                success: function(response){
                    if(response.status === "success"){
                        getCartItems();
                        Swal.fire({
                            icon: "success",
                            title: response.message
                        });
                    }
                    else{
                        Swal.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                },error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
        });

        $(document).on('click','#placeOrderBtn',function(){
            const total = $(this).attr('data-totalPrice');
            $('#OrderFormModal input[name="total_amount"]').val(total);
        });
        
        $('#place-order-form').on('submit',function(event){
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                method: "post",
                url: "./controllers/PlaceOrder.php",
                data: formData,
                dataType: "json",
                success: function(response){
                    if(response.status === "success"){
                        getCartItems();
                        Swal.fire({
                            icon: "success",
                            title: response.message
                        });
                    }
                    else{
                        Swal.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                },error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
        });

        $(document).on('click','.delete',function(){
            const cartID = $(this).attr("data-id");
            $.ajax({
                method: "post",
                url: "./controllers/DeleteCartItem.php",
                data: {
                    cartID: cartID
                },
                dataType: "json",
                success: function(response){
                    if(response.status === "success"){
                        getCartItems();
                        Swal.fire({
                            icon: "success",
                            title: response.message
                        });
                    }
                    else{
                        Swal.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                },error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>