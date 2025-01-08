document.addEventListener('DOMContentLoaded', function () {
    const vehicleIdElement = document.querySelector("#vehicle-id");
    const startDateElement = document.querySelector("#start-date");
    const endDateElement = document.querySelector("#end-date");
    const totalPriceElement = document.querySelector("#total-price");

    if (!vehicleIdElement || !startDateElement || !endDateElement || !totalPriceElement) {
        console.error("Un ou plusieurs éléments nécessaires manquent dans la page HTML.");
        return;
    }

    const vehicleId = parseInt(vehicleIdElement.textContent.trim(), 10);
    const startDate = startDateElement.textContent.trim();
    const endDate = endDateElement.textContent.trim();
    const totalPrice = parseFloat(totalPriceElement.textContent.trim());

    document.querySelector(".btn-primary").addEventListener("click", function (e) {
        e.preventDefault();

        if (!vehicleId || !startDate || !endDate || isNaN(totalPrice)) {
            alert("Les données de réservation sont invalides. Veuillez vérifier vos informations.");
            return;
        }

        const data = {
            vehicleId: vehicleId,
            startDate: startDate,
            endDate: endDate,
            totalPrice: totalPrice,
        };

        console.log("Données prêtes à être envoyées :", data);

        fetch("/reservation", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then(response => {
                console.log("Statut de la réponse :", response.status);
                return response.json();
            })
            .then(data => {
                console.log("Réponse du serveur :", data);

                if (data.message) {
                    alert("Réservation réussie !");
                } else {
                    alert("Erreur lors de la réservation : " + (data.error || "Une erreur inconnue s'est produite."));
                }
            })
            .catch(error => {
                console.error("Erreur lors de la requête :", error);
                alert("Une erreur est survenue lors de la communication avec le serveur.");
            });
    });
});
