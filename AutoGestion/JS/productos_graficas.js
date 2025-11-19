let indiceGrafica = 0;

function mostrarEstadisticas() {
  // Desactiva botón de búsqueda
document.getElementById("btnBusqueda").classList.add("disabled");

  const panel = document.getElementById("graficaEstadisticas");
  const panelTabla = document.getElementById("panelProductos");

  // Asegura que el panel de gráficas se muestre siempre
  panel.style.display = "block";
  panel.classList.add("visible");

  // Oculta el panel de tabla de productos
  panelTabla.style.display = "none";
  panelTabla.classList.remove("visible");

  // Reiniciar a la primera gráfica (índice 0)
  indiceGrafica = 0;
  const graficas = document.querySelectorAll(".grafica-slide");
  graficas.forEach((g, i) => g.classList.toggle("visible", i === 0));

  cargarGraficas();

  if (!window.intervalGraficas) {
    window.intervalGraficas = setInterval(cargarGraficas, 10000);
  }
}


function cambiarGrafica(direccion) {
  const graficas = document.querySelectorAll(".grafica-slide");
  graficas[indiceGrafica].classList.remove("visible");
  indiceGrafica = (indiceGrafica + direccion + graficas.length) % graficas.length;
  graficas[indiceGrafica].classList.add("visible");
}

function cargarGraficas() {
  fetch('../PHP/obtener_datos_grafica.php')
    .then(res => res.json())
    .then(data => {
      const textoGrandeBlanco = {
        color: 'white',
        font: {
          size: 16,
          weight: 'bold'
        }
      };

      // Gráfica 1: Más y menos vendidos
      const ctx1 = document.getElementById("graficaMasVendidos").getContext("2d");
      if (window.grafica1) window.grafica1.destroy();
      window.grafica1 = new Chart(ctx1, {
        type: "bar",
        data: {
          labels: [
            data.masVendido?.nombre || "Más Vendido",
            data.menosVendido?.nombre || "Menos Vendido"
          ],
          datasets: [{
            label: "Unidades",
            data: [
              data.masVendido?.cantidad || 0,
              data.menosVendido?.cantidad || 0
            ],
            backgroundColor: ["#1d4ed8", "#ef4444"]
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: "Producto Más y Menos Vendido",
              color: 'white',
              font: { size: 20 }
            },
            legend: { display: false },
            datalabels: {
              anchor: 'end',
              align: 'top',
              color: 'white',
              font: { size: 16 },
              formatter: val => `${val}`
            }
          },
          scales: {
            x: { ticks: textoGrandeBlanco },
            y: {
              beginAtZero: true,
              ticks: textoGrandeBlanco
            }
          }
        },
        plugins: [ChartDataLabels]
      });

      // Gráfica 2: Productos sin ventas
      const ctx2 = document.getElementById("graficaSinVentas").getContext("2d");
      if (window.grafica2) window.grafica2.destroy();
      window.grafica2 = new Chart(ctx2, {
        type: "doughnut",
        data: {
          labels: data.sinVentas.map(p => p.nombre),
          datasets: [{
            label: "Sin ventas",
            data: data.sinVentas.map(() => 1),
            backgroundColor: data.sinVentas.map(() =>
              `rgba(${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},0.7)`
            )
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: "Productos Sin Ventas",
              color: 'white',
              font: { size: 20 }
            },
            legend: {
              labels: textoGrandeBlanco
            },
            datalabels: {
              color: 'white',
              font: { size: 14 }
            }
          }
        },
        plugins: [ChartDataLabels]
      });

      // Gráfica 3: Ventas totales
      const ctx3 = document.getElementById("graficaTotalVentas").getContext("2d");
      if (window.grafica3) window.grafica3.destroy();
      window.grafica3 = new Chart(ctx3, {
        type: "bar",
        data: {
          labels: ["Ventas Totales"],
          datasets: [{
            label: "$",
            data: [data.totalVentas || 0],
            backgroundColor: "#22c55e"
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: "Ventas Totales ($)",
              color: 'white',
              font: { size: 20 }
            },
            legend: { display: false },
            datalabels: {
              color: 'white',
              font: { size: 16 }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: textoGrandeBlanco
            },
            x: {
              ticks: textoGrandeBlanco
            }
          }
        },
        plugins: [ChartDataLabels]
      });

      // Gráfica 4: Comparativa general
      const ctx4 = document.getElementById("graficaComparativa").getContext("2d");
      if (window.grafica4) window.grafica4.destroy();
      window.grafica4 = new Chart(ctx4, {
        type: "line",
        data: {
          labels: data.todos.map(p => p.nombre),
          datasets: [{
            label: "Vendidos",
            data: data.todos.map(p => p.cantidad),
            borderColor: "#3b82f6",
            fill: false,
            tension: 0.3
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: "Comparativa General",
              color: 'white',
              font: { size: 20 }
            },
            legend: {
              labels: textoGrandeBlanco
            },
            datalabels: {
              color: 'white',
              font: { size: 14 }
            }
          },
          scales: {
            y: { beginAtZero: true, ticks: textoGrandeBlanco },
            x: { ticks: textoGrandeBlanco }
          }
        },
        plugins: [ChartDataLabels]
      });
    });
}
