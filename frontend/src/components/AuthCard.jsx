function AuthCard({authMode, authForm, authSubmitting, error, onChange, onSubmit, onToggleMode}) {
    return (
        <form className="form-card" onSubmit={onSubmit}>
            <h2>{authMode === 'login' ? 'Login Admin' : 'Register Admin'}</h2>

            {authMode === 'register' && (
                <label>
                    Nama
                    <input 
                        name="name"
                        value={authForm.name}
                        onChange={onChange}
                        required
                        placeholder="Nama Lengkap"/>
                </label>
            )}

            <label>
                Email
                <input 
                    name="email"
                    type="email"
                    value={authForm.email}
                    onChange={onChange}
                    required
                    placeholder="admin@mail.com"/>
            </label>

            <label>
                Password
                <input 
                    name="password"
                    type="password"
                    value={authForm.password}
                    onChange={onChange}
                    required
                    minLength={8}
                    placeholder="Minimal 8 karakter"/>
            </label>

            <div className="actions">
                <button type="submit" disabled={authSubmitting}>
                    {authSubmitting ? 'Proses...' : authMode === 'login' ? 'login' : 'register'}
                </button>
                <button type="button" className="ghost" onClick={onToggleMode}>
                    {authMode === 'login' ? 'Pindah ke Register' : 'Pindah ke Login'}
                </button>
            </div>

            {error && <p className="error-msg">{error}</p>}
        </form>
    );
}

export default AuthCard;