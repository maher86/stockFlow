import Link from 'next/link';

import { LoginForm } from '../../components/auth/login-form';

export default function LoginPage() {
  return (
    <div className="app-shell">
      <header className="topbar">
        <Link className="brand" href="/">
          StockFlow
        </Link>
      </header>
      <main className="main-surface">
        <section className="auth-layout">
          <div className="auth-copy">
            <h1>Sign in to manage stock movement.</h1>
            <p>
              Access supplier packages, item sorting, customer invoices, and
              dashboard reporting with your StockFlow account.
            </p>
          </div>
          <div className="panel">
            <h2>Login</h2>
            <LoginForm />
            <p className="helper-text">
              Local admin: admin@stockflow.test / password
            </p>
          </div>
        </section>
      </main>
    </div>
  );
}
