document.addEventListener("DOMContentLoaded", function () {
    const rangeDateInput = document.getElementById('reservation_rangeDate');
    const priceElement = document.querySelector('[data-vehicle-price]');    
    const totalPriceInput = document.getElementById('reservation_totalPrice');
    const vehiclePrice = parseFloat(priceElement.getAttribute('data-vehicle-price'));

    function calculateTotalPrice(rangeValue) {
        const [startDateData, endDateData] = rangeValue.split(' au ');
        if (startDateData && endDateData && startDateData <= endDateData) {
            const startDate = new Date(startDateData.trim());
            const endDate = new Date(endDateData.trim());
            const diffTime = endDate - startDate;
            const days = diffTime / (1000 * 3600 * 24) + 1;
            const totalPrice = days * vehiclePrice; 
            priceElement.textContent = totalPrice.toFixed(2) + " €";
            totalPriceInput.value = totalPrice.toFixed(2);
        } else {
            priceElement.textContent = vehiclePrice.toFixed(2) + " €";
            totalPriceInput.value = vehiclePrice.toFixed(2);
        }
    }

    rangeDateInput.addEventListener('change', function () {
        calculateTotalPrice(rangeDateInput.value);
    })
    if (rangeDateInput.value) {
        calculateTotalPrice(rangeDateInput.value);
    }
});
