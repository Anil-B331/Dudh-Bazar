// Dudhbazar Main JS

document.addEventListener('DOMContentLoaded', () => {
    // Add micro-animation class to elements on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    const cards = document.querySelectorAll('.product-card, .dashboard-card');
    cards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease-out';
        observer.observe(card);
    });

    // Auto-dismiss alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = 0;
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    });

    // AJAX Cart Logic
    const cartForms = document.querySelectorAll('.ajax-cart-form');
    cartForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            formData.append('ajax', '1');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            const actionUrl = form.getAttribute('action');

            // Determine if this is a circular button
            const isCircle = submitBtn.classList.contains('btn-circle');

            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update cart count
                    const cartCountEl = document.getElementById('cart-count');
                    if (cartCountEl) {
                        cartCountEl.textContent = data.cart_count;
                        // Small bounce animation on cart link
                        cartCountEl.parentElement.style.transform = 'scale(1.1)';
                        cartCountEl.parentElement.style.display = 'inline-block';
                        cartCountEl.parentElement.style.transition = 'transform 0.2s';
                        setTimeout(() => cartCountEl.parentElement.style.transform = 'scale(1)', 200);
                    }
                    showToast('🛒 Added to Cart');
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                submitBtn.innerHTML = '✔';
                if (!isCircle) submitBtn.innerHTML = 'Added ✔';
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 1500);
            });
        });
    });

    // Toast functionality
    function showToast(message) {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = message;
        
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
