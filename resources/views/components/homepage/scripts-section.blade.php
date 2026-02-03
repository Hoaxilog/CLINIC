<script>
    function nextPage() {
        const container = document.getElementById('stack-container');
        const cards = container.querySelectorAll('.stack-card');
        const topCard = cards[cards.length - 1];
        topCard.style.transform = 'translateX(110%)';
        topCard.style.opacity = '0';
        setTimeout(() => {
            container.prepend(topCard);
            topCard.style.transform = 'translateX(0)';
            topCard.style.opacity = '1';
            const currentCards = container.querySelectorAll('.stack-card');
            currentCards.forEach((card, index) => {
                card.style.zIndex = (index + 1) * 10;
            });
        }, 500);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const menuPanel = document.getElementById('mobile-menu-panel');
        const menuBackdrop = document.getElementById('mobile-menu-backdrop');
        const menuClose = document.getElementById('menu-close');
        function openMenu() {
            mobileMenu.classList.remove('hidden');
            mobileMenu.setAttribute('aria-hidden', 'false');
            menuBtn.setAttribute('aria-expanded', 'true');
            menuIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    menuBackdrop.classList.remove('opacity-0');
                    menuBackdrop.classList.add('opacity-100');
                    menuPanel.classList.remove('translate-x-full');
                });
            });
        }
        function closeMenu() {
            menuPanel.classList.add('translate-x-full');
            menuBackdrop.classList.remove('opacity-100');
            menuBackdrop.classList.add('opacity-0');
            menuBtn.setAttribute('aria-expanded', 'false');
            menuIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />`;
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
                mobileMenu.setAttribute('aria-hidden', 'true');
            }, 300);
        }
        menuBtn.addEventListener('click', function() {
            if (mobileMenu.classList.contains('hidden')) {
                openMenu();
            } else {
                closeMenu();
            }
        });
        menuBackdrop.addEventListener('click', closeMenu);
        menuClose.addEventListener('click', closeMenu);
    });
</script>