<?php if ($is_authenticated): ?>
    <div class="modal fade" id="addProductModal" tabindex="-1"
        aria-labelledby="addProductModalLabel" aria-hidden="true">
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
                        <h5 class="modal-title mb-0" id="addProductModalLabel" style="
                        font-family: var(--font-display);
                        font-weight: 700;
                        font-size: 1.15rem;
                        color: var(--text-primary);
                    ">Add New Product</h5>
                        <p style="font-size:0.78rem; color:var(--text-muted); margin:3px 0 0;">
                            Fill in the details below to add a product to the catalog.
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding: 24px;">
                    <form action="create.php" method="POST" id="addProductForm">

                        <div class="mb-4">
                            <label for="modal_product_name" class="form-label" style="
                            font-size: 0.78rem; font-weight: 600;
                            text-transform: uppercase; letter-spacing: 0.07em;
                            color: var(--text-muted); margin-bottom: 6px;
                        ">Product Name</label>
                            <input type="text" class="form-control modal-input"
                                id="modal_product_name" name="product_name"
                                placeholder="e.g. Whole Milk 1L"
                                required autocomplete="off">
                        </div>

                        <div class="mb-4">
                            <label for="modal_product_category" class="form-label" style="
                            font-size: 0.78rem; font-weight: 600;
                            text-transform: uppercase; letter-spacing: 0.07em;
                            color: var(--text-muted); margin-bottom: 6px;
                        ">Category</label>
                            <div class="select-wrap-modal">
                                <select class="form-control modal-input"
                                    id="modal_product_category"
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
                                <label for="modal_product_price" class="form-label" style="
                                font-size: 0.78rem; font-weight: 600;
                                text-transform: uppercase; letter-spacing: 0.07em;
                                color: var(--text-muted); margin-bottom: 6px;
                            ">Price (₱)</label>
                                <input type="number" class="form-control modal-input"
                                    id="modal_product_price" name="product_price"
                                    placeholder="0.00" step="0.01" min="0" required>
                            </div>
                            <div class="col-6">
                                <label for="modal_quantity" class="form-label" style="
                                font-size: 0.78rem; font-weight: 600;
                                text-transform: uppercase; letter-spacing: 0.07em;
                                color: var(--text-muted); margin-bottom: 6px;
                            ">Quantity</label>
                                <input type="number" class="form-control modal-input"
                                    id="modal_quantity" name="quantity"
                                    placeholder="0" min="0" value="0" required>
                            </div>
                        </div>

                        <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:4px;">
                            <button type="button" class="btn" data-bs-dismiss="modal" style="
                            font-size: 0.875rem; font-weight: 600;
                            padding: 9px 20px; border-radius: var(--radius);
                            background: var(--surface-2); border: 1px solid var(--border);
                            color: var(--text-muted);
                        ">Cancel</button>
                            <button type="submit" class="btn-add">Add Product</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-input {
            font-family: var(--font-body);
            font-size: 0.9rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 10px 14px;
            color: var(--text-primary);
            background: #ffffff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
        }

        .modal-input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
        }

        .select-wrap-modal {
            position: relative;
        }

        .select-wrap-modal::after {
            content: '▾';
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            font-size: 14px;
        }

        .select-wrap-modal select {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
        }

        .modal-backdrop {
            z-index: 1029;
        }

        .modal {
            z-index: 1040;
        }
    </style>
<?php endif; ?>