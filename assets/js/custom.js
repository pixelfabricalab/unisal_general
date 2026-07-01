function isDesktop() {
  return window.innerWidth >= 768;
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementsByClassName('icon-search')[0].classList.add('fa-solid', 'fa-magnifying-glass');

  // Trasforma il menu accordion mobile
  if (!isDesktop()) {
    transformMobileAccordionMenu();
  }

  // Menu desktop - solo su schermi >= 768px
  if (isDesktop()) {
    // rimuove classe mod_list
    const mod_list = document.querySelector("ul.mod-menu.nav.navbar-nav");
    mod_list.classList.remove("mod-list");

    // aggiunge la classe alle ancore
    const anchors = document.querySelectorAll("ul.mod-menu li a");
    anchors.forEach((anchor) => {
      anchor.classList.add("nav-link");
    });

    // Seleziona l'elemento (es. con un id o un'altra classe)
    const elements = document.querySelectorAll(".nav-item");
    elements.forEach((elem) => {
      addDropdownClasses(elem);
    });

    // seleziona tutti gli anchor figli di .nav-item.dropdown
    const parent_anchors = document.querySelectorAll(".nav-item.dropdown > a");
    parent_anchors.forEach((anchor) => {
      transformParentAnchor(anchor);
    });

    // manipola le gli ul (tendina) per fargli avere le classi adatte
    const ul_children = document.querySelectorAll(".nav-item.dropdown ul");
    ul_children.forEach((ul_child) => {
      setDropdownClasses(ul_child);
    });
  }
});

function addDropdownClasses(element) {
  if (element.classList.contains("parent")) {
    element.classList.add("dropdown", "no-megamenu");
  }
}

function transformParentAnchor(element) {
  element.classList.add("nav-link", "dropdown-toggle");
  element.id = "navbarDropdown";
  element.setAttribute("role", "button");
  element.setAttribute("data-bs-toggle", "dropdown");
  element.setAttribute("aria-expanded", "false");
}

function setDropdownClasses(element) {
  // <ul class="dropdown-menu show" aria-labelledby="navbarDropdown" data-bs-popper="none">
  element.classList.add("dropdown-menu");
  element.setAttribute("aria-labelledby", "navbarDropdown");
  element.setAttribute("data-ps-popper", "none");
}

function transformMobileAccordionMenu() {
  const mobileMenu = document.querySelector(".menu-accordion ul.mod-menu");
  if (!mobileMenu) return;

  let collapseCounter = 0;
  const parentItems = mobileMenu.querySelectorAll("li.parent");

  parentItems.forEach((item) => {
    const link = item.querySelector(":scope > a");
    const submenu = item.querySelector(":scope > ul");

    if (link && submenu) {
      collapseCounter++;
      const collapseId = "accordion_collapse_" + collapseCounter;

      // Configura il link come toggle
      link.setAttribute("data-bs-toggle", "collapse");
      link.setAttribute("data-bs-target", "#" + collapseId);
      link.setAttribute("role", "button");
      link.setAttribute("aria-expanded", "false");
      link.setAttribute("aria-controls", collapseId);
      link.classList.add("dropdown-toggle");

      // Avvolgi il submenu in un collapse div
      const collapseDiv = document.createElement("div");
      collapseDiv.className = "collapse";
      collapseDiv.id = collapseId;
      collapseDiv.setAttribute("data-bs-parent", "#mobileMenuAccordion");

      // Sposta il submenu dentro il collapse
      submenu.parentNode.insertBefore(collapseDiv, submenu.nextSibling);
      collapseDiv.appendChild(submenu);

      // Inizializza Bootstrap Collapse
      if (typeof bootstrap !== 'undefined') {
        new bootstrap.Collapse(collapseDiv, {
          toggle: false
        });
      }

      // Aggiungi listener per aggiornare aria-expanded
      collapseDiv.addEventListener("show.bs.collapse", function () {
        link.setAttribute("aria-expanded", "true");
      });
      collapseDiv.addEventListener("hide.bs.collapse", function () {
        link.setAttribute("aria-expanded", "false");
      });
    }
  });
}

