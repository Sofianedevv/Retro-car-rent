document.addEventListener('DOMContentLoaded', function () {
    const triButton = document.getElementById('tri-prix-desc');
    const vehiclesList = document.getElementById('vehicles-list');

    let currentOrder = 'desc'; 

    
    function sortVehicles(order) {
        const vehicles = Array.from(vehiclesList.children);

        vehicles.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price'));
            const priceB = parseFloat(b.getAttribute('data-price'));

            return order === 'asc' ? priceA - priceB : priceB - priceA;
        });

        vehicles.forEach(vehicle => vehiclesList.appendChild(vehicle));
    }

    triButton.addEventListener('click', () => {
        currentOrder = currentOrder === 'desc' ? 'asc' : 'desc';

        sortVehicles(currentOrder);

        triButton.innerHTML = currentOrder === 'desc'
            ? '<i class="fas fa-sort-amount-down mr-2"></i> Prix décroissant'
            : '<i class="fas fa-sort-amount-up mr-2"></i> Prix croissant';
    });
});
