import Link from 'next/link';

import { ProtectedRoute } from '../../components/auth/protected-route';
import { InventoryWorkspace } from '../../components/inventory/inventory-workspace';

export default function InventoryPage() {
  return (
    <div className="app-shell">
      <header className="topbar">
        <Link className="brand" href="/">
          StockFlow
        </Link>
        <nav className="nav-actions" aria-label="Workspace">
          <Link className="button secondary" href="/dashboard">
            Dashboard
          </Link>
          <Link className="button secondary" href="/login">
            Login
          </Link>
        </nav>
      </header>
      <main className="main-surface">
        <ProtectedRoute>
          <InventoryWorkspace />
        </ProtectedRoute>
      </main>
    </div>
  );
}
