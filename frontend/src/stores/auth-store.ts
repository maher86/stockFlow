'use client';

import { create } from 'zustand';

import { fetchMe, login, logout, setAuthToken, type User } from '../services/api';

const TOKEN_KEY = 'stockflow_token';

type AuthState = {
  token: string | null;
  user: User | null;
  isHydrated: boolean;
  isLoading: boolean;
  error: string | null;
  hydrate: () => Promise<void>;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
};

function readStoredToken(): string | null {
  if (typeof window === 'undefined') {
    return null;
  }

  return window.localStorage.getItem(TOKEN_KEY);
}

function storeToken(token: string | null): void {
  if (typeof window === 'undefined') {
    return;
  }

  if (token) {
    window.localStorage.setItem(TOKEN_KEY, token);
    return;
  }

  window.localStorage.removeItem(TOKEN_KEY);
}

export const useAuthStore = create<AuthState>((set) => ({
  token: null,
  user: null,
  isHydrated: false,
  isLoading: false,
  error: null,
  hydrate: async () => {
    const token = readStoredToken();

    if (!token) {
      set({ isHydrated: true, token: null, user: null });
      return;
    }

    setAuthToken(token);
    set({ isLoading: true, token });

    try {
      const user = await fetchMe();
      set({ isHydrated: true, isLoading: false, user, error: null });
    } catch {
      storeToken(null);
      setAuthToken(null);
      set({
        isHydrated: true,
        isLoading: false,
        token: null,
        user: null,
        error: null,
      });
    }
  },
  login: async (email: string, password: string) => {
    set({ isLoading: true, error: null });

    try {
      const session = await login(email, password);
      storeToken(session.token);
      setAuthToken(session.token);
      set({
        isLoading: false,
        token: session.token,
        user: session.user,
        error: null,
      });
    } catch {
      set({
        isLoading: false,
        token: null,
        user: null,
        error: 'Email or password is incorrect.',
      });
    }
  },
  logout: async () => {
    set({ isLoading: true });

    try {
      await logout();
    } finally {
      storeToken(null);
      setAuthToken(null);
      set({
        isLoading: false,
        token: null,
        user: null,
        error: null,
      });
    }
  },
}));
