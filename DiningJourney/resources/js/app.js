//
const menuToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');
const searchType = document.querySelector('input[name="type"]');

menuToggle?.addEventListener('click', () => {
    const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!expanded));
    navLinks?.classList.toggle('open', !expanded);
});

document.querySelectorAll('.search-tab').forEach((tab) => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.search-tab').forEach((item) => item.classList.remove('active'));
        tab.classList.add('active');
        if (searchType) {
            searchType.value = tab.dataset.category;
        }
    });
});
