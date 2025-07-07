<?php
include "./includes/navbar.php";
include "./includes/dashboard_session.php";
require "./Classes/Dbh.php";
require "./Classes/Order.php";
?>

<div class="container mt-5">
    <h1 class="text-center">Notifications</h1>
    <div class="card">
        <div class="card-body">
            <div class="row" id="container">

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        fetchMessages();

        function fetchMessages() {
            $.ajax({
                method: "GET",
                url: "./controllers/GetDeliverMessage.php",
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    const container = $('#container');
                    container.empty();

                    if (response.length > 0) {
                        const messages = response.map((item) => `
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center g-3">
                            <div class="col-3 py-3">
                                <p>Order Number: <span class="text-primary">${item.order_number}</span></p>
                            </div>
                            <div class="col-4 py-3">
                                <p>Message: ${item.message}</p>
                            </div>
                            <div class="col-3 py-3">
                                <p>Date: ${item.created_at}</p>
                            </div>
                            <div class="col-2 py-3 text-end">
                                <a href="UserViewOrderDetails.php?order_id=${item.order_id}" class="btn btn-primary btn-sm">View</a>
                            </div>
                            </div>
                        </div>
                    </div>
                    `).join("");

                        container.append(messages);
                    } else {
                        container.append("<p class='text-secondary'>No messages</p>");
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
</script>