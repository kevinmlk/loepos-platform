<article class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4 w-full">
    <h2>Historiek</h2>
    <canvas id="dailyUploadsChart" width="400" height="50"></canvas>
</article>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('dailyUploadsChart').getContext('2d');
        const dailyUploadedDocuments = @json($dailyUploadedDocuments);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(dailyUploadedDocuments),
                datasets: [{
                    label: 'Uploaded Documents',
                    data: Object.values(dailyUploadedDocuments),
                    backgroundColor: 'rgb(198, 224, 236)',
                    borderColor: 'rgb(23, 96, 135)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5,
                        }
                    },
                }
            }
        });
    });
</script>
