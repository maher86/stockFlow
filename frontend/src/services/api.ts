import axios from 'axios';

export type ApiEnvelope<T> = {
  data: T;
};

export type User = {
  id: number;
  name: string;
  email: string;
};

export type LoginResponse = {
  token: string;
  user: User;
};

export type DashboardOverview = {
  suppliers_count: number;
  customers_count: number;
  packages_count: number;
  items_count: number;
  invoices_count: number;
  revenue_total: number;
  outstanding_total: number;
};

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL ?? '/api/v1',
  headers: {
    Accept: 'application/json',
  },
});

export function setAuthToken(token: string | null): void {
  if (token) {
    api.defaults.headers.common.Authorization = `Bearer ${token}`;
    return;
  }

  delete api.defaults.headers.common.Authorization;
}

export async function login(email: string, password: string): Promise<LoginResponse> {
  const response = await api.post<ApiEnvelope<LoginResponse>>('/auth/login', {
    email,
    password,
  });

  return response.data.data;
}

export async function logout(): Promise<void> {
  await api.post('/auth/logout');
}

export async function fetchMe(): Promise<User> {
  const response = await api.get<ApiEnvelope<User>>('/auth/me');

  return response.data.data;
}

export async function fetchDashboardOverview(): Promise<DashboardOverview> {
  const response = await api.get<ApiEnvelope<DashboardOverview>>('/dashboard/overview');

  return response.data.data;
}
