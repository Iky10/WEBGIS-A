document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('chartKondisi')) {
        var ctx = document.getElementById('chartKondisi').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Baik', 'Sedang', 'Rusak'],
                datasets: [{
                    data: [
                        window.CHART_DATA_BAIK || 0, 
                        window.CHART_DATA_SEDANG || 0, 
                        window.CHART_DATA_RUSAK || 0
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
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
