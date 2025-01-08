document.addEventListener('DOMContentLoaded', function () {
    const vehicleElement = document.getElementById('vehicle-data'); 
    const vehicleId = vehicleElement ? vehicleElement.getAttribute('data-vehicle-id') : null;
    const dateRangeInput = document.getElementById('reservation_rangeDate');

    fetch(`/reservations/${vehicleId}`)
        .then(response => response.json())
        .then(data => {
            const disabledDates = data.map(reservation => {
                let startDate = new Date(reservation.startDate);
                let endDate = new Date(reservation.endDate);

                if (startDate > endDate) {
                    [startDate, endDate] = [endDate, startDate];
                }

                return {
                    start: startDate.toISOString().split('T')[0], 
                    end: endDate.toISOString().split('T')[0]      
                };
            });

            console.log("Plages de dates désactivées :", disabledDates);

            const disabledDatesFormatted = disabledDates.map(range => {
                const dates = [];
                let currentDate = new Date(range.start);

                while (currentDate <= new Date(range.end)) {
                    dates.push(currentDate.toISOString().split('T')[0]);
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                return dates;
            }).flat();

            console.log("Liste des dates désactivées :", disabledDatesFormatted);

            flatpickr(dateRangeInput, {
                mode: "range", 
                disable: disabledDatesFormatted,  
                dateFormat: "Y-m-d", 
                locale: "fr",  
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startDate = selectedDates[0]; 
                        const endDate = selectedDates[1];    
                   }
                },
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    if (disabledDatesFormatted.includes(dStr)) {
                        dayElem.classList.add('bg-red-300', 'text-red-500');
                    }
                }
            });
        })
        .catch(error => {
            console.error("Erreur lors de la récupération des dates de réservation :", error);
        });
});
