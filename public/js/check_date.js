document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('search_start_date');
    const endDateInput = document.getElementById('search_end_date');
    const submitBtn = document.getElementById('search_btn');

    if (!startDateInput || !endDateInput) return;

    submitBtn.disabled = true;

    const showError = (input, message) => {
        let errorElement = input.nextElementSibling;

        if (!errorElement || !errorElement.classList.contains('input-error')) {
            errorElement = document.createElement('div');
            errorElement.className = 'input-error text-red-500 text-sm mt-1';
            input.insertAdjacentElement('afterend', errorElement);
        }

        errorElement.textContent = message;

        input.style.borderColor = 'red';
    };

    const clearMessageError = (input) => {
        let errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains('input-error')) {
            errorElement.remove();
        }
        input.style.borderColor = '';
    }


    const validateRange = () => {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (isNaN(startDate) || isNaN(endDate)) {
            console.error("Veuillez saisir des dates valides.");
            showError(startDateInput, "Veuillez saisir une date de départ valide.");
            showError(endDateInput, "Veuillez saisir une date de retour valide.");
            submitBtn.disabled = true;
            return;
        }

        clearMessageError(startDateInput);
        clearMessageError(endDateInput);

        if (startDate > endDate) {
            console.error("La date de départ doit être antérieure à la date de retour.");
            showError(startDateInput, "La date de départ doit être antérieure à la date de retour.");
            showError(endDateInput, "La date de retour doit être après ou égale à la date de départ.");
            submitBtn.disabled = true;
            return;
        }
        submitBtn.disabled = false;
    };

    startDateInput.addEventListener('input', validateRange);
    endDateInput.addEventListener('input', validateRange)
})



