document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("domain_path");
  const prefix = "https://";

  // Ensure prefix on page load
  if (!input.value.startsWith(prefix)) {
    input.value = prefix;
  }

  input.addEventListener("input", function () {
    // Always keep prefix
    if (!input.value.startsWith(prefix)) {
      input.value = prefix;
    }

    // Extract user-entered part
    let domainPart = input.value.slice(prefix.length);

    // Allow only lowercase letters and dots
    domainPart = domainPart.toLowerCase().replace(/[^a-z.]/g, "");

    input.value = prefix + domainPart;
  });

  // Prevent cursor from going before prefix
  input.addEventListener("keydown", function (e) {
    if (input.selectionStart < prefix.length) {
      input.setSelectionRange(prefix.length, prefix.length);
    }
  });

  input.addEventListener("click", function () {
    if (input.selectionStart < prefix.length) {
      input.setSelectionRange(prefix.length, prefix.length);
    }
  });
});
