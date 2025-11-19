// Opcional: agregar animación al botón flotante
const waBtn = document.querySelector('.whatsapp-float');
waBtn.addEventListener('mouseenter', () => {
  waBtn.style.transform = 'scale(1.1)';
});
waBtn.addEventListener('mouseleave', () => {
  waBtn.style.transform = 'scale(1)';
});
