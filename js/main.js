document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");
  
    form.addEventListener("submit", function (e) {
      const nom = document.getElementById("nom").value.trim();
      const prenom = document.getElementById("prenom").value.trim();
      const cin = document.getElementById("cin").value.trim();
      const email = document.getElementById("email").value.trim();
      const nomAssociation = document.getElementById("associationName").value.trim();
      const adresseAssociation = document.getElementById("associationAddress").value.trim();
      const matriculeFiscal = document.getElementById("fiscalId").value.trim();
      const pseudo = document.getElementById("pseudo").value.trim();
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirmPassword").value;
      const logo = document.getElementById("logo").files[0];
  
      if (!nom || !prenom || !cin || !email || !nomAssociation || !adresseAssociation || !matriculeFiscal || !pseudo || !password || !confirmPassword || !logo) {
        alert("Tous les champs sont obligatoires.");
        e.preventDefault();
        return;
      }
  
      if (!/^\d{8}$/.test(cin)) {
        alert("Le numéro CIN doit contenir exactement 8 chiffres.");
        e.preventDefault();
        return;
      }
  
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert("Adresse email invalide.");
        e.preventDefault();
        return;
      }
  
      if (!/^\$[A-Z]{3}\d{2}$/.test(matriculeFiscal)) {
        alert("L'identifiant fiscal doit être sous la forme : $ABC12");
        e.preventDefault();
        return;
      }
  
      if (!/^[a-zA-Z]+$/.test(pseudo)) {
        alert("Le pseudo doit contenir uniquement des lettres.");
        e.preventDefault();
        return;
      }
  
      if (password.length < 8 || !/[$#]$/.test(password)) {
        alert("Le mot de passe doit contenir au moins 8 caractères et se terminer par $ ou #.");
        e.preventDefault();
        return;
      }
  
      if (password !== confirmPassword) {
        alert("Les mots de passe ne correspondent pas.");
        e.preventDefault();
        return;
      }
    });
  });
  