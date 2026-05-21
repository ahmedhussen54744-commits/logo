// ==================== Slider ====================
const slides = document.querySelectorAll('.slide');
const sliderDotsContainer = document.getElementById('sliderDots');
const prevBtn = document.getElementById('sliderPrev');
const nextBtn = document.getElementById('sliderNext');
let currentSlide = 0;
let slideInterval;

// Create dots
slides.forEach((_, index) => {
    const dot = document.createElement('span');
    dot.classList.add('dot');
    if (index === 0) dot.classList.add('active');
    dot.addEventListener('click', () => goToSlide(index));
    sliderDotsContainer.appendChild(dot);
});

function goToSlide(index) {
    slides[currentSlide].classList.remove('active');
    document.querySelectorAll('.slider-dots .dot')[currentSlide].classList.remove('active');
    currentSlide = index;
    slides[currentSlide].classList.add('active');
    document.querySelectorAll('.slider-dots .dot')[currentSlide].classList.add('active');
}

function nextSlide() {
    const next = (currentSlide + 1) % slides.length;
    goToSlide(next);
}

function prevSlide() {
    const prev = (currentSlide - 1 + slides.length) % slides.length;
    goToSlide(prev);
}

// Auto play
function startSlider() {
    slideInterval = setInterval(nextSlide, 5000);
}

function stopSlider() {
    clearInterval(slideInterval);
}

prevBtn.addEventListener('click', () => {
    stopSlider();
    prevSlide();
    startSlider();
});

nextBtn.addEventListener('click', () => {
    stopSlider();
    nextSlide();
    startSlider();
});

startSlider();

// ==================== Mobile Menu ====================
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navMenu = document.getElementById('navMenu');

mobileMenuBtn.addEventListener('click', () => {
    navMenu.classList.toggle('active');
});

// Close menu on click outside
document.addEventListener('click', (e) => {
    if (!navMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
        navMenu.classList.remove('active');
    }
});

// ==================== Font Size Controls ====================
const fontIncrease = document.getElementById('fontIncrease');
const fontDecrease = document.getElementById('fontDecrease');
const fontReset = document.getElementById('fontReset');
let currentFontSize = 100;

fontIncrease.addEventListener('click', () => {
    if (currentFontSize < 130) {
        currentFontSize += 10;
        document.body.style.fontSize = currentFontSize + '%';
    }
});

fontDecrease.addEventListener('click', () => {
    if (currentFontSize > 80) {
        currentFontSize -= 10;
        document.body.style.fontSize = currentFontSize + '%';
    }
});

fontReset.addEventListener('click', () => {
    currentFontSize = 100;
    document.body.style.fontSize = '100%';
});

// ==================== Statistics Counter Animation ====================
function animateCounters() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-target'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += step;
            if (current < target) {
                stat.textContent = toBanglaNumber(Math.floor(current));
                requestAnimationFrame(updateCounter);
            } else {
                stat.textContent = toBanglaNumber(target);
            }
        };
        
        updateCounter();
    });
}

// Convert to Bangla numbers
function toBanglaNumber(num) {
    const banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return num.toString().replace(/\d/g, d => banglaDigits[d]).replace(/(\d)(?=(\d{2})+(?!\d))/g, '$1,');
}

// Intersection Observer for counter animation
const statsSection = document.querySelector('.statistics');
let countersAnimated = false;

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !countersAnimated) {
            countersAnimated = true;
            animateCounters();
        }
    });
}, { threshold: 0.5 });

if (statsSection) {
    observer.observe(statsSection);
}

// ==================== Smooth Scroll ====================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
});

// ==================== Marquee Notice (optional scroll effect) ====================
const noticeList = document.querySelector('.notice-list');
if (noticeList) {
    // Add hover pause effect
    noticeList.addEventListener('mouseenter', () => {
        noticeList.style.animationPlayState = 'paused';
    });
    noticeList.addEventListener('mouseleave', () => {
        noticeList.style.animationPlayState = 'running';
    });
}

// ==================== Back to Top ====================
const backToTop = document.createElement('button');
backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
backToTop.classList.add('back-to-top');
backToTop.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 45px;
    height: 45px;
    background: #006a4e;
    color: #fff;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    transition: 0.3s;
    z-index: 9999;
`;

document.body.appendChild(backToTop);

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTop.style.display = 'flex';
    } else {
        backToTop.style.display = 'none';
    }
});

backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
