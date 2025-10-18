<x-main-layout>
    <div class="p-10 text-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4">Order Accepted!</h1>
        <p class="text-lg mb-2">
            Thank you, <span class="font-semibold">{{ $purchase->supplier->name }}</span>.
        </p>
        <p class="text-lg mb-4">
            Your confirmation has been received — our purchase order has been <span
                class="font-semibold text-green-600">accepted</span>.
        </p>
        <p class="text-gray-700 mb-6">
            We appreciate your cooperation. Our team will now proceed with the next steps of payment and delivery
            coordination.
            You’ll receive further updates once processing begins.
        </p>
    </div>
</x-main-layout>
