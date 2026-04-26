import './App.css';
import AuthCard from './components/AuthCard';
import ProductFormCard from './components/ProductFormCard';
import ProductTableCard from './components/ProductTableCard';
import useAuth from './hooks/useAuth';
import useProducts from './hooks/useProduct';

function App() {
  const { authMode, isAuthenticated, authForm, user, authSubmitting, authError, handleAuthChange, handleAuthSubmit, getAuthHeaders, handleToggleAuthMode, handleLogout } = useAuth()
  const { products, loading, form, editingId, submitting, productError, handleDelete, handleProductChange, loadProducts, handleProductSubmit, startEdit, resetForm } = useProducts({ isAuthenticated, getAuthHeaders })

return (
  <main className='container'>
    <section className='header-card'>
      <p className='label'>FE + BE</p>
      <h1>Manajemen Produk</h1>
      <p className='subtext'>CRUD API</p>

      <div className='auth-summary'>
        {user ? (
         <>
           <p>Hello, {user.name}!</p>
          <button type='button' onClick={handleLogout}>Log Out</button>
         </>
        ) : (
          <p>Please log in to manage products.</p>
        )}
      </div>
    </section>

    <section className="grid-layout">
        <AuthCard
          authMode={authMode}
          authForm={authForm}
          authSubmitting={authSubmitting}
          error={authError}
          onChange={handleAuthChange}
          onSubmit={handleAuthSubmit}
          onToggleMode={handleToggleAuthMode}
        />

        <ProductFormCard
          editingId={editingId}
          form={form}
          submitting={submitting}
          canManage={isAuthenticated}
          error={productError}
          onChange={handleProductChange}
          onSubmit={handleProductSubmit}
          onReset={resetForm}
        />

        <ProductTableCard
          loading={loading}
          products={products}
          onEdit={startEdit}
          onDelete={handleDelete}
          onRefresh={loadProducts}
          canManage={isAuthenticated}
        />
    </section>
  </main>
)
}

export default App;