<?php include '../config/db_config.php'; ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bubble Tea - Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .spec-active { border-color: #f97316 !important; color: #f97316 !important; background-color: #fff7ed !important; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .animate-modal { animation: slideUp 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-50 pb-28">
    <nav class="sticky top-0 bg-white shadow-sm z-10 p-4 flex space-x-6 overflow-x-auto">
        <?php
        $categories = $conn->query("SELECT * FROM categories");
        $selected_cat = $_GET['cat'] ?? 1;
        while($cat = $categories->fetch_assoc()):
            $active = ($selected_cat == $cat['category_id']) ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-600';
        ?>
            <a href="?cat=<?php echo $cat['category_id']; ?>" class="font-bold whitespace-nowrap <?php echo $active; ?>"><?php echo $cat['category_name']; ?></a>
        <?php endwhile; ?>
    </nav>

    <main class="p-4 space-y-4">
        <?php
        $products = $conn->query("SELECT * FROM products WHERE category_id = $selected_cat");
        while($p = $products->fetch_assoc()):
        ?>
            <div class="flex bg-white rounded-xl p-3 shadow-sm space-x-4">
                <img src="<?php echo $p['image_url']; ?>" class="w-24 h-24 rounded-lg object-cover bg-gray-100" onerror="this.src='https://via.placeholder.com/150?text=Tea'">
                <div class="flex-1 flex flex-col justify-between">
                    <h3 class="font-bold text-lg"><?php echo $p['name']; ?></h3>
                    <div class="flex justify-between items-center">
                        <span class="text-orange-500 font-bold">$<?php echo number_format($p['price'], 2); ?></span>
                        <button onclick="openModal('<?php echo $p['name']; ?>', <?php echo $p['price']; ?>)" class="bg-orange-500 text-white px-5 py-1.5 rounded-full text-sm font-bold">选规格</button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </main>

    <div id="specModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 space-y-6 animate-modal">
            <h2 id="modalTitle" class="text-xl font-bold">加载中...</h2>
            <div class="space-y-3">
                <p class="text-xs font-semibold text-gray-400 uppercase">规格</p>
                <div id="sizeOptions" class="flex gap-3">
                    <button class="spec-btn border-2 p-3 rounded-xl flex-1" onclick="selectSpec('size', 'Regular', this)">标准杯</button>
                    <button class="spec-btn border-2 p-3 rounded-xl flex-1" onclick="selectSpec('size', 'Large', this)">大杯 (+$1)</button>
                </div>
            </div>
            <div class="space-y-3">
                <p class="text-xs font-semibold text-gray-400 uppercase">糖度</p>
                <div id="sugarOptions" class="flex gap-2">
                    <button class="spec-btn border-2 px-4 py-2 rounded-xl" onclick="selectSpec('sugar', 'Full', this)">全糖</button>
                    <button class="spec-btn border-2 px-4 py-2 rounded-xl" onclick="selectSpec('sugar', 'Half', this)">半糖</button>
                </div>
            </div>
            <div class="flex justify-between items-center pt-4 border-t">
                <span class="text-orange-500 text-2xl font-bold">$<span id="totalPrice">0.00</span></span>
                <button onclick="addToCart()" class="bg-orange-500 text-white px-8 py-3 rounded-xl font-bold">加入购物车</button>
            </div>
            <button onclick="closeModal()" class="w-full text-gray-400 text-sm">再想想</button>
        </div>
    </div>

    <div id="cartNavBar" onclick="window.location.href='checkout.php'" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 w-11/12 bg-gray-900 text-white p-4 rounded-2xl flex justify-between items-center shadow-2xl cursor-pointer z-50">
        <div class="flex items-center space-x-3">
            <span id="cartBadge" class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">0</span>
            <span class="font-bold">去结算</span>
        </div>
        <span id="cartNavTotal" class="font-bold text-orange-400">$0.00</span>
    </div>

    <script src="/js/script.js"></script>
</body>
</html>