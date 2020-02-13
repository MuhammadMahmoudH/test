<?php

// Order Data
$order = new stdClass();
$order->total_cart = 175;
$order->shipping_cost = 50;
$order->customer_id = 101;
$order->cart_content = [
    ['product_id' => 1, 'product_name' => 'Book', 'product_price' => 5, 'category_id' => 10],
    ['product_id' => 2, 'product_name' => 'Pen', 'product_price' => 1, 'category_id' => 20],
    ['product_id' => 3, 'product_name' => 'Bag', 'product_price' => 120, 'category_id' => 30],
    ['product_id' => 4, 'product_name' => 'Notebook', 'product_price' => 35, 'category_id' => 40],
    ['product_id' => 5, 'product_name' => 'Pencil Case', 'product_price' => 14, 'category_id' => 50]
];


// Coupon Data
$coupon = new stdClass();
$coupon->type = 'percentage';         // fixed, percentage
$coupon->amount = 20;                   // Based on type
$coupon->end_date = strtotime("+1 day");
$coupon->minimum_amount = 100;
$coupon->free_shipping = false;                // true, false
$coupon->included_categories = [10, 20];
$coupon->excluded_categories = [50];
$coupon->included_products = [3];
$coupon->excluded_products = [4, 1];

try {
    $orderProcess = (array)$order;
    $couponProcess = (array)$coupon;
    echo calculate_coupon_discount($orderProcess, $couponProcess);
} catch (Exception $e) {
}

//validate order was included or excluded
function validated_order_coupon($order, $content, $is_include = true)
{

    if ($is_include) {
        $countIncludeDiff = in_array($order,$content );
        return $countIncludeDiff == true ? 1 : 0;
    } else {
        $countExcludedDiff = in_array($order,$content );
        return $countExcludedDiff == null ? 1 : 0;
    }
}

function calculate_coupon_discount($order, $coupon)
{
    $discounted_amount = 0;

    // Your code
    // get all order basket list
    foreach ($order['cart_content'] as $ord) {
        $is_included_categories = validated_order_coupon($ord['category_id'], $coupon['included_categories']);
        $is_excluded_categories = validated_order_coupon($ord['category_id'], $coupon['excluded_categories'], false);
        $is_included_products = validated_order_coupon($ord['product_id'], $coupon['included_products']);
        $is_excluded_products = validated_order_coupon($ord['product_id'], $coupon['excluded_products'], false);

        //check order to add in total
        if (($is_included_categories && $is_excluded_categories || $is_included_products)) {
            if ($is_excluded_products){
                //get discount
                $discounted_amount += ((float)$ord['product_price'] * ((float)$coupon['amount'] / 100));
            }
        }
    }
// return discount amount
    return $discounted_amount;
}


