import Link from 'next/link';

import { ProtectedRoute } from '../../components/auth/protected-route';
import { DashboardOverview } from '../../components/dashboard/dashboard-overview';

export default function DashboardPage() {
  return (
    <div className="app-shell">
      <header className="topbar">
        <Link className="brand" href="/">
          StockFlow
        </Link>
        <nav className="nav-actions" aria-label="Workspace">
          <Link className="button secondary" href="/login">
            Login
          </Link>
        </nav>
      </header>
      <main className="main-surface">
        <ProtectedRoute>
          <DashboardOverview />
        </ProtectedRoute>
      </main>
    </div>
  );
}
