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
      <h2>{editingId ? "Edit Product" : "Register Product"}</h2>

      <label>
        Nama Produk
        <input
          name="title"
          value={form.title}
          onChange={onChange}
          required
          placeholder="Contoh: Keyboard Mechanical"
          disabled={!canManage}
        />
      </label>

      <label>
        Deskripsi
        <input
          name="description"
          value={form.description}
          onChange={onChange}
          placeholder="Deskripsi Singkat Produk"
          required
          disabled={!canManage}
        />
      </label>

      <label>
        ID Kategori
        <input
          name="category_id"
          type="number"
          min={1}
          step={1}
          value={form.category_id}
          onChange={onChange}
          required
          placeholder="Contoh: 1"
          disabled={!canManage}
        />
      </label>

      <label>
        Harga
        <input
          name="price"
          type="number"
          min={0}
          step={0.01}
          value={form.price}
          onChange={onChange}
            required
          placeholder="0.00"
          disabled={!canManage}
        />
      </label>

      <label>
        Rating
        <input
          name="rating"
          type="number"
          min={0}
          max={10}
          step={0.1}
          value={form.rating}
          onChange={onChange}
          required
          placeholder="0 - 10"
          disabled={!canManage}
        />
      </label>

      <label>
        File Path
        <input
          name="file_path"
          value={form.file_path}
          onChange={onChange}
          required
          placeholder="Contoh: files/dashboard-ui-kit.zip"
          disabled={!canManage}
        />
      </label>

      <label>
        Stok
        <input
          name="stock"
          type="number"
          min={0}
          step={1}
          value={form.stock}
          onChange={onChange}
          required
          placeholder="0"
          disabled={!canManage}
        />
      </label>

      <div className="actions">
        <button type="submit" disabled={submitting || !canManage}>
          {submitting
            ? "Menyimpan ..."
            : editingId
              ? "Update"
              : "Simpan"}
        </button>
        {editingId && (
          <button type="button" className="ghost" onClick={onReset} disabled={!canManage}>
            Batal
          </button>
        )}
      </div>

      {error && <p className="error-msg">{error}</p>}
      {!canManage && <p className="error-msg">Anda tidak memiliki izin untuk mengelola produk.</p>}
    </form>
  );
}

export default ProductFormCard;