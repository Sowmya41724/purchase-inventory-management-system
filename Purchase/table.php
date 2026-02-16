<?php

if (isset($_REQUEST['selected_product'])) {
    $selected_product = $_REQUEST['selected_product'] ?? '';
    $selected_unit = $_REQUEST['selected_unit'] ?? '';
    $selected_qty = $_REQUEST['selected_qty'] ?? '';
    $selected_rate = $_REQUEST['selected_rate'] ?? '';
    $selected_amount = $_REQUEST['selected_amount'] ?? '';
    ?>
    <tr>
        <td class="row-id">
        </td>
        <td>
            <?php if (!empty($selected_product)) {
                echo $selected_product;
            } ?>
            <input type="hidden" name="productArray[]" value="<?php if (!empty($selected_product)) {
                echo $selected_product;
            } ?>">
        </td>
        <td>
            <?php if (!empty($selected_unit)) {
                echo $selected_unit;
            } ?>
            <input type="hidden" name="unitArray[]" value="<?php if (!empty($selected_unit)) {
                echo $selected_unit;
            } ?>">
        </td>
        <td>
            <input type="number" name="qty" class="edit-qty" value="<?php echo $selected_qty; ?>" step="any">
            <input type="hidden" name="quantityArray[]" value="<?php if (!empty($selected_qty)) {
                echo $selected_qty;
            } ?>">
        </td>
        <td>
            <input type="number" name="rate" class="edit-rate" value="<?php echo $selected_rate; ?>" step="any">
            <input type="hidden" name="rateArray[]" value="<?php if (!empty($selected_rate)) {
                echo $selected_rate;
            } ?>">
        </td>
        <td>
            <input type="number" class="row-amount" name="amountArray[]" value="<?php if (!empty($selected_amount)) {
                echo $selected_amount;
            } ?>" readonly>
        </td>
        <td id="no-hover">
            <button type="button" style="width: 80px;" class="deleteItem">&#x1F5D1; Delete</button>
        </td>
    </tr>
    <?php
}

?>