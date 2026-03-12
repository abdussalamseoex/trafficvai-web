<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('currency', {
            current: localStorage.getItem('selected_currency') || 'USD',
            rates: {
                USD: { symbol: '$', rate: 1 },
                BDT: { symbol: '৳', rate: 120 },
                EUR: { symbol: '€', rate: 0.95 }
            },
            init() {
                this.updatePrices();
                
                // Watch for external currency changes (e.g. from other tabs or components)
                window.addEventListener('currency-changed', (e) => {
                    if (this.current !== e.detail.currency) {
                        this.current = e.detail.currency;
                        localStorage.setItem('selected_currency', this.current);
                        this.updatePrices();
                    }
                });

                // Observe DOM for dynamically loaded prices (e.g. modals, ajax)
                const observer = new MutationObserver((mutations) => {
                    let shouldUpdate = false;
                    for (let mutation of mutations) {
                        if (mutation.addedNodes.length) {
                            mutation.addedNodes.forEach(node => {
                                if (node.nodeType === 1) { // Element node
                                    if (node.classList && node.classList.contains('price-convert')) {
                                        shouldUpdate = true;
                                    } else if (node.querySelectorAll && node.querySelectorAll('.price-convert').length > 0) {
                                        shouldUpdate = true;
                                    }
                                }
                            });
                        }
                    }
                    if (shouldUpdate) {
                        this.updatePrices();
                    }
                });
                
                observer.observe(document.body, { childList: true, subtree: true });
            },
            setCurrency(newCurrency) {
                this.current = newCurrency;
                localStorage.setItem('selected_currency', newCurrency);
                this.updatePrices();
                window.dispatchEvent(new CustomEvent('currency-changed', { detail: { currency: newCurrency } }));
            },
            updatePrices() {
                const targetCurrency = this.rates[this.current];
                document.querySelectorAll('.price-convert').forEach(el => {
                    // Skip if element has x-text, as Alpine handles it
                    if (el.hasAttribute('x-text')) return;
                    
                    const basePrice = parseFloat(el.dataset.basePrice);
                    if (!isNaN(basePrice)) {
                        const convertedPrice = basePrice * targetCurrency.rate;
                        let formattedPrice = convertedPrice.toFixed(2);
                        if (formattedPrice.endsWith('.00')) {
                            formattedPrice = convertedPrice.toFixed(0);
                        }
                        
                        if (el.dataset.format === 'number-only') {
                            el.innerText = formattedPrice;
                        } else {
                            // Default format: Symbol + Price
                            el.innerText = targetCurrency.symbol + formattedPrice;
                        }
                    }
                });
            },
            convert(amount) {
                return amount * this.rates[this.current].rate;
            },
            format(amount) {
                const converted = this.convert(amount);
                let formattedSize = converted.toFixed(2);
                if (formattedSize.endsWith('.00')) {
                    formattedSize = converted.toFixed(0);
                }
                return this.rates[this.current].symbol + formattedSize;
            }
        });
    });
</script>
