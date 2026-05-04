document.addEventListener("DOMContentLoaded", () => {


    console.log("panier.js chargé !");

    /* ============================================================
       1) AJOUT AU PANIER (AJAX)
    ============================================================ */
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.preventDefault();

            try {
                const response = await fetch(btn.href);
                const data = await response.json();

                const cart = document.querySelector('.cart');
                if (cart) {
                    cart.innerHTML = `
                        <img src="/images/cart.png" alt="Panier" class="icon-lg">
                        <span class="cart-count">${data.count}</span>
                    `;
                }

                btn.style.transform = "scale(0.95)";
                btn.style.backgroundColor = "#cfa600";

                setTimeout(() => {
                    btn.style.transform = "scale(1)";
                    btn.style.backgroundColor = "";
                }, 200);

            } catch (error) {
                console.error("Erreur AJAX :", error);
            }
        });
    });



    /* ============================================================
       2) OUVERTURE DU MODAL + CHARGEMENT DES INGREDIENTS
    ============================================================ */
    let currentPlatId = null;

    document.querySelectorAll(".btn-custom").forEach(btn => {
        btn.addEventListener("click", async () => {

            currentPlatId = btn.dataset.platId;

            const response = await fetch(`/plat/${currentPlatId}/ingredients`);
            const data = await response.json();

            document.getElementById("modalPlatName").innerText =
                "Personnaliser " + data.plat;

            loadClientIngredients(data.ingredients);
            loadAdminIngredients(data.ingredients);

            document.getElementById("customModal").style.display = "flex";
            document.getElementById("confirmCustom").dataset.platId = currentPlatId;
        });
    });



    /* ============================================================
       3) ONGLET CLIENT : AFFICHAGE DES INGREDIENTS
    ============================================================ */
    function loadClientIngredients(ingredients) {
        const list = document.getElementById("modalIngredientList");
        const replaceFrom = document.getElementById("replaceFrom");
        const replaceTo = document.getElementById("replaceTo");

        list.innerHTML = "";
        replaceFrom.innerHTML = '<option value="">Ingrédient à remplacer</option>';
        replaceTo.innerHTML = '<option value="">Remplacer par…</option>';

        ingredients.forEach(ing => {
            const li = document.createElement("li");

            li.innerHTML = `
                <label>
                    <input type="checkbox"
                           class="remove-checkbox"
                           value="${ing.id}"
                           ${ing.essentiel ? "disabled" : ""}>
                    ${ing.nom}
                    ${ing.essentiel ? '<span class="tag-essential">Essentiel</span>' : ''}
                </label>
            `;

            list.appendChild(li);

            if (!ing.essentiel) {
                replaceFrom.innerHTML += `<option value="${ing.id}">${ing.nom}</option>`;
            }
        });
    }



    /* ============================================================
       4) ONGLET ADMIN : AFFICHAGE + SUPPRESSION
    ============================================================ */
    function loadAdminIngredients(ingredients) {
        const adminList = document.getElementById("adminIngredientList");
        adminList.innerHTML = "";

        ingredients.forEach(ing => {
            const li = document.createElement("li");
            li.dataset.id = ing.id;

            li.innerHTML = `
                <span>${ing.nom}</span>
                ${ing.essentiel ? '<span class="tag-essential">Essentiel</span>' : ''}
                <button class="btn-danger deleteIng">Supprimer</button>
            `;

            adminList.appendChild(li);
        });

        document.querySelectorAll(".deleteIng").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.closest("li").dataset.id;

                await fetch(`/ingredient/delete/${id}`);
                reloadIngredients();
            });
        });
    }



    /* ============================================================
       5) ONGLET ADMIN : AJOUT D’UN INGREDIENT
    ============================================================ */
    document.getElementById("addIngredientBtn").addEventListener("click", async () => {
        const name = document.getElementById("newIngredientName").value.trim();
        const essentiel = document.getElementById("newIngredientEssentiel").checked;

        if (!name) return;

        await fetch('/ingredient/add', {
            method: "POST",
            body: new URLSearchParams({
                nom: name,
                essentiel: essentiel,
                platId: currentPlatId
            })
        });

        document.getElementById("newIngredientName").value = "";
        document.getElementById("newIngredientEssentiel").checked = false;

        reloadIngredients();
    });



    /* ============================================================
       6) RECHARGER LES INGREDIENTS (CLIENT + ADMIN)
    ============================================================ */
    async function reloadIngredients() {
        const response = await fetch(`/plat/${currentPlatId}/ingredients`);
        const data = await response.json();

        loadClientIngredients(data.ingredients);
        loadAdminIngredients(data.ingredients);
    }



    /* ============================================================
       7) FERMETURE DU MODAL
    ============================================================ */
    document.getElementById("cancelModal").addEventListener("click", () => {
        document.getElementById("customModal").style.display = "none";
    });



    /* ============================================================
       8) VALIDATION DE LA PERSONNALISATION CLIENT
    ============================================================ */
    document.getElementById("confirmCustom").addEventListener("click", async () => {

        const platId = currentPlatId;

        const removed = [...document.querySelectorAll(".remove-checkbox:checked")]
            .map(cb => cb.value);

        const replaceFrom = document.getElementById("replaceFrom").value;
        const replaceTo = document.getElementById("replaceTo").value;

        const replaced = (replaceFrom && replaceTo)
            ? [{ from: replaceFrom, to: replaceTo }]
            : [];

        const payload = { removed, replaced };

        const response = await fetch(`/panier/add-custom/${platId}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        const cart = document.querySelector('.cart');
        if (cart) {
            cart.innerHTML = `
                <img src="/images/cart.png" alt="Panier" class="icon-lg">
                <span class="cart-count">${data.count}</span>
            `;
        }

        document.getElementById("customModal").style.display = "none";
    });



    /* ============================================================
       9) ONGLET DU MODAL (CLIENT / ADMIN)
    ============================================================ */
    document.querySelectorAll('.modal-tabs .tab').forEach(btn => {
        btn.addEventListener('click', () => {

            document.querySelectorAll('.modal-tabs .tab')
                .forEach(t => t.classList.remove('active'));

            document.querySelectorAll('.tab-content')
                .forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        });
    });



    /* ============================================================
       10) FORMATTAGE PAIEMENT (inchangé)
    ============================================================ */
    const form = document.getElementById("paymentForm");
    const button = document.getElementById("payButton");

    if (form && button) {
        form.addEventListener("submit", () => {
            button.classList.add("btn-loading");
            button.disabled = true;
        });
    }

    const titulaire = document.querySelector('#titulaire');
    if (titulaire) {
        titulaire.addEventListener("input", () => {
            titulaire.value = titulaire.value.replace(/[0-9]/g, "");
        });
    }

    const numero = document.querySelector('#numero');
    if (numero) {
        numero.addEventListener("input", () => {
            numero.value = numero.value
                .replace(/\D/g, "")
                .replace(/(.{4})/g, "$1 ")
                .trim();
        });
    }

    const expiration = document.querySelector('#expiration');
    if (expiration) {
        expiration.addEventListener("input", () => {
            let v = expiration.value.replace(/\D/g, "");

            if (v.length >= 2) {
                let mois = parseInt(v.slice(0, 2));
                if (mois === 0) mois = 1;
                if (mois > 12) mois = 12;

                v = mois.toString().padStart(2, "0")
                    + (v.length > 2 ? "/" + v.slice(2, 4) : "");
            }

            expiration.value = v;
        });
    }

});
