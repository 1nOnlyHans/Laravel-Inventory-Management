<x-main-layout>
    <div class="p-10 text-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4">Order Accepted!</h1>
        <p class="text-lg">Thank you for approving our purchase order, {{ $purchase->supplier->name }}.</p>
    </div>
</x-main-layout>
