// admin.js

document.addEventListener('DOMContentLoaded', function() {
    // Example code to dynamically fetch and display data
    fetchRecentActivities();
});

function fetchRecentActivities() {
    // Simulated data fetch; replace with actual AJAX request
    const activities = [
        { user: 'John Doe', activity: 'Logged in', time: '2 hours ago' },
        { user: 'Jane Smith', activity: 'Updated Profile', time: '3 hours ago' },
        { user: 'Mike Johnson', activity: 'Purchased Subscription', time: '5 hours ago' }
    ];

    const tbody = document.getElementById('recent-activities');
    activities.forEach(activity => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${activity.user}</td>
            <td>${activity.activity}</td>
            <td>${activity.time}</td>
        `;
        tbody.appendChild(tr);
    });
}
