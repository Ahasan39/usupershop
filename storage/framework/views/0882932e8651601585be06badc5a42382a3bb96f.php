<?php
    use Illuminate\Support\Str;

    $routeName = Route::currentRouteName();

    // Helper function to check if current route starts with a prefix of route names
    function isMenuOpen(array $routes, $currentRoute)
    {
        foreach ($routes as $r) {
            if (Str::startsWith($currentRoute, $r)) {
                return true;
            }
        }
        return false;
    }
?>

<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        
        <?php if(Auth::user()->role === 'admin'): ?>
            <li class="nav-item <?php echo e(isMenuOpen(['users.'], $routeName) ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(isMenuOpen(['users.'], $routeName) ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Manage Users
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('users.add')); ?>"
                            class="nav-link <?php echo e($routeName === 'users.add' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Add User</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('users.view')); ?>"
                            class="nav-link <?php echo e($routeName === 'users.view' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>User List</p>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        
        <?php if(auth()->user()->payment_status == 1): ?>

            
            <li class="nav-item <?php echo e(isMenuOpen(['sellers.refer.'], $routeName) ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(isMenuOpen(['sellers.refer.'], $routeName) ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-copy"></i>
                    <p>
                        Refer
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('sellers.refer.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'sellers.refer.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Refer List</p>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item <?php echo e(isMenuOpen(['sellers.'], $routeName) ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(isMenuOpen(['sellers.'], $routeName) ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-user"></i>
                    <p>
                        Manage Profile
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('sellers.view.profile')); ?>"
                            class="nav-link <?php echo e($routeName === 'sellers.view.profile' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Your Profile</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('sellers.password.view')); ?>"
                            class="nav-link <?php echo e($routeName === 'sellers.password.view' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Change Password</p>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li
                class="nav-item <?php echo e(isMenuOpen(['sellers.', 'vendor.', 'dropshipper.product.'], $routeName) ? 'menu-open' : ''); ?>">
                <a href="#"
                    class="nav-link <?php echo e(isMenuOpen(['sellers.', 'vendor.', 'dropshipper.product.'], $routeName) ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-boxes"></i>
                    <p>
                        Manage Products
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <?php if(auth()->user()->usertype === 'seller'): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('sellers.shopkeeper_product')); ?>"
                                class="nav-link <?php echo e($routeName === 'sellers.shopkeeper_product' ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usupershop Product</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('sellers.seller_product')); ?>"
                                class="nav-link <?php echo e($routeName === 'sellers.seller_product' ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>My Products</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(auth()->user()->usertype === 'vendor'): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('vendor.productview')); ?>"
                                class="nav-link <?php echo e($routeName === 'vendor.productview' ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Lists</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('vendor.addproduct')); ?>"
                                class="nav-link <?php echo e($routeName === 'vendor.addproduct' ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Product</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(auth()->user()->usertype === 'dropshipper'): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('dropshipper.product.list')); ?>"
                                class="nav-link <?php echo e($routeName === 'dropshipper.product.list' ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Lists</p>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>

            
            <li class="nav-item <?php echo e(isMenuOpen(['seller.orders.'], $routeName) ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(isMenuOpen(['seller.orders.'], $routeName) ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>
                        Manage Orders
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.orders.pending.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.orders.pending.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Pending Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.orders.confirmed.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.orders.confirmed.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Confirmed Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.orders.packaging.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.orders.packaging.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>packaging Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.orders.shipment.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.orders.shipment.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>shipment Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.orders.cancel.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.orders.cancel.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Cancel Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.orders.return.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.orders.return.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Return Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.orders.delivered.list')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.orders.delivered.list' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Delivered Orders</p>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item <?php echo e(isMenuOpen(['seller.reports.'], $routeName) ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(isMenuOpen(['seller.reports.'], $routeName) ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>
                        Reports
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <?php if(auth()->user()->usertype === 'vendor'): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('seller.reports.sales')); ?>"
                                class="nav-link <?php echo e($routeName === 'seller.reports.sales' ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Sales</p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(auth()->user()->usertype === 'seller'): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('seller.reports.reseller_commission_reports')); ?>"
                                class="nav-link <?php echo e($routeName === 'seller.reports.reseller_commission_reports' ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Reseller Commission</p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('seller.reports.refer')); ?>"
                            class="nav-link <?php echo e($routeName === 'seller.reports.refer' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Refer Commission</p>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item <?php echo e(isMenuOpen(['manage.wallets'], $routeName) ? 'menu-open' : ''); ?>">
                <a href="#" class="nav-link <?php echo e(isMenuOpen(['manage.wallets'], $routeName) ? 'active' : ''); ?>">
                    <i class="nav-icon fas fa-wallet"></i>
                    <p>
                        Manage Wallets
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('manage.wallets.payment')); ?>"
                            class="nav-link <?php echo e($routeName === 'manage.wallets.payment' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Payment</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('manage.wallets')); ?>"
                            class="nav-link <?php echo e($routeName === 'manage.wallets' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Wallet</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('manage.wallets.verified')); ?>"
                            class="nav-link <?php echo e($routeName === 'manage.wallets.verified' ? 'active' : ''); ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Verified</p>
                        </a>
                    </li>
                </ul>
            </li>

        <?php endif; ?>

        
        <li class="nav-item">
            <a href="<?php echo e(route('logout')); ?>"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
                <i class="nav-icon fas fa-lock"></i>
                <p>Logout</p>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
            </a>
        </li>
    </ul>
</nav>
<?php /**PATH D:\webleez works\u super project\usupershop_v2\resources\views/backend/seller/seller-sidebar.blade.php ENDPATH**/ ?>