window.onload = () => {
  // Récupération des liens supprimer des Images
  let linksImg = document.querySelectorAll("[data-delete='image']");
  // On boucle sur linksImg
  let linkImg;
  for (linkImg of linksImg) {
    // On écoute le clic
    linkImg.addEventListener("click", function (e) {
      // On empêche la navigation
      e.preventDefault();
      // On demande confirmation
      if (confirm("Voulez-vous supprimer cette image ?")) {
        // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
        fetch(this.getAttribute("href"), {
          method: "DELETE",
          header: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ _token : this.dataset.token })
        })
          .then(
            // On récupère la réponse en JSON
            (response) => response.json()
            )
          .then(data => {
            if (data.success)
              this.parentElement.parentElement.remove(),
                window.location.reload()
            else alert(data.error)
            })
          .catch((e) => alert(e));
      }
    });
  }

  // Récupération des liens supprimer des Vidéos
  let linksVideo = document.querySelectorAll("[data-delete='video']");
  // On boucle sur linksVideo
  let linkVideo;
  for (linkVideo of linksVideo) {
    // On écoute le clic
    linkVideo.addEventListener("click", function (f) {
      // On empêche la navigation
      f.preventDefault();
      // On demande confirmation
      if (confirm("Voulez-vous supprimer cette vidéo ?")) {
        // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
        fetch(this.getAttribute("href"), {
          method: "DELETE",
          header: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ _token : this.dataset.token })
        })
          .then(
            // On récupère la réponse en JSON
            (response) => response.json()
            )
          .then(data => {
            if (data.success)
              this.parentElement.parentElement.remove(),
                window.location.reload()
            else alert(data.error)
            })
          .catch((f) => alert(f));
      }
    })
  }
}