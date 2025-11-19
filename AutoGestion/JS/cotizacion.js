document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('quoteForm');
  const summary = document.getElementById('selectedServices');
  const costEl = document.getElementById('estimatedCost');
  const fields = form.querySelectorAll('select, input[name=modelo], input[name=anio], input[name=km]');
  const summaryFields = document.querySelectorAll('.sidebar .summary-list li');

  form.addEventListener('change', updateSummary);
  form.addEventListener('submit', e => {
    e.preventDefault();
    alert('Tu solicitud ha sido enviada. Pronto te contactaremos.');
    form.reset();
    updateSummary();
  });

  function updateSummary(){
    const marca = form.marca.value, modelo = form.modelo.value,
          anio = form.anio.value, km = form.km.value;
    summaryFields[0].textContent = `Marca: ${marca || '—'}`;
    summaryFields[1].textContent = `Modelo: ${modelo || '—'}`;
    summaryFields[2].textContent = `Año: ${anio || '—'}`;
    summaryFields[3].textContent = `Kilometraje: ${km? km + ' km' : '—'}`;
    const servicios = Array.from(form.querySelectorAll('input[type=checkbox]:checked'));
    summary.innerHTML = servicios.length
      ? servicios.map(s => `<li>${s.value}</li>`).join('')
      : '<li class="empty">(ninguno seleccionado)</li>';
    const total = servicios.reduce((sum, s) => sum + parseInt(s.dataset.price), 0);
    costEl.textContent = total ? total : '—';
  }

  updateSummary();
});
