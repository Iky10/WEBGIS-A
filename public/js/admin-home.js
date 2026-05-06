document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('chartKondisi')) {
        var ctx = document.getElementById('chartKondisi').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Kosong', 'Sedang Dipakai'],
                datasets: [{
                    data: [
                        window.CHART_DATA_KOSONG || 0, 
                        window.CHART_DATA_DIPAKAI || 0 
                    ],
                    backgroundColor: ['#6c757d', '#28a745'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '65%',
            }
        });
    }
});
