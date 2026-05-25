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

export type Supplier = {
  id: number;
  name: string;
  phone: string | null;
  location: string | null;
};

export type PackageStatus = 'unsorted' | 'sorted' | 'processing' | 'complete';

export type InventoryPackage = {
  id: number;
  supplier_id: number;
  supplier?: Supplier;
  reference: string | null;
  name: string;
  total_items: number;
  status: PackageStatus;
  received_at: string | null;
  sorted_at: string | null;
  notes: string | null;
};

export type Season = 'summer' | 'winter' | 'spring' | 'all_seasons';
export type Gender = 'men' | 'women' | 'boys' | 'girls';
export type ItemType = 'pants' | 'shorts' | 'shirt' | 'skirt' | 'jacket' | 'dress' | 'other';
export type ItemConditionValue = 'perfect' | 'good' | 'normal' | 'zero';
export type PriceTier = 'N3' | 'N2' | 'N1' | 'zero';

export type ItemCondition = {
  id: number;
  condition: ItemConditionValue;
  price_tier: PriceTier;
  quantity: number;
};

export type InventoryItem = {
  id: number;
  package_id: number;
  package?: InventoryPackage;
  sku: string | null;
  name: string;
  season: Season;
  gender: Gender;
  type: ItemType;
  quantity: number;
  notes: string | null;
  conditions: ItemCondition[];
};

export type CreateSupplierPayload = {
  name: string;
  phone?: string;
  location?: string;
};

export type CreatePackagePayload = {
  supplier_id: number;
  reference?: string;
  name: string;
  total_items: number;
  received_at: string;
  notes?: string;
};

export type UpdateItemConditionPayload = {
  conditions: Array<{
    condition: ItemConditionValue;
    price_tier: PriceTier;
    quantity: number;
  }>;
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

export async function fetchSuppliers(): Promise<Supplier[]> {
  const response = await api.get<ApiEnvelope<Supplier[]>>('/suppliers');

  return response.data.data;
}

export async function createSupplier(payload: CreateSupplierPayload): Promise<Supplier> {
  const response = await api.post<ApiEnvelope<Supplier>>('/suppliers', payload);

  return response.data.data;
}

export async function fetchPackages(): Promise<InventoryPackage[]> {
  const response = await api.get<ApiEnvelope<InventoryPackage[]>>('/packages');

  return response.data.data;
}

export async function createPackage(payload: CreatePackagePayload): Promise<InventoryPackage> {
  const response = await api.post<ApiEnvelope<InventoryPackage>>('/packages', payload);

  return response.data.data;
}

export async function fetchItems(): Promise<InventoryItem[]> {
  const response = await api.get<ApiEnvelope<InventoryItem[]>>('/items');

  return response.data.data;
}

export async function updateItemConditions(
  itemId: number,
  payload: UpdateItemConditionPayload,
): Promise<InventoryItem> {
  const response = await api.patch<ApiEnvelope<InventoryItem>>(`/items/${itemId}`, payload);

  return response.data.data;
}
