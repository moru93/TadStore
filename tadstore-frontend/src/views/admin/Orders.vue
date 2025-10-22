<template>
  <div class="orders-admin">
    <h2 class="page-title">📦 Pedidos Realizados</h2>

    <!-- Gráfico de pedidos -->
    <div class="chart-container">
      <canvas id="ordersChart"></canvas>
    </div>

    <!-- Tabla de pedidos -->
    <div class="table-container">
      <table class="orders-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Confirmado en</th>
            <th>Creado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id">
            <td>{{ order.id }}</td>
            <td>{{ order.name }}</td>
            <td class="email">{{ order.email }}</td>
            <td>{{ order.phone }}</td>
            <td class="address">{{ order.address }}</td>
            <td>\${{ Number(order.total_amount).toFixed(2) }}</td>
            <td>
              <span :class="['status-badge', order.status]">
                {{ order.status }}
              </span>
            </td>
            <td>{{ formatDate(order.confirmed_at) }}</td>
            <td>{{ formatDate(order.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import Chart from "chart.js/auto";

const orders = ref([]);
const stats = ref([]);
let chartInstance = null;

const fetchOrders = async () => {
  try {
    const response = await api.get("/orders");
    if (response.data.success) orders.value = response.data.orders;
  } catch (error) {
    console.error("Error al cargar pedidos:", error);
  }
};

const fetchStats = async () => {
  try {
    const response = await api.get("/orders/stats");
    if (response.data.success) {
      stats.value = response.data.stats;
      renderChart();
    }
  } catch (error) {
    console.error("Error al cargar estadísticas:", error);
  }
};

const renderChart = () => {
  const ctx = document.getElementById("ordersChart");
  if (chartInstance) chartInstance.destroy();

  const labels = stats.value.map((s) => s.date);
  const totals = stats.value.map((s) => s.total_sales);
  const counts = stats.value.map((s) => s.orders_count);

  chartInstance = new Chart(ctx, {
    type: "bar",
    data: {
      labels,
      datasets: [
        {
          label: "Total de ventas (COP)",
          data: totals,
          yAxisID: "y",
          backgroundColor: "rgba(76, 175, 80, 0.5)",
          borderColor: "#4CAF50",
          borderWidth: 2,
        },
        {
          label: "Cantidad de pedidos",
          data: counts,
          yAxisID: "y1",
          type: "line",
          borderColor: "#2196F3",
          backgroundColor: "rgba(33, 150, 243, 0.2)",
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: "index", intersect: false },
      stacked: false,
      scales: {
        y: {
          type: "linear",
          position: "left",
          beginAtZero: true,
          title: { display: true, text: "Total (COP)" },
        },
        y1: {
          type: "linear",
          position: "right",
          beginAtZero: true,
          grid: { drawOnChartArea: false },
          title: { display: true, text: "Cantidad de pedidos" },
        },
      },
    },
  });
};

const formatDate = (datetime) => {
  if (!datetime) return "—";
  const d = new Date(datetime);
  return d.toLocaleString("es-CO");
};

onMounted(() => {
  fetchOrders();
  fetchStats();
});
</script>

<style scoped>
/* ---------- Base layout ---------- */
.orders-admin {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 25px;
  color: #333;
}

.page-title {
  font-size: 1.8rem;
  font-weight: bold;
  color: #333;
  text-align: center;
}

/* ---------- Chart container ---------- */
.chart-container {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  min-height: 300px;
  max-width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Ajusta el canvas para pantallas pequeñas */
.chart-container canvas {
  width: 100% !important;
  height: auto !important;
  max-height: 400px;
}

/* ---------- Table container ---------- */
.table-container {
  overflow-x: auto;
  background: #fff;
  color: #333;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.orders-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
  min-width: 700px; /* previene colapso en móviles */
}

.orders-table th,
.orders-table td {
  padding: 10px;
  border-bottom: 1px solid #ddd;
  text-align: left;
  white-space: nowrap;
}

.orders-table th {
  background: #f9f9f9;
  font-weight: 600;
  font-size: 0.9rem;
  color: #444;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 8px;
  text-transform: capitalize;
  font-weight: 500;
}

.status-badge.confirmed {
  background-color: #4caf50;
  color: white;
}

.status-badge.pending {
  background-color: #ffc107;
  color: #333;
}

/* ---------- Responsive design ---------- */

/* Tablets (≤1024px) */
@media (max-width: 1024px) {
  .orders-admin {
    padding: 15px;
  }

  .page-title {
    font-size: 1.5rem;
  }

  .orders-table th,
  .orders-table td {
    padding: 8px;
    font-size: 0.9rem;
  }

  .chart-container {
    padding: 15px;
    min-height: 250px;
  }
}

/* Móviles (≤768px) */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.3rem;
    text-align: center;
  }

  .chart-container {
    padding: 10px;
    min-height: 220px;
  }

  .orders-table {
    font-size: 0.85rem;
    min-width: 600px;
  }

  .orders-table th,
  .orders-table td {
    padding: 6px;
  }

  .table-container {
    padding: 10px;
    border-radius: 10px;
  }

  .status-badge {
    font-size: 0.8rem;
  }
}

/* Muy pequeños (≤480px) */
@media (max-width: 480px) {
  .orders-admin {
    gap: 15px;
  }

  .page-title {
    font-size: 1.1rem;
  }

  .orders-table {
    font-size: 0.8rem;
    min-width: 500px;
  }

  .chart-container {
    padding: 8px;
    min-height: 200px;
  }

  .status-badge {
    font-size: 0.75rem;
  }
}
</style>
