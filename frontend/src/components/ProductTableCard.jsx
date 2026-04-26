function ProductFormCard({ 
    loading, 
    products, 
    onEdit, 
    onDelete, 
    onRefresh, 
    canManage, 
}) {
    return (
        <section className="table-card wide-card">
            <div className="table-head">
                <h2>Daftar Produk</h2>
                <button type="button" className="ghost" onClick={onRefresh}>
                    refresh
                </button>
            </div>

            {loading ? (
                <p>Memuat Data...</p> 
            ) : products.length === 0 ? (
                    <p>Belum ada produk yang terdaftar.</p>
            ) : (
                <div className="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {products.map(product => (
                                <tr key={product.id}>
                                    <td>
                                        <strong>{product.title}</strong>
                                        <span>{product.description || "-"}</span>
                                    </td>
                                    <td>Rp {Number(product.price).toLocaleString('id-ID')}</td>
                                    <td>{product.stock}</td>
                                    <td className="row-acrions">
                                        <button
                                            type="button"
                                            className="ghost"
                                            onClick={() => onEdit(product)}
                                            disabled={!canManage}>
                                            edit
                                        </button>
                                        <button
                                            type="button"
                                            className="danger"
                                            onClick={() => onDelete(product.id)}
                                            disabled={!canManage}>
                                            hapus
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </section>
    );
}

export default ProductFormCard;