window.onload = () => {
    // Récupération des liens supprimer des commentaires
    let linksComments = document.querySelectorAll("[data-delete='comment']");
    // On boucle sur linksImg
    for(linkComment of linksComments) {
    // On écoute le clic
        linkComment.addEventListener("click", function(e) {
        // On empêche la navigation
        e.preventDefault();
        // On demande confirmation
        if (confirm("Êtes-vous sûr de vouloir supprimer ce commentaire ?")) {
            // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
            fetch(this.getAttribute("href"), {
            method: "DELETE",
            header: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ _token: this.dataset.token }),
            })
            .then(
            // On récupère la réponse en JSON
                (response ) => response.json()
                ).then((data) => {
                if (data.success)
                    this.parentElement.parentElement.remove(),
                    window.location.reload();
                    else alert(data.error);
                })
            .catch((e) => alert(e));
        }
    });
    }
}
