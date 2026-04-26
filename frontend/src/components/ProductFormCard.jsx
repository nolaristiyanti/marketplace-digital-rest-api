function ProductFormCard({ 
    editingId, 
    form, 
    submitting,
    canManage,
    error,
    onChange,
    onSubmit,
    onReset,
}) {
    return (
        <form className="form-card" onSubmit={onSubmit}>
            <h2>{editingId ? 'Edit Product' : 'Register Product'}</h2>

            <label>
                Nama Produk
                <input 
                    name="name"
                    value={form.name}
                    onChange={onChange}
                    required
                    placeholder="Contoh: Keyboard Mechanical"
                    disabled={!canManage}/>
            </label>

            <label>
                Deskripsi
                <input 
                    name="description"
                    type="email"
                    value={form.description}
                    onChange={onChange}
                    rows="3"
                    placeholder="Deskripsi Singkat Product"
                    disabled={!canManage}/>
            </label>

            <label>
                Harga
                <input 
                    name="price"
                    type="number"
                    value={form.price}
                    min={0}
                    step={0.01}
                    onChange={onChange}
                    required
                    placeholder="0.00"
                    disabled={!canManage}/>
            </label>

            <label>
                Stok
                <input 
                    name="stock"
                    type="number"
                    value={form.stock}
                    min={1}
                    step={1}
                    onChange={onChange}
                    required
                    placeholder="0"
                    disabled={!canManage}/>
            </label>

            <div className="actions">
                <button type="submit" disabled={submitting || !canManage}>
                    {submitting ? 'Menyimpan...' : editingId ? 'Update' : 'Simpan'}
                </button>
                {editingId && (
                    <button type="button" className="ghost" onClick={onReset} disabled={!canManage}>
                        Batal
                    </button>
                )}
            </div>

            {error && <p className="error-msg">{error}</p>}
            {!canManage && <p className="error-msg">Anda tidak memiliki izin untuk mengelola produk ini.</p>    }
        </form>
    );
}

export default ProductFormCard;