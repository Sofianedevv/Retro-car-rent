document.addEventListener('DOMContentLoaded', function () {
    const vehicleElement = document.getElementById('vehicle-data');
    const vehicleId = vehicleElement ? vehicleElement.getAttribute('data-vehicle-id') : null;
    const startDateInput = document.getElementById('reservation_startDate');
    const endDateInput = document.getElementById('reservation_endDate');

    if (!vehicleId || !startDateInput || !endDateInput) {
        console.error("Certains éléments requis ne sont pas trouvés dans le DOM.");
        return;
    }

    fetch(`/dates-reservations/${vehicleId}`)
        .then(response => response.json())
        .then(data => {
            const disabledRanges = data.map(reservation => ({
                start: new Date(reservation.startDate),
                end: new Date(reservation.endDate)
            }));

            console.log("Plages de dates désactivées :", disabledRanges);

            const isRangeOverlapping = (start, end) => {
                return disabledRanges.some(range => {
                    return (
                        (start >= range.start && start <= range.end) || 
                        (end >= range.start && end <= range.end) ||     
                        (start <= range.start && end >= range.end)      
                    );
                });
            };

            
            const validateRange = () => {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (isNaN(startDate) || isNaN(endDate)) {
                    console.error("Veuillez saisir des dates valides.");
                    return;
                }

                if (startDate >= endDate) {
                    console.error("La date de début doit être antérieure à la date de fin.");
                    return;
                }

                if (isRangeOverlapping(startDate, endDate)) {
                    console.error(
                        `Erreur : La plage sélectionnée (${startDate.toISOString()} à ${endDate.toISOString()}) chevauche une plage de réservation existante.`
                    );
                } else {
                    console.log(
                        `Plage valide : ${startDate.toISOString()} à ${endDate.toISOString()}.`
                    );
                }
            };

            
            startDateInput.addEventListener('input', validateRange);
            endDateInput.addEventListener('input', validateRange);
        })
        .catch(error => {
            console.error("Erreur lors de la récupération des données de réservation :", error);
        });
});
