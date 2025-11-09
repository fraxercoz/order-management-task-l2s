<script setup>
const props = defineProps({
  order: {
    type: Object,
    required: true,
  },
  statusUpdating: {
    type: Boolean,
    default: false,
  },
  statusError: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(['update-status', 'close']);

const handleUpdate = (status) => {
  emit('update-status', status);
};

const handleClose = () => {
  emit('close');
};
</script>

<template>
  <!-- Backdrop -->
  <div
    class="fixed inset-0 z-40 flex items-center justify-center bg-black/40"
    @click.self="handleClose"
  >
    <!-- Modal card -->
    <div
      class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 max-h-[80vh] overflow-y-auto p-6 relative"
    >
      <!-- Close button -->
      <button
        class="absolute top-3 right-3 text-slate-400 hover:text-slate-600"
        type="button"
        @click="handleClose"
      >
        ✕
      </button>

      <h3 class="text-2xl font-semibold text-slate-800 mb-2">
        Order details: {{ order.order_number }}
      </h3>

      <div class="grid gap-4 md:grid-cols-3 text-sm text-slate-700 mb-4">
        <div>
          <p class="font-semibold text-slate-500">Customer</p>
          <p>{{ order.customer?.name }}</p>
        </div>
        <div>
          <p class="font-semibold text-slate-500">Status</p>
          <p class="capitalize">{{ order.status }}</p>
        </div>
        <div>
          <p class="font-semibold text-slate-500">Total</p>
          <p class="font-semibold">
            £{{ Number(order.total).toFixed(2) }}
          </p>
        </div>
      </div>

      <div v-if="statusError" class="mb-2 text-sm text-red-600">
        {{ statusError }}
      </div>

      <div class="mb-4 flex flex-wrap items-center gap-2">
        <span class="text-sm text-slate-600 mr-2">Change status:</span>

        <button
          class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 disabled:opacity-50"
          :disabled="statusUpdating || order.status === 'pending'"
          @click="handleUpdate('pending')"
        >
          Pending
        </button>

        <button
          class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 disabled:opacity-50"
          :disabled="statusUpdating || order.status === 'paid'"
          @click="handleUpdate('paid')"
        >
          Paid
        </button>

        <button
          class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 disabled:opacity-50"
          :disabled="statusUpdating || order.status === 'shipped'"
          @click="handleUpdate('shipped')"
        >
          Shipped
        </button>

        <button
          class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 disabled:opacity-50"
          :disabled="statusUpdating || order.status === 'cancelled'"
          @click="handleUpdate('cancelled')"
        >
          Cancelled
        </button>

        <span v-if="statusUpdating" class="text-xs text-slate-500 ml-2">
          Updating...
        </span>
      </div>

      <h4 class="mt-4 mb-2 font-semibold text-slate-700">
        Items
      </h4>
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm text-slate-700">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
              <th class="px-4 py-2 font-semibold">Product</th>
              <th class="px-4 py-2 font-semibold">Qty</th>
              <th class="px-4 py-2 font-semibold">Unit price</th>
              <th class="px-4 py-2 font-semibold">Line total</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in order.items"
              :key="item.id"
              class="border-b border-slate-100"
            >
              <td class="px-4 py-2">{{ item.product?.name }}</td>
              <td class="px-4 py-2">{{ item.quantity }}</td>
              <td class="px-4 py-2">
                £{{ Number(item.unit_price).toFixed(2) }}
              </td>
              <td class="px-4 py-2 font-semibold">
                £{{ Number(item.line_total).toFixed(2) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
