import Link from 'next/link';

export default function HomePage() {
  return (
    <div className="app-shell">
      <header className="topbar">
        <div className="brand">StockFlow</div>
        <nav className="nav-actions" aria-label="Primary">
          <Link className="button secondary" href="/login">
            Login
          </Link>
          <Link className="button" href="/dashboard">
            Dashboard
          </Link>
        </nav>
      </header>
      <main className="main-surface">
        <section className="auth-layout">
          <div className="auth-copy">
            <h1>Clothing inventory, sorting, and invoices in one place.</h1>
            <p>
              Receive supplier packages, classify stock, manage customers, and
              create invoices from a single operational workspace.
            </p>
          </div>
          <div className="panel">
            <h2>Workspace status</h2>
            <p>Backend APIs are ready through dashboard reporting.</p>
            <Link className="button" href="/login">
              Continue to login
            </Link>
          </div>
        </section>
      </main>
    </div>
  );
}
