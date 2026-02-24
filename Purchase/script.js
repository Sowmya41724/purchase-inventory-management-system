function showUser(str) {
    if (str == "") return;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var parts = this.responseText.split("|");
            var unitValue = parts[0];
            var rateValue = parts[1];

            document.getElementById("rateHint").value = rateValue;
            var selectElement = document.getElementById("Unit");
            if (selectElement) {
                // Check if the unitValue exists as an option
                var optionExists = false;
                for (var i = 0; i < selectElement.options.length; i++) {
                    if (selectElement.options[i].value === unitValue) {
                        optionExists = true;
                        break;
                    }
                }
                // If not, add it
                if (!optionExists && unitValue) {
                    var opt = document.createElement("option");
                    opt.value = unitValue;
                    opt.text = unitValue;
                    opt.selected = true;
                    selectElement.appendChild(opt);
                } else {
                    selectElement.value = unitValue;
                }
            }
            Add();
        }
    };
    xmlhttp.open("GET", "value.php?q=" + str, true);
    xmlhttp.send();
}

function Add() {
    var qty = parseFloat(document.getElementById("Quantity").value) || 0;
    var rate = parseFloat(document.getElementById("rateHint").value) || 0;
    document.getElementById("Amount").value = (qty * rate).toFixed(2);
}

$(document).ready(function () {
    calculateTotal();
});

$('#addItem').on('click', function () {
    var product = "";
    var unit = "";
    var quantity = "";
    var rate = "";
    var amount = "";
    var count = 0;

    if ($('select[name="product"]').length > 0) {
        product = $('select[name="product"] option:selected').text().trim();
    }

    if ($('select[name="unit"]').length > 0) {
        unit = $('select[name="unit"]').val();
    }

    if ($('input[type="number"][name="quantity"]').length > 0) {
        quantity = $('input[type="number"][name="quantity"]').val();
    }

    if ($('input[type="text"][name="rate"]').length > 0) {
        rate = $('input[type="text"][name="rate"]').val();
    }

    if ($('input[type="text"][name="amount"]').length > 0) {
        amount = $('input[type="text"][name="amount"]').val();
    }

    $('.table-error').text('');

    if (!product || product === 'Select' || !unit || !quantity || !rate || !amount) {
        alert("Please select a product and fill all fields before adding to table");
        return;
    }

    let isDuplicate = false;
    $('#purchaseTableBody tr').each(function () {
        let existingProduct = $(this).find('td:eq(1)').text().trim();
        if (existingProduct === product) {
            isDuplicate = true;
            return false;
        }
    });

    if (isDuplicate) {
        $('.table-error').text('This product is already added to the table');
        return;
    }


    var post_url = "table.php?selected_product=" + encodeURIComponent(product) +
        "&selected_unit=" + encodeURIComponent(unit) +
        "&selected_qty=" + encodeURIComponent(quantity) +
        "&selected_rate=" + encodeURIComponent(rate) +
        "&selected_amount=" + encodeURIComponent(amount);

    $.ajax({
        url: post_url, type: 'GET', cache: false,
        success: function (result) {
            if ($('#purchaseTableBody').length > 0) {
                $('#purchaseTableBody').append(result);
                renumberRows();
                calculateTotal();
                clearInputs();
                $('.table-error').text('');
            }
        },
        error: function (xhr, status, error) {
            console.error(status, error);
            alert("Error adding item. Please try again.");
        }
    });
});

$(document).on('click', '.deleteItem', function () {
    $(this).closest('tr').remove();
    renumberRows();
    calculateTotal();
});

function renumberRows() {
    $('#purchaseTableBody tr').each(function (index) {
        $(this).find('.row-id').text(index + 1);
    });
}

function calculateTotal() {
    let total = 0;
    $('#purchaseTableBody tr').each(function () {
        let amt = $(this).find('.row-amount').val();
        total += parseFloat(amt) || 0;
    });
    $('.grand_total').val(total.toFixed(2));
}

function clearInputs() {
    $('#Product').val('');
    $('#Unit').val('');
    $('#Quantity').val('');
    $('#rateHint').val('');
    $('#Amount').val('');
}

$(document).on('input', '.edit-qty, .edit-rate', function () {
    let row = $(this).closest('tr');

    row.find('.error-message').remove();

    let qty = parseFloat(row.find('.edit-qty').val()) || 0;
    let rate = parseFloat(row.find('.edit-rate').val()) || 0;
    let amount = (qty * rate).toFixed(2);

    row.find('.row-amount').val(amount);
    row.find('input[name="quantityArray[]"]').val(qty);
    row.find('input[name="rateArray[]"]').val(rate);
    row.find('input[name="amountArray[]"]').val(amount);

    calculateTotal();
});