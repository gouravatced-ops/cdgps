document.addEventListener("DOMContentLoaded", () => {
  // (() => {
  //   const input = document.getElementById("domain_path");
  //   if (!input) return;

  //   const prefix = "https://";

  //   // ensure prefix on load
  //   if (!input.value.startsWith(prefix)) {
  //     input.value = prefix;
  //   }

  //   input.addEventListener("input", () => {
  //     if (!input.value.startsWith(prefix)) {
  //       input.value = prefix;
  //     }

  //     let domainPart = input.value.slice(prefix.length);
  //     domainPart = domainPart.toLowerCase().replace(/[^a-z.]/g, "");

  //     input.value = prefix + domainPart;
  //   });

  //   const lockCursor = () => {
  //     if (input.selectionStart < prefix.length) {
  //       input.setSelectionRange(prefix.length, prefix.length);
  //     }
  //   };

  //   input.addEventListener("keydown", lockCursor);
  //   input.addEventListener("click", lockCursor);
  // })();

  /* =====================================================
     2 PASSWORD + CONFIRM PASSWORD VALIDATION
  ===================================================== */
  (() => {
    const passwordInput = document.getElementById("UserPassword");
    const confirmInput = document.getElementById("confirmPassword");
    const mismatchText = document.getElementById("passwordMismatch");
    const criteriaBox = document.getElementById("passwordCriteria");
    const toggleBtn = document.getElementById("togglePassword");

    if (!passwordInput || !confirmInput) return;

    const form = passwordInput.closest("form");
    const submitBtn = form?.querySelector('button[type="submit"]');
    if (!submitBtn) return;

    const toggle = (id, ok) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.className = ok ? "text-success" : "text-danger";
    };

    const validatePassword = () => {
      const val = passwordInput.value;

      const checks = {
        len: val.length >= 8,
        upper: (val.match(/[A-Z]/g) || []).length === 1,
        lower: /[a-z]/.test(val),
        number: /[0-9]/.test(val),
        special: (val.match(/[^A-Za-z0-9]/g) || []).length === 1,
      };

      Object.entries(checks).forEach(([id, ok]) => toggle(id, ok));

      criteriaBox.style.display = "block";
      return Object.values(checks).every(Boolean);
    };

    const validateConfirm = () => {
      const match =
        passwordInput.value && passwordInput.value === confirmInput.value;
      mismatchText.style.display = match ? "none" : "block";
      return match;
    };

    const toggleSubmit = () => {
      submitBtn.disabled = !(validatePassword() && validateConfirm());
    };

    passwordInput.addEventListener("keyup", toggleSubmit);
    confirmInput.addEventListener("keyup", toggleSubmit);

    submitBtn.disabled = true;

    // 👁 toggle password visibility
    toggleBtn?.addEventListener("click", () => {
      const icon = toggleBtn.querySelector("i");
      passwordInput.type =
        passwordInput.type === "password" ? "text" : "password";
      icon?.classList.toggle("bi-eye");
      icon?.classList.toggle("bi-eye-slash");
    });
  })();

  /* =====================================================
     2 EMAIL PREFIX → FINAL EMAIL
  ===================================================== */
  (() => {
    const prefixInput = document.getElementById("emailPrefix");
    const finalEmail = document.getElementById("finalEmail");
    if (!prefixInput || !finalEmail) return;

    const domain = "@cgstranchizone.gov.in";

    prefixInput.addEventListener("input", () => {
      prefixInput.value = prefixInput.value
        .toLowerCase()
        .replace(/[^a-z0-9]/g, "");

      finalEmail.value = prefixInput.value + domain;
    });
  })();

  /* =====================================================
     43 DOMAIN PATH (patna1 rule)
     → lowercase letters + only ONE digit at end
  ===================================================== */
  (() => {
    const input = document.getElementById("domainPath");
    const hidden = document.getElementById("finaldomainPath");
    if (!input) return;

    input.addEventListener("input", () => {
      let value = input.value.toLowerCase().replace(/[^a-z0-9]/g, "");

      const letters = value.replace(/[0-9]/g, "");
      const digit = value.match(/[0-9]/);

      const cleanValue = letters + (digit ? digit[0] : "");
      input.value = cleanValue;

      if (hidden) {
        hidden.value = "https://" + cleanValue + ".cgstranchizone.gov.in";
      }
    });
  })();
});
