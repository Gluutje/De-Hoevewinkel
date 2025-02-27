<?php if (!empty($categories)): ?>
    <!-- Categorieën tabs -->
    <div class="category-tabs">
        <?php foreach ($categories as $categoryName => $products): ?>
            <button class="category-tab" data-category="<?php echo htmlspecialchars($categoryName); ?>">
                <?php echo htmlspecialchars($categoryName); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Product secties per categorie -->
    <?php foreach ($categories as $categoryName => $products): ?>
        <div class="product-section" data-category="<?php echo htmlspecialchars($categoryName); ?>">
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                        <div class="product-details">
                            <span class="product-price">
                                €<?php echo number_format($product['price'], 2); ?>
                            </span>
                            <span class="product-unit">
                                per <?php echo htmlspecialchars($product['unit']); ?>
                            </span>
                        </div>
                        <button class="buy-button" data-product-id="<?php echo $product['product_id']; ?>">
                            Kopen
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toon eerste categorie
        const categories = document.querySelectorAll('.product-section');
        const tabs = document.querySelectorAll('.category-tab');
        
        if (categories.length > 0 && tabs.length > 0) {
            categories[0].classList.add('active');
            tabs[0].classList.add('active');
        }

        // Categorie tabs functionaliteit
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Update actieve tab
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Update zichtbare producten
                categories.forEach(section => {
                    if (section.dataset.category === category) {
                        section.classList.add('active');
                    } else {
                        section.classList.remove('active');
                    }
                });
            });
        });

        // Koop knop functionaliteit
        document.querySelectorAll('.buy-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                window.location.href = '?route=product/buy&id=' + productId;
            });
        });
    });
    </script>
<?php else: ?>
    <div class="no-products">
        <p>Er zijn momenteel geen producten beschikbaar.</p>
    </div>
<?php endif; ?> 