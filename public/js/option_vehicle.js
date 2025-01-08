document.addEventListener('DOMContentLoaded', function () {

    const vehicleElement = document.getElementById('total-price'); 
    const vehiclePrice = vehicleElement ? vehicleElement.getAttribute('data-vehicle-price') : null;

    let basePrice = parseFloat(vehiclePrice) || 0;
    let totalPrice = basePrice;

    let totalOptionsPrice = 0;

    const optionPlusButtons = document.querySelectorAll('.option-plus');
    const optionMinusButtons = document.querySelectorAll('.option-minus');
    const optionsPriceDisplay = document.getElementById('options-price');
    const totalPriceDisplay = document.getElementById('total-price');

    function updateTotalPrice() {
        totalPrice = basePrice + totalOptionsPrice;
        totalPriceDisplay.textContent = totalPrice.toFixed(2);
        optionsPriceDisplay.textContent = totalOptionsPrice.toFixed(2);
    }

    optionPlusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const optionId = button.getAttribute('data-option-id');
            const optionPrice = parseFloat(button.getAttribute('data-price'));
            const countElement = document.getElementById(`option_${optionId}_count`);
            let count = parseInt(countElement.textContent);

            if (count < 3) {
                count++;
                countElement.textContent = count;

                totalOptionsPrice += optionPrice;
                updateTotalPrice();
            }
        });
    });

    optionMinusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const optionId = button.getAttribute('data-option-id');
            const optionPrice = parseFloat(button.getAttribute('data-price'));
            const countElement = document.getElementById(`option_${optionId}_count`);
            let count = parseInt(countElement.textContent);

            if (count > 0) {
                count--;
                countElement.textContent = count;

                totalOptionsPrice -= optionPrice;
                updateTotalPrice();
            }
        });
    });
});