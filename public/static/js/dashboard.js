document.addEventListener('DOMContentLoaded', function () {
    const dateElement = document.getElementById('dashboard-current-date');

    if (!dateElement) {
        return;
    }

    dateElement.textContent = new Intl.DateTimeFormat('es-MX', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(new Date());
});
