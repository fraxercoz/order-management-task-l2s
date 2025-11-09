<script setup>
import { ref, onMounted } from 'vue';
import OrderDetailsCard from './OrderDetailsCard.vue';
import { fetchOrders, updateOrderStatus } from '../api/orders';

const orders = ref([]);
const loading = ref(false);
const error = ref(null);
const selectedOrder = ref(null);
const statusUpdating = ref(false);
const statusError = ref(null);

const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(10);

const loadOrders = async (page = 1) => {
  loading.value = true;
  error.value = null;

  try {
    const { orders: data, meta } = await fetchOrders(page);

    orders.value = data;

    if (meta) {
      currentPage.value = meta.current_page ?? 1;
      lastPage.value = meta.last_page ?? 1;
      total.value = meta.total ?? data.length;
      perPage.value = meta.per_page ?? data.length;
    } else {
      // Fallback if no meta (shouldn't really happen now)
      currentPage.value = 1;
      lastPage.value = 1;
      total.value = data.length;
      perPage.value = data.length;
    }
  } catch (err) {
    console.error(err);
    error.value = 'Failed to load orders.';
  } finally {
    loading.value = false;
  }
};


const selectOrder = (order) => {
  selectedOrder.value = order;
  statusError.value = null;
};

const updateStatus = async (orderId, newStatus) => {
  if (!selectedOrder.value) return;

  statusUpdating.value = true;
  statusError.value = null;

  try {
    const updatedOrder = await updateOrderStatus(orderId, newStatus);

    selectedOrder.value = updatedOrder;

    const idx = orders.value.findIndex((o) => o.id === updatedOrder.id);
    if (idx !== -1) {
      orders.value[idx] = updatedOrder;
    }
  } catch (err) {
    console.error(err);
    statusError.value = 'Failed to update status.';
  } finally {
    statusUpdating.value = false;
  }
};

const goToPage = async (page) => {
  if (page < 1 || page > lastPage.value || page === currentPage.value) {
    return;
  }
  await loadOrders(page);
};

//onMounted(loadOrders);
onMounted(() => loadOrders());
</script>

<template>
  <div class="min-h-screen bg-slate-100 flex justify-center py-10">
    <div class="w-full max-w-5xl bg-white shadow-md rounded-xl p-6">
      <h2 class="text-3xl font-bold text-slate-800 mb-6">
        Orders
      </h2>

      <div v-if="loading" class="mb-4 text-slate-600">
        Loading orders...
      </div>

      <div v-if="error" class="mb-4 text-red-600">
        {{ error }}
      </div>

      <!-- Orders table -->
      <div v-if="orders.length" class="overflow-x-auto">
        <table class="min-w-full text-left text-sm text-slate-700">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
              <th class="px-4 py-2 font-semibold">Order #</th>
              <th class="px-4 py-2 font-semibold">Customer</th>
              <th class="px-4 py-2 font-semibold">Status</th>
              <th class="px-4 py-2 font-semibold text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="order in orders"
              :key="order.id"
              @click="selectOrder(order)"
              class="border-b border-slate-100 hover:bg-slate-50 cursor-pointer transition"
            >
              <td class="px-4 py-2">{{ order.order_number }}</td>
              <td class="px-4 py-2">
                {{ order.customer?.name || '—' }}
              </td>
              <td class="px-4 py-2 capitalize">
                <span
                  class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                  :class="{
                    'bg-yellow-100 text-yellow-800': order.status === 'pending',
                    'bg-green-100 text-green-800': order.status === 'paid',
                    'bg-blue-100 text-blue-800': order.status === 'shipped',
                    'bg-red-100 text-red-800': order.status === 'cancelled',
                  }"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-4 py-2 text-right font-semibold">
                £{{ Number(order.total).toFixed(2) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination controls -->
      <div
        v-if="lastPage > 1"
        class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-sm text-slate-600"
      >
        <div>
          Page {{ currentPage }} of {{ lastPage }}
          <span v-if="total">
            — {{ total }} orders
          </span>
        </div>

        <div class="flex gap-2">
          <button
            type="button"
            class="px-3 py-1 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="currentPage === 1 || loading"
            @click="goToPage(currentPage - 1)"
          >
            Previous
          </button>

          <button
            type="button"
            class="px-3 py-1 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="currentPage === lastPage || loading"
            @click="goToPage(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>

      <div v-else-if="!loading && !error" class="text-slate-600">
        No orders found.
      </div>

      <!-- Selected order details as a separate card component -->
      <OrderDetailsCard
        v-if="selectedOrder"
        :order="selectedOrder"
        :status-updating="statusUpdating"
        :status-error="statusError"
        @update-status="(status) => updateStatus(selectedOrder.id, status)"
        @close="selectedOrder = null"
        />
    </div>
  </div>
</template>
