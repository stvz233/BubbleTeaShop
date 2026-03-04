<?php include '../config/db_config.php'; ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bubble Tea Pro - Order Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="sticky top-0 bg-white shadow-sm z-10 p-4 flex space-x-6 overflow-x-auto">
        <?php
        $cat_query = "SELECT * FROM categories";
        $categories = $conn->query($cat_query);
        while($cat = $categories->fetch_assoc()):
        ?>
            <a href="?cat=<?php echo $cat['category_id']; ?>" class="whitespace-nowrap font-bold text-gray-600">
                <?php echo $cat['category_name']; ?>
            </a>
        <?php endwhile; ?>
    </nav>

    <main class="p-4 space-y-4">
        <?php
        $selected_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 1;
        // dynamically fetch products based on selected category
        $prod_query = "SELECT * FROM products WHERE category_id = $selected_cat";
        $products = $conn->query($prod_query);
        
        if ($products->num_rows > 0):
            while($p = $products->fetch_assoc()):
        ?>
            <div class="flex bg-white rounded-xl shadow-sm p-3 space-x-4">
                <img src="<?php echo $p['image_url']; ?>" class="w-24 h-24 rounded-lg object-cover bg-gray-100">
                <div class="flex-1 flex flex-col justify-between">
                    <h3 class="font-bold text-lg"><?php echo $p['name']; ?></h3>
                    <div class="flex justify-between items-center">
                        <span class="text-orange-500 font-bold">$<?php echo $p['price']; ?></span>
                        <button onclick="openModal('<?php echo $p['name']; ?>', <?php echo $p['price']; ?>)" 
                                class="bg-orange-500 text-white px-4 py-1 rounded-full text-sm font-bold">选规格</button>
                    </div>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
            echo "<p class='text-center text-gray-400'>Oops... No items under this category.</p>";
        endif;
        ?>
    </main>
    <div id="specModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end justify-center">
    <div class="bg-white w-full rounded-t-2xl p-6 space-y-4 animate-slide-up">
        <h2 id="modalTitle" class="text-xl font-bold"></h2>
        
        <div>
            <p class="text-sm text-gray-500 mb-2">规格</p>
            <div class="flex gap-2" id="sizeOptions">
                <button class="border p-2 rounded-lg flex-1" onclick="selectSpec('size', 'Regular')">标准</button>
                <button class="border p-2 rounded-lg flex-1" onclick="selectSpec('size', 'Large')">大杯</button>
            </div>
        </div>

        <div>
            <p class="text-sm text-gray-500 mb-2">糖度</p>
            <div class="flex gap-2 flex-wrap" id="sugarOptions">
                <button class="border p-2 rounded-lg px-4" onclick="selectSpec('sugar', 'Normal')">全糖</button>
                <button class="border p-2 rounded-lg px-4" onclick="selectSpec('sugar', 'Half')">半糖</button>
            </div>
        </div>

        <button onclick="addToCart()" class="w-full bg-orange-500 text-white py-3 rounded-full font-bold">加入购物车</button>
        <button onclick="closeModal()" class="w-full text-gray-400 py-2 text-sm">取消</button>
        </div>
    </div>
</body>
</html>