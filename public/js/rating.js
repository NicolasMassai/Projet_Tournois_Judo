document.addEventListener("DOMContentLoaded", () => {
    const ratingGroups = document.querySelectorAll(".rating");

    ratingGroups.forEach(group => {
        const stars = group.querySelectorAll(".star");

        stars.forEach((star, index) => {
            // Survol
            star.addEventListener("mouseover", () => {
                resetStars(group);
                fillStars(stars, index, "orange");
            });

            // Quitter le survol
            star.addEventListener("mouseout", () => {
                resetStars(group);
                const checkedIndex = getCheckedIndex(group);
                if (checkedIndex !== -1) {
                    fillStars(stars, checkedIndex, "gold");
                }
            });

            // Clic
            star.addEventListener("click", () => {
                const radio = document.getElementById(star.htmlFor);
                if (radio) {
                    radio.checked = true;
                }
                resetStars(group);
                fillStars(stars, index, "gold");
            });
        });
    });

    // Réinitialiser les couleurs d'étoiles
    function resetStars(group) {
        const stars = group.querySelectorAll(".star");
        stars.forEach(star => {
            star.style.color = "#ccc"; // Couleur par défaut (étoiles vides)
        });
    }

    // Remplir les étoiles jusqu'à un index donné
    function fillStars(stars, index, color) {
        for (let i = 0; i <= index; i++) {
            stars[i].style.color = color;
        }
    }

    // Récupérer l'index de l'étoile actuellement cochée
    function getCheckedIndex(group) {
        const radios = group.querySelectorAll("input");
        for (let i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                return i;
            }
        }
        return -1;
    }
});
