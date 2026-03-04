<?php 
// 引入数据库配置，确保你的路径正确
include '../config/db_config.php'; 
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bubble Tea Pro - Order Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .spec-active { border-color: #f97316; color: #f97316; background-color: #fff7ed; }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-50 pb-20">
    <nav class="sticky top-0 bg-white shadow-sm z-10 p-4 flex space-x-6 overflow-x-auto">
        <?php
        $cat_query = "SELECT * FROM categories";
        $categories = $conn->query($cat_query);
        $selected_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 1;

        while($cat = $categories->fetch_assoc()):
            $active_class = ($selected_cat == $cat['category_id']) ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-600';
        ?>
            <a href="?cat=<?php echo $cat['category_id']; ?>" class="whitespace-nowrap font-bold pb-1 <?php echo $active_class; ?>">
                <?php echo $cat['category_name']; ?>
            </a>
        <?php endwhile; ?>
    </nav>

    <main class="p-4 space-y-4">
        <?php
        $prod_query = "SELECT * FROM products WHERE category_id = $selected_cat";
        $products = $conn->query($prod_query);
        
        if ($products && $products->num_rows > 0):
            while($p = $products->fetch_assoc()):
        ?>
            <div class="flex bg-white rounded-xl shadow-sm p-3 space-x-4">
                <img src="<?php echo $p['image_url']; ?>" class="w-24 h-24 rounded-lg object-cover bg-gray-100" onerror="this.src='https://via.placeholder.com/150?text=Tea'">
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-lg"><?php echo $p['name']; ?></h3>
                        <p class="text-gray-400 text-xs mt-1">Best seller in London, ON</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-orange-500 font-bold">$<?php echo number_format($p['price'], 2); ?></span>
                        <button onclick="openModal('<?php echo $p['name']; ?>', <?php echo $p['price']; ?>)" 
                                class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-1.5 rounded-full text-sm font-bold transition-colors">
                            选规格
                        </button>
                    </div>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
            echo "<div class='py-20 text-center text-gray-400'>暂无此分类商品</div>";
        endif;
        ?>
    </main>

    <div id="specModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-end justify-center transition-opacity">
        <div class="bg-white w-full max-w-md rounded-t-2xl p-6 space-y-6 animate-slide-up">
            <div class="flex justify-between items-center">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-800"></h2>
                <button onclick="closeModal()" class="text-gray-400 text-2xl">&times;</button>
            </div>
            
            <div class="space-y-3">
                <p class="text-sm font-semibold text-gray-500">容量规格</p>
                <div class="flex gap-3" id="sizeOptions">
                    <button class="spec-btn border-2 p-2 rounded-xl flex-1 transition-all" onclick="selectSpec('size', 'Regular', this)">标准</button>
                    <button class="spec-btn border-2 p-2 rounded-xl flex-1 transition-all" onclick="selectSpec('size', 'Large', this)">大杯</button>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-semibold text-gray-500">糖度选择</p>
                <div class="flex gap-3 flex-wrap" id="sugarOptions">
                    <button class="spec-btn border-2 px-4 py-2 rounded-xl transition-all" onclick="selectSpec('sugar', 'Full', this)">全糖</button>
                    <button class="spec-btn border-2 px-4 py-2 rounded-xl transition-all" onclick="selectSpec('sugar', 'Half', this)">半糖</button>
                    <button class="spec-btn border-2 px-4 py-2 rounded-xl transition-all" onclick="selectSpec('sugar', 'None', this)">无糖</button>
                </div>
            </div>

            <div class="flex justify-between items-center py-2 border-t pt-6">
                <span class="font-bold text-gray-700">购买数量</span>
                <div class="flex items-center space-x-4">
                    <button onclick="updateQty(-1)" class="w-8 h-8 rounded-full border flex items-center justify-center text-xl font-bold">-</button>
                    <span id="qtyDisplay" class="font-bold text-lg w-4 text-center">1</span>
                    <button onclick="updateQty(1)" class="w-8 h-8 rounded-full border bg-gray-100 flex items-center justify-center text-xl font-bold">+</button>
                </div>
            </div>

            <div class="flex space-x-4">
                <div class="flex-1">
                    <p class="text-xs text-gray-400">预计总计</p>
                    <p class="text-orange-500 text-2xl font-bold">$<span id="totalPrice">0.00</span></p>
                </div>
                <button onclick="addToCart()" class="flex-[2] bg-orange-500 text-white py-4 rounded-xl font-bold shadow-lg shadow-orange-200">
                    加入购物车
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentItem = { name: '', basePrice: 0, size: 'Regular', sugar: 'Normal', qty: 1 };

        function openModal(name, price) {
            currentItem = { name, basePrice: price, size: 'Regular', sugar: 'Normal', qty: 1 };
            document.getElementById('modalTitle').innerText = name;
            document.getElementById('qtyDisplay').innerText = "1";
            calculateTotal();
            
            // 显示 Modal
            document.getElementById('specModal').classList.remove('hidden');
            
            // 默认选中第一个按钮
            document.querySelector('#sizeOptions button').click();
            document.querySelector('#sugarOptions button').click();
        }

        function selectSpec(type, value, el) {
            currentItem[type] = value;
            
            // 切换选中样式
            const container = el.parentElement;
            container.querySelectorAll('button').forEach(btn => btn.classList.remove('spec-active'));
            el.classList.add('spec-active');
            
            // 大杯加 $1.00 (典型的 BA 定价逻辑)
            calculateTotal();
        }

        function updateQty(delta) {
            currentItem.qty = Math.max(1, currentItem.qty + delta);
            document.getElementById('qtyDisplay').innerText = currentItem.qty;
            calculateTotal();
        }

        function calculateTotal() {
            let total = currentItem.basePrice;
            if (currentItem.size === 'Large') total += 1.0; // 大杯加价逻辑
            total = total * currentItem.qty;
            document.getElementById('totalPrice').innerText = total.toFixed(2);
        }

        function addToCart() {
            // 模拟加入购物车
            console.log("Cart Update:", currentItem);
            alert(`✅ 加入购物车成功！\n品类：${currentItem.name}\n规格：${currentItem.size} / ${currentItem.sugar}\n数量：${currentItem.qty}`);
            closeModal();
            // 这里可以跳转到结算页或刷新状态
        }

        function closeModal() {
            document.getElementById('specModal').classList.add('hidden');
        }

        // 点击背景关闭 Modal
        window.onclick = function(event) {
            const modal = document.getElementById('specModal');
            if (event.target == modal) closeModal();
        }
    </script>
</body>
</html>