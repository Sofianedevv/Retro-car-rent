document.addEventListener("DOMContentLoaded", function () {
    const startDateInput = document.getElementById('reservation_startDate');
    const endDateInput = document.getElementById('reservation_endDate');
    const pricePerDayElement = document.getElementById('pricePerDay');
    const daysCountElement = document.getElementById('daysCount');
    const optionPriceElement = document.getElementById('options-price');
    const totalPriceElement = document.getElementById('totalPrice');
    const formElement = document.querySelector('form');

    const vehiclePrice = parseFloat(pricePerDayElement.dataset.price);
    let totalOptionPrice = 0;
    let totalPrice = vehiclePrice;

    function updateTotalPrice() {
        totalPrice = vehiclePrice * (daysCountElement.textContent || 1) + totalOptionPrice;
        totalPriceElement.textContent = totalPrice.toFixed(2).replace('.', ',') + " €";
        optionPriceElement.textContent = totalOptionPrice.toFixed(2).replace('.', ',') + " €";
    }

    function calculateTotalPrice(startDateValue, endDateValue) {
        if (startDateValue && endDateValue) {
            const startDate = new Date(startDateValue.trim());
            const endDate = new Date(endDateValue.trim());
    
            startDate.setHours(0, 0, 0, 0);
            endDate.setHours(0, 0, 0, 0);
    
            if (startDate <= endDate) {
                const diffTime = endDate.getTime() - startDate.getTime();
                const days = Math.ceil(diffTime / (1000 * 3600 * 24)) + 1; 
                daysCountElement.textContent = days;
    
                totalPrice = (days * vehiclePrice) + totalOptionPrice;
            } else {
                daysCountElement.textContent = "0";
            }
        } else {
            daysCountElement.textContent = "0";
        }
        updateTotalPrice();
    }

    startDateInput.addEventListener('change', function () {
        calculateTotalPrice(startDateInput.value, endDateInput.value);
    });

    endDateInput.addEventListener('change', function () {
        calculateTotalPrice(startDateInput.value, endDateInput.value);
    });

    if (startDateInput.value && endDateInput.value) {
        calculateTotalPrice(startDateInput.value, endDateInput.value);
    }

    const optionPlusButtons = document.querySelectorAll('.option-plus');
    const optionMinusButtons = document.querySelectorAll('.option-minus');

    optionPlusButtons.forEach(button => {
        button.addEventListener('click', function () {
            const optionId = button.getAttribute('data-option-id');
            const optionPrice = parseFloat(button.getAttribute('data-price'));
            const countElement = document.getElementById(`option_${optionId}_count`);
            let count = parseInt(countElement.textContent);

            if (count < 3) {
                count++;
                countElement.textContent = count;

                totalOptionPrice += optionPrice;
                updateTotalPrice();
            }
        });
    });

    optionMinusButtons.forEach(button => {
        button.addEventListener('click', function () {
            const optionId = button.getAttribute('data-option-id');
            const optionPrice = parseFloat(button.getAttribute('data-price'));
            const countElement = document.getElementById(`option_${optionId}_count`);
            let count = parseInt(countElement.textContent);

            if (count > 0) {
                count--;
                countElement.textContent = count;

                totalOptionPrice -= optionPrice;
                updateTotalPrice();
            }
        });
    });

    formElement.addEventListener('submit', function (event) {
        event.preventDefault();
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const vehicleId = document.getElementById('vehicle-data').getAttribute('data-vehicle-id');

        const selectedOptions = [];
        document.querySelectorAll('.option-plus').forEach(function(button) {
            const optionId = button.getAttribute('data-option-id');
            const countElement = document.getElementById(`option_${optionId}_count`);
            const count = parseInt(countElement.textContent);

            if (count > 0) {
                selectedOptions.push({
                    id: optionId,
                    count: count
                });
            }
        });

        const data = {
            vehicleId: parseInt(vehicleId, 10),
            startDate: startDate,
            endDate: endDate,
            totalPrice: totalPrice.toFixed(2), 
            options: selectedOptions
        };
        console.log(data);

        fetch('/recapitulatif', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "/recapitulatif";
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'envoi:', error);
        });
    });
});
