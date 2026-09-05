<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $store_id
 * @property int $shift_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $shift_type
 * @property string $check_in
 * @property bool $is_on_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Shift $shift
 * @property-read \App\Models\Store $store
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereIsOnTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereShiftType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUserId($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $allChildren
 * @property-read int|null $all_children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $store_id
 * @property int $shift_id
 * @property int $user_id
 * @property string $description
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Shift $shift
 * @property-read \App\Models\Store $store
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense forStore($storeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereUserId($value)
 */
	class Expense extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string|null $code
 * @property string|null $image
 * @property string $type
 * @property numeric $cost_price
 * @property numeric $selling_price
 * @property int $stock
 * @property int $min_stock
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StoreProductStock> $storeStocks
 * @property-read int|null $store_stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionDetail> $transactionDetails
 * @property-read int|null $transaction_details_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMinStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $store_id
 * @property int $transaction_id
 * @property int $user_id
 * @property int $shift_id
 * @property string $return_code
 * @property numeric $returned_total
 * @property numeric $replacement_total
 * @property numeric $price_difference
 * @property string $payment_method
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReturnDetail> $details
 * @property-read int|null $details_count
 * @property-read \App\Models\Shift $shift
 * @property-read \App\Models\Store $store
 * @property-read \App\Models\Transaction $transaction
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn forStore($storeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn wherePriceDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereReplacementTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereReturnCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereReturnedTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReturn whereUserId($value)
 */
	class ProductReturn extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $return_id
 * @property int $product_id
 * @property string $type
 * @property int $qty
 * @property numeric $price
 * @property numeric $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductReturn $productReturn
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereReturnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturnDetail whereUpdatedAt($value)
 */
	class ReturnDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 * @property array<array-key, mixed>|null $user_ids
 * @property string|null $photo
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property numeric $cash_initial
 * @property numeric $cash_expected
 * @property numeric|null $cash_actual
 * @property numeric|null $difference
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 * @property-read int|null $expenses_count
 * @property-read string|null $photo_url
 * @property-read mixed $staff_names
 * @property-read float $total_expenses
 * @property-read float $total_profit
 * @property-read float $total_sales
 * @property-read \App\Models\Store $store
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift forStore($storeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCashActual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCashExpected($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCashInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereUserIds($value)
 */
	class Shift extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $address
 * @property string|null $phone
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 * @property-read int|null $expenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StoreProductStock> $productStocks
 * @property-read int|null $product_stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shift> $shifts
 * @property-read int|null $shifts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereUpdatedAt($value)
 */
	class Store extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $store_id
 * @property int $product_id
 * @property int $stock
 * @property int $min_stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock whereMinStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreProductStock whereUpdatedAt($value)
 */
	class StoreProductStock extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $store_id
 * @property string $invoice_code
 * @property int $user_id
 * @property int $shift_id
 * @property numeric $total_cost
 * @property numeric $total_price
 * @property numeric $total_profit
 * @property numeric $pay_amount
 * @property numeric $change_amount
 * @property string $payment_method
 * @property string|null $payment_proof
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionDetail> $details
 * @property-read int|null $details_count
 * @property-read \App\Models\Shift $shift
 * @property-read \App\Models\Store $store
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction forStore($storeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereInvoiceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePayAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTotalProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUserId($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $transaction_id
 * @property int $product_id
 * @property int|null $served_by_user_id
 * @property string|null $target_phone
 * @property string|null $digital_provider
 * @property int $qty
 * @property numeric $cost_price
 * @property numeric $selling_price
 * @property numeric $subtotal
 * @property numeric $profit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User|null $servedBy
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereDigitalProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereServedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereTargetPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionDetail whereUpdatedAt($value)
 */
	class TransactionDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $store_id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 * @property-read int|null $expenses_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shift> $shifts
 * @property-read int|null $shifts_count
 * @property-read \App\Models\Store|null $store
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

