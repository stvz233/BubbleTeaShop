<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Your Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-4">
    <div class="max-w-md mx-auto space-y-6">
        <div class="flex items-center space-x-4">
            <button onclick="window.location.href='index.php'" class="bg-white p-2 rounded-full shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <h1 class="text-2xl font-bold">确认订单</h1>
        </div>

        <div id="checkoutList" class="bg-white rounded-2xl shadow-sm p-4 space-y-4">
            </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
            <div class="flex justify-between font-bold text-xl">
                <span>总计</span>
                <span id="finalTotal" class="text-orange-600">$0.00</span>
            </div>
            <button onclick="submitOrder()" class="w-full bg-orange-500 text-white py-4 rounded-xl font-bold text-lg shadow-lg">支付订单</button>
        </div>
    </div>
    <script src="/js/script.js"></script>
</body>
</html>