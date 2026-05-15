/**
 * Admin Dashboard Charts
 */

const initDashboardCharts = async () => {
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        const monthlyData = await fetch('../../api/attendanceMonthly.php').then(r => r.json());
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: Object.keys(monthlyData),
                datasets: [{
                    label: 'Total Attendance',
                    data: Object.values(monthlyData),
                    borderColor: '#4F46E5',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(79, 70, 229, 0.05)'
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    const leaveCtx = document.getElementById('leaveChart');
    if (leaveCtx) {
        const leaveData = await fetch('../../api/leaveCountApi.php').then(r => r.json());
        new Chart(leaveCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sick Leave', 'Personal Leave'],
                datasets: [{
                    data: [
                        leaveData.sickLeaveCounts.reduce((a, b) => a + b, 0),
                        leaveData.personalLeaveCounts.reduce((a, b) => a + b, 0)
                    ],
                    backgroundColor: ['#EF4444', '#F59E0B'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', initDashboardCharts);
