document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");
  
    form.addEventListener("submit", function (e) {
      e.preventDefault(); // Prevent immediate submit
  
      const nom = document.getElementById("nom").value.trim();
      const prenom = document.getElementById("prenom").value.trim();
      const email = document.getElementById("email").value.trim();
      const cin = document.getElementById("cin").value.trim();
      const pseudo = document.getElementById("pseudo").value.trim();
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirmPassword").value;
  
      // Basic non-empty check
      if (!nom || !prenom || !email || !cin || !pseudo || !password || !confirmPassword) {
        alert("Tous les champs sont obligatoires.");
        return;
      }
  
      // Email validation (basic pattern)
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert("Adresse email invalide.");
        return;
      }
  
      // CIN validation
      if (!/^\d{8}$/.test(cin)) {
        alert("CIN invalide. Il doit contenir exactement 8 chiffres.");
        return;
      }
  
      // Pseudo validation
      if (!/^[a-zA-Z]+$/.test(pseudo)) {
        alert("Le pseudo doit contenir uniquement des lettres.");
        return;
      }
  
      // Password length and ending
      if (password.length < 8) {
        alert("Le mot de passe doit contenir au moins 8 caractères.");
        return;
      }
  
      const lastChar = password.slice(-1);
      if (lastChar !== "$" && lastChar !== "#") {
        alert("Le mot de passe doit se terminer par $ ou #.");
        return;
      }
  
      // Confirm password
      if (password !== confirmPassword) {
        alert("Les mots de passe ne sont pas identiques.");
        return;
      }
  
      form.submit(); // Submit the form if everything is valid
    });
  });
  