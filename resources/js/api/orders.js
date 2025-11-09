// resources/js/api/orders.js

async function requestJson(url, options = {}) {
  const response = await fetch(url, {
    headers: {
      'Content-Type': 'application/json',
      ...(options.headers || {}),
    },
    ...options,
  });

  const text = await response.text();

  if (!response.ok) {
    console.error('API error:', response.status, text);
    throw new Error(`HTTP error! status: ${response.status}`);
  }

  if (!text) {
    return null;
  }

  try {
    return JSON.parse(text);
  } catch {
    return text;
  }
}

/**
 * Fetch orders with pagination.
 * Returns { orders, meta, links }.
 */
export async function fetchOrders(page = 1) {
  const json = await requestJson(`/api/orders?page=${page}`);

  return {
    orders: Array.isArray(json?.data) ? json.data : [],
    meta: json?.meta ?? null,
    links: json?.links ?? null,
  };
}

/**
 * Update a single order's status.
 * Returns the updated order object.
 */
export async function updateOrderStatus(id, status) {
  const json = await requestJson(`/api/orders/${id}/status`, {
    method: 'POST',
    body: JSON.stringify({ status }),
  });

  return json?.data ?? json;
}
