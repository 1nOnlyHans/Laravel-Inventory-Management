<x-main-layout>
    <div class="p-10 text-center">
        <h1 class="text-3xl font-bold text-red-600 mb-4">Order Rejected</h1>
        <p class="text-lg mb-2">
            Hello <span class="font-semibold">{{ $purchase->supplier->name }}</span>,
        </p>
        <p class="text-lg mb-4">
            We appreciate your time in reviewing our purchase order. Unfortunately, the order has been <span
                class="font-semibold text-red-500">rejected</span>.
        </p>
        <p class="text-gray-700 mb-6">
            If you have any concerns or wish to discuss this decision further, please contact our purchasing department
            for clarification.
        </p>
    </div>
</x-main-layout>
