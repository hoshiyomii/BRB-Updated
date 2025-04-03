document.addEventListener("DOMContentLoaded", loadAnnouncements);

document.getElementById("addAnnouncementForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let title = document.getElementById("title").value;
    let content = document.getElementById("content").value;
    let type = document.getElementById("type").value;
    let maxParticipants = document.getElementById("max_participants").value || null;

    fetch("add_announcement.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, content, type, maxParticipants })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("addMsg").innerText = data.message;
        loadAnnouncements(); // Reload list
    });
});

function loadAnnouncements() {
    fetch("fetch_announcements.php")
    .then(response => response.json())
    .then(data => {
        let container = document.getElementById("announcementsList");
        container.innerHTML = "";

        data.forEach(item => {
            let div = document.createElement("div");
            div.innerHTML = `
                <h3>${item.title}</h3>
                <p>${item.content}</p>
                <p><strong>Type:</strong> ${item.type}</p>
                ${item.type === "event" ? `<p><strong>Max Participants:</strong> ${item.max_participants}</p>` : ""}
                <button onclick="deleteAnnouncement(${item.id})">Delete</button>
                <button onclick="editAnnouncement(${item.id}, '${item.title}', '${item.content}', '${item.type}', ${item.max_participants})">Edit</button>
            `;
            container.appendChild(div);
        });
    });
}

function deleteAnnouncement(id) {
    if (!confirm("Are you sure you want to delete this announcement?")) return;

    fetch("delete_announcement.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    });
}


function editAnnouncement(id, title, content, type, maxParticipants) {
    let newTitle = prompt("Edit Title:", title);
    let newContent = prompt("Edit Content:", content);
    let newType = prompt("Edit Type (view-only/event):", type);
    let newMaxParticipants = newType === "event" ? prompt("Edit Max Participants:", maxParticipants) : null;

    fetch("edit_announcement.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, title: newTitle, content: newContent, type: newType, maxParticipants: newMaxParticipants })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadAnnouncements();
    });
}
