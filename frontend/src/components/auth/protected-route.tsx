'use client';

import Link from 'next/link';
import { type ReactNode, useEffect } from 'react';

import { useAuthStore } from '../../stores/auth-store';

type ProtectedRouteProps = {
  children: ReactNode;
};

export function ProtectedRoute({ children }: ProtectedRouteProps) {
  const { hydrate, isHydrated, isLoading, token } = useAuthStore();

  useEffect(() => {
    if (!isHydrated) {
      void hydrate();
    }
  }, [hydrate, isHydrated]);

  if (!isHydrated || isLoading) {
    return <div className="panel">Loading workspace...</div>;
  }

  if (!token) {
    return (
      <div className="panel">
        <h2>Login required</h2>
        <p>Your StockFlow workspace is protected.</p>
        <Link className="button" href="/login">
          Go to login
        </Link>
      </div>
    );
  }

  return <>{children}</>;
}
