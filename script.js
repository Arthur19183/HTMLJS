const header = document.querySelector(".site-header");
const nav = document.querySelector("#site-nav");
const toggle = document.querySelector(".nav-toggle");
const year = document.querySelector("#year");
const form = document.querySelector("#contact-form");
const formNote = document.querySelector("#form-note");
const privacyModal = document.querySelector("#privacy-modal");
const acceptPrivacy = document.querySelector("#accept-privacy");
const openPrivacyButtons = document.querySelectorAll("[data-open-privacy]");
const closePrivacyButton = document.querySelector("[data-close-privacy]");
const privacyStorageKey = "datenschutz-zur-kenntnis-genommen";

function hasPrivacyAcknowledgement() {
  try {
    return sessionStorage.getItem(privacyStorageKey) === "yes";
  } catch {
    return false;
  }
}

function openPrivacy({ required = false } = {}) {
  if (!privacyModal) return;
  privacyModal.classList.add("is-open");
  document.body.classList.toggle("privacy-locked", required);
  privacyModal.querySelector(".privacy-content")?.scrollTo(0, 0);
  window.setTimeout(() => acceptPrivacy?.focus(), 0);
}

function closePrivacy() {
  if (!privacyModal || document.body.classList.contains("privacy-locked")) return;
  privacyModal.classList.remove("is-open");
}

if (hasPrivacyAcknowledgement()) {
  document.body.classList.remove("privacy-locked");
} else {
  openPrivacy({ required: true });
}

acceptPrivacy?.addEventListener("click", () => {
  try {
    sessionStorage.setItem(privacyStorageKey, "yes");
  } catch {
    // Die Seite bleibt auch verfügbar, wenn Session Storage blockiert ist.
  }
  document.body.classList.remove("privacy-locked");
  privacyModal?.classList.remove("is-open");
});

openPrivacyButtons.forEach((button) => {
  button.addEventListener("click", () => openPrivacy());
});

closePrivacyButton?.addEventListener("click", closePrivacy);

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") closePrivacy();
});

if (year) {
  year.textContent = String(new Date().getFullYear());
}

window.addEventListener(
  "scroll",
  () => {
    header?.classList.toggle("is-scrolled", window.scrollY > 12);
  },
  { passive: true }
);

toggle?.addEventListener("click", () => {
  const open = nav?.classList.toggle("is-open");
  toggle.setAttribute("aria-expanded", open ? "true" : "false");
  toggle.setAttribute("aria-label", open ? "Menü schliessen" : "Menü öffnen");
});

nav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    nav.classList.remove("is-open");
    toggle?.setAttribute("aria-expanded", "false");
    toggle?.setAttribute("aria-label", "Menü öffnen");
  });
});

const revealEls = document.querySelectorAll(".reveal");
if ("IntersectionObserver" in window) {
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          io.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: "0px 0px -8% 0px" }
  );
  revealEls.forEach((el) => io.observe(el));
} else {
  revealEls.forEach((el) => el.classList.add("is-visible"));
}

form?.addEventListener("submit", (event) => {
  event.preventDefault();

  const data = new FormData(form);
  const privacyConfirmed = data.get("datenschutz_bestaetigt") === "Ja";

  if (!privacyConfirmed) {
    formNote.textContent = "Bitte die Datenschutzerklärung bestätigen.";
    return;
  }

  const subject = encodeURIComponent("Anfrage über Website");
  formNote.textContent =
    "Ihr E-Mail-Programm öffnet sich. Bitte die Nachricht dort absenden.";
  window.location.href = `mailto:kontakt@sap-fico-beratung.ch?subject=${subject}`;
});
