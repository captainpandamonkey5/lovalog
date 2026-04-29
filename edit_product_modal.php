<?php if ($is_authenticated): ?>
    <div class="modal fade" id="editProductModal" tabindex="-1"
        aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 24px 64px rgba(0,0,0,0.12);
            overflow: hidden;
        ">
                <div class="modal-header" style="
                background: var(--surface-2);
                border-bottom: 1px solid var(--border);
                padding: 20px 24px;
            ">
                    <div>
                        <h5 class="modal-title mb-0" id="editProductModalLabel" style="
                        font-family: var(--font-display);
                        font-weight: 700;
                        font-size: 1.15rem;
                        color: var(--text-primary);
                    ">Edit Product</h5>
                        <p style="font-size:0.78rem; color:var(--text-muted); margin:3px 0 0;">
                            Update the product details below.
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding: 24px;">
                    <form action="update.php" method="POST" id="editProductForm">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-4">
                            <label for="edit_product_name" class="form-label" style="
                            font-size: 0.78rem; font-weight: 600;
                            text-transform: uppercase; letter-spacing: 0.07em;
                            color: var(--text-muted); margin-bottom: 6px;
                        ">Product Name</label>
                            <input type="text" class="form-control modal-input"
                                id="edit_product_name" name="product_name"
                                required autocomplete="off">
                        </div>

                        <div class="mb-4">
                            <label for="edit_product_category" class="form-label" style="
                            font-size: 0.78rem; font-weight: 600;
                            text-transform: uppercase; letter-spacing: 0.07em;
                            color: var(--text-muted); margin-bottom: 6px;
                        ">Category</label>
                            <div class="select-wrap-modal">
                                <select class="form-control modal-input"
                                    id="edit_product_category"
                                    name="product_category" required>
                                    <option value="" disabled selected>Select a category…</option>
                                    <option value="Beverages">🥤 Beverages</option>
                                    <option value="Dairy">🥛 Dairy</option>
                                    <option value="Snacks">🍿 Snacks</option>
                                    <option value="Alcohol">🍺 Alcohol</option>
                                    <option value="Vegetables">🥦 Vegetables</option>
                                    <option value="Fruits">🍎 Fruits</option>
                                    <option value="Baby Care">🍼 Baby Care</option>
                                    <option value="Frozen Food">🧊 Frozen Food</option>
                                    <option value="Condiments">🫙 Condiments</option>
                                    <option value="Canned Goods">🥫 Canned Goods</option>
                                    <option value="Ready to Eat">🍱 Ready to Eat</option>
                                    <option value="Cigarettes">🚬 Cigarettes</option>
                                    <option value="Detergents">🧴 Detergents</option>
                                    <option value="Others">📋 Others</option>
                                    <option value="Uncategorized">❓ Uncategorized</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label for="edit_product_price" class="form-label" style="
                                font-size: 0.78rem; font-weight: 600;
                                text-transform: uppercase; letter-spacing: 0.07em;
                                color: var(--text-muted); margin-bottom: 6px;
                            ">Price (₱)</label>
                                <input type="number" class="form-control modal-input"
                                    id="edit_product_price" name="product_price"
                                    step="0.01" min="0" required>
                            </div>
                            <div class="col-6">
                                <label for="edit_quantity" class="form-label" style="
                                font-size: 0.78rem; font-weight: 600;
                                text-transform: uppercase; letter-spacing: 0.07em;
                                color: var(--text-muted); margin-bottom: 6px;
                            ">Quantity</label>
                                <input type="number" class="form-control modal-input"
                                    id="edit_quantity" name="quantity"
                                    min="0" required>
                            </div>
                        </div>

                        <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:4px;">
                            <button type="button" class="btn" data-bs-dismiss="modal" style="
                            font-size: 0.875rem; font-weight: 600;
                            padding: 9px 20px; border-radius: var(--radius);
                            background: var(--surface-2); border: 1px solid var(--border);
                            color: var(--text-muted);
                        ">Cancel</button>
                            <button type="submit" class="btn-add">Update Product</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>