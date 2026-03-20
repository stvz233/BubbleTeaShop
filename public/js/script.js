let currentItem = { name: '', basePrice: 0, size: 'Regular', sugar: 'Full', qty: 1 };

// --- 首页逻辑 ---
function openModal(name, price) {
    currentItem = { name, basePrice: price, size: 'Regular', sugar: 'Full', qty: 1 };
    document.getElementById('modalTitle').innerText = name;
    document.getElementById('specModal').classList.remove('hidden');
    
    // 初始化默认高亮
    document.querySelectorAll('.spec-btn').forEach(btn => btn.classList.remove('spec-active'));
    calculateModalPrice();
}

function selectSpec(type, value, el) {
    currentItem[type] = value;
    const container = el.parentElement;
    container.querySelectorAll('button').forEach(btn => btn.classList.remove('spec-active'));
    el.classList.add('spec-active');
    calculateModalPrice();
}

function calculateModalPrice() {
    let price = currentItem.basePrice + (currentItem.size === 'Large' ? 1.0 : 0);
    document.getElementById('totalPrice').innerText = price.toFixed(2);
}

function addToCart() {
    let cart = JSON.parse(localStorage.getItem('bubble_cart') || '[]');
    // 检查是否有完全一样的（名字+规格），如果有则加数量
    let existing = cart.find(i => i.name === currentItem.name && i.size === currentItem.size && i.sugar === currentItem.sugar);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ ...currentItem });
    }
    localStorage.setItem('bubble_cart', JSON.stringify(cart));
    document.getElementById('specModal').classList.add('hidden');
    updateNav();
}

// --- 结算页逻辑 ---
function renderCheckout() {
    const cart = JSON.parse(localStorage.getItem('bubble_cart') || '[]');
    const list = document.getElementById('checkoutList');
    if (!list) return;

    let total = 0;
    list.innerHTML = cart.length === 0 ? '<p class="text-center py-10 text-gray-400">购物车空空如也</p>' : '';

    cart.forEach((item, index) => {
        let unitPrice = item.basePrice + (item.size === 'Large' ? 1.0 : 0);
        let itemSubtotal = unitPrice * item.qty;
        total += itemSubtotal;

        list.innerHTML += `
            <div class="flex justify-between items-center border-b border-gray-50 pb-4">
                <div class="flex-1">
                    <p class="font-bold text-gray-800">${item.name}</p>
                    <p class="text-xs text-gray-400">${item.size} / ${item.sugar}</p>
                    <p class="text-orange-500 font-bold mt-1">$${itemSubtotal.toFixed(2)}</p>
                </div>
                <div class="flex flex-col items-end space-y-2">
                    <button onclick="removeItem(${index})" class="text-gray-300 hover:text-red-500"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                    <div class="flex items-center border rounded-lg bg-gray-50">
                        <button onclick="changeQty(${index}, -1)" class="px-3 py-1 font-bold">-</button>
                        <span class="px-3 py-1 bg-white font-bold min-w-[30px] text-center text-sm">${item.qty}</span>
                        <button onclick="changeQty(${index}, 1)" class="px-3 py-1 font-bold">+</button>
                    </div>
                </div>
            </div>`;
    });
    document.getElementById('finalTotal').innerText = `$${total.toFixed(2)}`;
}

function changeQty(index, delta) {
    let cart = JSON.parse(localStorage.getItem('bubble_cart') || '[]');
    cart[index].qty += delta;
    if (cart[index].qty <= 0) {
        if(confirm("确定要移除这件商品吗？")) cart.splice(index, 1);
        else cart[index].qty = 1;
    }
    localStorage.setItem('bubble_cart', JSON.stringify(cart));
    renderCheckout();
}

function removeItem(index) {
    let cart = JSON.parse(localStorage.getItem('bubble_cart') || '[]');
    cart.splice(index, 1);
    localStorage.setItem('bubble_cart', JSON.stringify(cart));
    renderCheckout();
}

// --- 通用辅助 ---
function updateNav() {
    const cart = JSON.parse(localStorage.getItem('bubble_cart') || '[]');
    const bar = document.getElementById('cartNavBar');
    if (!bar) return;

    if (cart.length === 0) {
        bar.classList.add('hidden');
    } else {
        bar.classList.remove('hidden');
        let total = 0;
        cart.forEach(item => total += (item.basePrice + (item.size === 'Large' ? 1.0 : 0)) * item.qty);
        document.getElementById('cartBadge').innerText = cart.length;
        document.getElementById('cartNavTotal').innerText = `$${total.toFixed(2)}`;
    }
}

function closeModal() { document.getElementById('specModal').classList.add('hidden'); }

function submitOrder() {
    alert("正在前往支付...");
    localStorage.removeItem('bubble_cart');
    window.location.href = 'index.php';
}

window.addEventListener('DOMContentLoaded', () => {
    updateNav();
    renderCheckout();
});