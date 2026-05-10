
function setRole(role) {
    currentRole = role;
    document.querySelectorAll(".demo-bar .demo-btn").forEach((b) => {
    if (b.textContent === "Читатель" || b.textContent === "Автор")
        b.classList.toggle(
        "active",
        b.textContent
            .toLowerCase()
            .includes(role === "reader" ? "чита" : "авт"),
        );
    });
    document.getElementById("articlesCard").style.display =
    role === "author" ? "block" : "none";
    document.getElementById("badgeRole").textContent =
    role === "author" ? "Автор" : "Читатель";
    document.getElementById("badgeRole").className =
    "badge " + (role === "author" ? "badge--author" : "badge--reader");
}

function setSub(active) {
    hasSub = active;
    document.getElementById("btn-sub").classList.toggle("active", active);
    document
    .getElementById("btn-nosub")
    .classList.toggle("active", !active);
    document.getElementById("badgeSub").style.display = active
    ? "inline-block"
    : "none";
    document.getElementById("subCardValue").textContent = active
    ? "Активна"
    : "Нет";
    document.getElementById("subCardSub").textContent = active
    ? "Ежегодный план · до 01.01.2027"
    : "Нажмите, чтобы подписаться";
    document
    .getElementById("subInfoPanel")
    .classList.toggle("visible", active);
    document.getElementById("subOfferPanel").classList.remove("visible");
}

function handleSubCard(e) {
    e.preventDefault();
    if (hasSub) {
    document.getElementById("subInfoPanel").classList.add("visible");
    document.getElementById("subOfferPanel").classList.remove("visible");
    } else {
    document.getElementById("subOfferPanel").classList.add("visible");
    document.getElementById("subInfoPanel").classList.remove("visible");
    }
    document
    .getElementById("subOfferPanel")
    .scrollIntoView({ behavior: "smooth", block: "start" });
}

function activateSub() {
    setSub(true);
    document.getElementById("subOfferPanel").classList.remove("visible");
    document.getElementById("subInfoPanel").classList.add("visible");
}

function toggleFollow(btn) {
    const following = btn.classList.contains("following");
    btn.classList.toggle("following", !following);
    btn.textContent = following ? "+ Подписаться" : "✓ Подписан";
}