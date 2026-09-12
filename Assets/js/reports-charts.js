document.addEventListener('DOMContentLoaded', () => {

    const salesCanvas = document.getElementById('dailySalesChart');
    if (salesCanvas) {
        const salesLabels = JSON.parse(salesCanvas.dataset.labels || '[]');
        const salesData = JSON.parse(salesCanvas.dataset.values || '[]');

        new Chart(salesCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: salesData,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }


    const customersCanvas = document.getElementById('topCustomersChart');
    if (customersCanvas) {
        const customerLabels = JSON.parse(customersCanvas.dataset.labels || '[]');
        const customerData = JSON.parse(customersCanvas.dataset.values || '[]');

        new Chart(customersCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: customerLabels,
                datasets: [{
                    label: 'Amount Spent ($)',
                    data: customerData,
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});