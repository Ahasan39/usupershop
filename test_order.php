<?php

// Test Order Creation Script
// Run with: php test_order.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Shipping;
use Illuminate\Support\Facades\DB;

echo "=== Order Creation Test ===\n\n";

// Check database connection
try {
    DB::connection()->getPdo();
    echo "✓ Database connected successfully\n";
} catch (\Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check latest order
try {
    $latestOrder = Order::latest()->first();
    if ($latestOrder) {
        echo "✓ Latest order ID: " . $latestOrder->id . "\n";
        echo "  Order No: " . $latestOrder->order_no . "\n";
        echo "  Status: " . $latestOrder->status . "\n";
        echo "  Created: " . $latestOrder->created_at . "\n";
    } else {
        echo "! No orders found in database\n";
    }
} catch (\Exception $e) {
    echo "✗ Error fetching orders: " . $e->getMessage() . "\n";
}

// Check order_details table structure
try {
    $columns = DB::select("DESCRIBE order_details");
    echo "\n✓ order_details table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
} catch (\Exception $e) {
    echo "\n✗ Error checking order_details structure: " . $e->getMessage() . "\n";
}

// Check if required columns exist
$requiredColumns = ['color_name', 'size_name', 'buy_price', 'sell_price'];
echo "\n✓ Checking required columns:\n";
foreach ($requiredColumns as $col) {
    $exists = DB::select("SHOW COLUMNS FROM order_details LIKE '$col'");
    if (count($exists) > 0) {
        echo "  ✓ $col exists\n";
    } else {
        echo "  ✗ $col MISSING!\n";
    }
}

// Check latest shipping
try {
    $latestShipping = Shipping::latest()->first();
    if ($latestShipping) {
        echo "\n✓ Latest shipping ID: " . $latestShipping->id . "\n";
        echo "  Name: " . $latestShipping->name . "\n";
        echo "  Mobile: " . $latestShipping->mobile . "\n";
    } else {
        echo "\n! No shipping records found\n";
    }
} catch (\Exception $e) {
    echo "\n✗ Error fetching shipping: " . $e->getMessage() . "\n";
}

// Check latest payment
try {
    $latestPayment = Payment::latest()->first();
    if ($latestPayment) {
        echo "\n✓ Latest payment ID: " . $latestPayment->id . "\n";
        echo "  Method: " . $latestPayment->payment_method . "\n";
    } else {
        echo "\n! No payment records found\n";
    }
} catch (\Exception $e) {
    echo "\n✗ Error fetching payment: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
