// Navbar toggle for mobile
function toggleNav() {
    document.getElementById("togglenav").classList.toggle("show");
}


// Dropdown menu toggle
function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}

// Slider functionality
const slider = document.querySelector('.slider');
const slides = document.querySelectorAll('.slide');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');
let currentIndex = 0;

function showSlide(index) {
    if (index >= slides.length) {
        currentIndex = 0;
    } else if (index < 0) {
        currentIndex = slides.length - 1;
    } else {
        currentIndex = index;
    }
    const offset = -currentIndex * 100;
    slider.style.transform = `translateX(${offset}%)`;
}

nextButton.addEventListener('click', () => {
    showSlide(currentIndex + 1);
});

prevButton.addEventListener('click', () => {
    showSlide(currentIndex - 1);
});

setInterval(() => {
    showSlide(currentIndex + 1);
}, 4500);
// JavaScript to hide dropdown and toggle nav on scroll down
let lastScrollTop = 0;
const dropdownMenu = document.querySelector('.dropdown-menu');
const navbarNav = document.querySelector('.navbar_nav');

window.addEventListener('scroll', () => {
    let scrollTop = window.scrollY;

    if (scrollTop > lastScrollTop) {
        // Scroll down: hide dropdown menu and navbar navigation
        if (dropdownMenu) dropdownMenu.classList.remove('show');
        if (navbarNav) navbarNav.classList.remove('show');
    }

    lastScrollTop = scrollTop;
});
