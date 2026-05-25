'use client';

import { useRouter } from 'next/navigation';
import { type FormEvent, useState } from 'react';

import { useAuthStore } from '../../stores/auth-store';

export function LoginForm() {
  const router = useRouter();
  const { error, isLoading, login: loginUser } = useAuthStore();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();

    await loginUser(email, password);

    if (!useAuthStore.getState().error) {
      router.push('/dashboard');
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <div className="form-field">
        <label htmlFor="email">Email</label>
        <input
          id="email"
          name="email"
          type="email"
          autoComplete="email"
          value={email}
          onChange={(event) => setEmail(event.target.value)}
          required
        />
      </div>
      <div className="form-field">
        <label htmlFor="password">Password</label>
        <input
          id="password"
          name="password"
          type="password"
          autoComplete="current-password"
          value={password}
          onChange={(event) => setPassword(event.target.value)}
          required
        />
      </div>
      {error ? <p className="error-text">{error}</p> : null}
      <button className="button" type="submit" disabled={isLoading}>
        {isLoading ? 'Signing in...' : 'Sign in'}
      </button>
    </form>
  );
}
