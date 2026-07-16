document.addEventListener("DOMContentLoaded", function () {

    const mapElement = document.getElementById("worldMap");

    if (!mapElement || typeof window.countriesMap === "undefined") {
        return;
    }

    const map = L.map("worldMap", {
        zoomControl: true
    }).setView([20, 0], 2);

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution: "&copy; OpenStreetMap"
        }
    ).addTo(map);

    window.countriesMap.forEach(country => {

        if (!country.lat || !country.lng) {
            return;
        }

        let color = "#16A34A";

        if (country.risk_level === "HIGH") {
            color = "#DC2626";
        } else if (country.risk_level === "MEDIUM") {
            color = "#F59E0B";
        }

        L.circleMarker(
            [country.lat, country.lng],
            {
                radius: 7,
                fillColor: color,
                color: "#fff",
                weight: 2,
                fillOpacity: .9
            }
        )
        .bindPopup(`
            <strong>${country.name}</strong><br>
            Risk Score : ${country.risk_score}<br>
            Level : ${country.risk_level}
        `)
        .addTo(map);

    });

});