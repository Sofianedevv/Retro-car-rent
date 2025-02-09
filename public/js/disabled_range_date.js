document.addEventListener('DOMContentLoaded', function () {
    const vehicleElement = document.querySelector('[data-vehicle-id]');
    const vehicleId = vehicleElement ? vehicleElement.getAttribute('data-vehicle-id') : null;
    const startDateInput = document.getElementById('reservation_startDate');
    const endDateInput = document.getElementById('reservation_endDate');
    const startTimeInput = document.getElementById('reservation_startTime');
    const endTimeInput = document.getElementById('reservation_endTime');
    const submitButton = document.querySelector('button[type="submit"]'); 

    if (!vehicleId || !startDateInput || !endDateInput || !startTimeInput || !endTimeInput || !submitButton) {
        console.error("Certains éléments requis ne sont pas trouvés dans le DOM.");
        return;
    }

    submitButton.disabled = true; 

    fetch(`/dates/reservations/${vehicleId}`)
        .then(response => response.json())
        .then(data => {
            const disabledRanges = data.map(reservation => ({
                start: new Date(reservation.startDate),
                end: new Date(reservation.endDate)
            }));

            console.log("Plages de dates désactivées :", disabledRanges);

            const isRangeOverlapping = (start, end) => {
                return disabledRanges.some(range => {
                    console.log(start, end)
                    return (
                        (start >= range.start && start < range.end) || 
                        (end > range.start && end <= range.end) ||    
                        (start <= range.start && end >= range.end)    
                    );
                });
            };

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

            const clearError = (input) => {
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('input-error')) {
                    errorElement.remove();
                }
                
                input.style.borderColor = '';
            };

            const getDateTimeFromInputs = (dateInput, timeInput) => {
                const date = dateInput.value;
                const time = timeInput.value || '00:00'; 
                return new Date(`${date}T${time}:00`);
            };

            const validateRange = () => {
                const startDate = getDateTimeFromInputs(startDateInput, startTimeInput);
                const endDate = getDateTimeFromInputs(endDateInput, endTimeInput);

                if (isNaN(startDate) || isNaN(endDate)) {
                    console.error("Veuillez saisir des dates valides.");
                    showError(startDateInput, "Veuillez saisir une date de début valide.");
                    showError(endDateInput, "Veuillez saisir une date de fin valide.");
                    submitButton.disabled = true; 
                    return;
                }

                clearError(startDateInput);
                clearError(endDateInput);

                if (startDate >= endDate) {
                    console.error("La date de début doit être antérieure à la date de fin.");
                    showError(startDateInput, "La date de début doit être avant la date de fin.");
                    showError(endDateInput, "La date de fin doit être après la date de début.");
                    submitButton.disabled = true;
                    return;
                }

                if (isRangeOverlapping(startDate, endDate)) {
                    console.error(
                        `Erreur : La plage sélectionnée (${startDate.toISOString()} à ${endDate.toISOString()}) chevauche une plage de réservation existante.`
                    );
                    showError(startDateInput, `Cette plage chevauche une réservation existante (de ${startDate} à ${endDate}).`);
                    showError(endDateInput, `Cette plage chevauche une réservation existante (de ${startDate} à ${endDate}).`);
                    submitButton.disabled = true; 
                } else {
                    const lastReservationBeforeStart = disabledRanges
                        .filter(reservation => reservation.end <= startDate)
                        .sort((a, b) => b.end - a.end)[0]; 

                    if (lastReservationBeforeStart) {
                        const lastReservationEndTime = lastReservationBeforeStart.end;
                        const minimumAvailableTime = new Date(lastReservationEndTime.getTime() + 30 * 60 * 1000); 

                        if (startDate < minimumAvailableTime) {
                            const formattedTime = minimumAvailableTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            console.error(`Erreur : Le véhicule sera disponible à partir de ${formattedTime}.`);
                            showError(startDateInput, `Le véhicule sera disponible à partir de ${formattedTime}.`);
                            submitButton.disabled = true;
                            return;
                        }
                    }

                    console.log(`Plage valide : ${startDate.toISOString()} à ${endDate.toISOString()}.`);
                    clearError(startDateInput);
                    clearError(endDateInput);
                    submitButton.disabled = false;
                }
            };

            startDateInput.addEventListener('input', validateRange);
            endDateInput.addEventListener('input', validateRange);
            startTimeInput.addEventListener('input', validateRange);
            endTimeInput.addEventListener('input', validateRange);
        })
        .catch(error => {
            console.error("Erreur lors de la récupération des données de réservation :", error);
        });
});
