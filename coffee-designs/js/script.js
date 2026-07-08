// ============================================
// ROAST WORKS — front-end logic
// Talks to two PHP endpoints:
//   GET  /api/designs.php   -> gallery items (JSON)
//   POST /api/contact.php   -> contact form handler (JSON)
// ============================================

document.getElementById("year").textContent = new Date().getFullYear();

const ICONS = {
  branding: `<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><circle cx="24" cy="24" r="16"/><path d="M24 12v24M12 24h24"/></svg>`,
  packaging: `<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 16l16-8 16 8-16 8-16-8z"/><path d="M8 16v16l16 8 16-8V16"/><path d="M24 24v16"/></svg>`,
  illustration: `<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 38l6-14c2-5 6-8 8-16 2 8 6 11 8 16l6 14"/><path d="M14 30h20"/></svg>`,
  photography: `<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="14" width="36" height="24" rx="2"/><circle cx="24" cy="26" r="7"/><path d="M18 14l3-4h6l3 4"/></svg>`,
  default: `<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 22c0-6 4-10 10-10s10 4 10 10-4 8-10 8-10-2-10-8z"/><path d="M10 30h28l-2 6H12l-2-6z"/></svg>`
};

const gallery       = document.getElementById("gallery");
const filtersEl      = document.getElementById("filters");
const galleryEmpty  = document.getElementById("galleryEmpty");
const archiveCount  = document.getElementById("archiveCount");

let ALL_DESIGNS = [];
let activeFilter = "all";

async function loadDesigns() {
  try {
    const res = await fetch("/api/designs.php");
    if (!res.ok) throw new Error("Bad response " + res.status);
    const data = await res.json();
    ALL_DESIGNS = data.designs || [];
    buildFilters(ALL_DESIGNS);
    renderGallery();
  } catch (err) {
    console.error("Could not load designs:", err);
    archiveCount.textContent = "couldn't load the archive — is the API running?";
    gallery.innerHTML = "";
  }
}

function buildFilters(items) {
  const categories = ["all", ...new Set(items.map(i => i.category))];
  filtersEl.innerHTML = categories.map(cat => `
    <button class="filter-chip" data-cat="${cat}" role="tab" aria-pressed="${cat === "all"}">
      ${cat === "all" ? "All" : cat}
    </button>
  `).join("");

  filtersEl.querySelectorAll(".filter-chip").forEach(btn => {
    btn.addEventListener("click", () => {
      activeFilter = btn.dataset.cat;
      filtersEl.querySelectorAll(".filter-chip").forEach(b =>
        b.setAttribute("aria-pressed", b === btn ? "true" : "false")
      );
      renderGallery();
    });
  });
}

function renderGallery() {
  const items = activeFilter === "all"
    ? ALL_DESIGNS
    : ALL_DESIGNS.filter(i => i.category === activeFilter);

  archiveCount.textContent = `${items.length} piece${items.length === 1 ? "" : "s"} on file`;

  if (!items.length) {
    gallery.innerHTML = "";
    galleryEmpty.hidden = false;
    return;
  }
  galleryEmpty.hidden = true;

  gallery.innerHTML = items.map((item, i) => {
    const palette = `p${(i % 6) + 1}`;
    const icon = ICONS[item.category?.toLowerCase()] || ICONS.default;
    return `
      <article class="card ${palette}">
        <span class="card-num">${String(item.id).padStart(2, "0")}</span>
        <div class="card-icon">${icon}</div>
        <div class="card-body">
          <span class="card-tag">${item.category}</span>
          <h3 class="card-title">${escapeHtml(item.title)}</h3>
          <p class="card-desc">${escapeHtml(item.description)}</p>
        </div>
      </article>
    `;
  }).join("");
}

function escapeHtml(str = "") {
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

loadDesigns();

// ============================================
// CONTACT FORM -> /api/contact.php
// ============================================
const form       = document.getElementById("contactForm");
const submitBtn  = document.getElementById("submitBtn");
const formStatus = document.getElementById("formStatus");

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  formStatus.textContent = "";
  formStatus.className = "form-status";

  const payload = {
    name: form.name.value.trim(),
    email: form.email.value.trim(),
    message: form.message.value.trim()
  };

  submitBtn.disabled = true;
  submitBtn.textContent = "Sending…";

  try {
    const res = await fetch("/api/contact.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (!res.ok || !data.ok) {
      formStatus.textContent = data.error || "Something went wrong — try again.";
      formStatus.classList.add("err");
    } else {
      formStatus.textContent = data.message || "Sent — thank you!";
      formStatus.classList.add("ok");
      form.reset();
    }
  } catch (err) {
    formStatus.textContent = "Network error — check your connection and try again.";
    formStatus.classList.add("err");
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = "Send it →";
  }
});
