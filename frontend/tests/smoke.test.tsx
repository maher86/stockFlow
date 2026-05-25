import React from 'react';
import { renderToString } from 'react-dom/server';
import { describe, expect, it, vi } from 'vitest';

import DashboardPage from '../src/app/dashboard/page';
import LoginPage from '../src/app/login/page';
import HomePage from '../src/app/page';

vi.mock('next/navigation', () => ({
  useRouter: () => ({
    push: vi.fn(),
  }),
}));

describe('HomePage', () => {
  it('renders without crashing', () => {
    const html = renderToString(<HomePage />);

    expect(html).toContain('StockFlow');
  });
});

describe('LoginPage', () => {
  it('renders the login form without crashing', () => {
    const html = renderToString(<LoginPage />);

    expect(html).toContain('Sign in to manage stock movement.');
    expect(html).toContain('Login');
  });
});

describe('DashboardPage', () => {
  it('renders the protected dashboard shell without crashing', () => {
    const html = renderToString(<DashboardPage />);

    expect(html).toContain('StockFlow');
    expect(html).toContain('Loading workspace');
  });
});
