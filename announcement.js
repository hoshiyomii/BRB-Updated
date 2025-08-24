document.getElementById("typeSelect").addEventListener("change", function() {
    let maxParticipantsLabel = document.getElementById("maxParticipantsLabel");
    let maxParticipants = document.getElementById("maxParticipants");

    if (this.value === "event") {
        maxParticipantsLabel.style.display = "block";
        maxParticipants.style.display = "block";
        maxParticipants.required = true;
    } else {
        maxParticipantsLabel.style.display = "none";
        maxParticipants.style.display = "none";
        maxParticipants.required = false;
    }
});
