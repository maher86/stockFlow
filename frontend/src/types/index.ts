// ============================================================
// StockFlow — Shared TypeScript Types
// All types mirror the backend domain model exactly.
// ============================================================

// ─── Enums ──────────────────────────────────────────────────────────────────

export type Season = 'summer' | 'winter' | 'spring' | 'all_seasons'
export type Gender = 'men' | 'women' | 'boys' | 'girls'
export type ItemType = 'pants' | 'shorts' | 'shirt' | 'skirt' | 'jacket' | 'dress' | 'other'
export type Condition = 'perfect' | 'good' | 'normal' | 'zero'
export type PriceTier = 'N3' | 'N2' | 'N1' | 'zero'
export type PackageStatus = 'unsorted' | 'sorted' | 'processing' | 'complete'
export type InvoiceStatus = 'draft' | 'pending' | 'partial' | 'paid' | 'cancelled'
export type CustomerType = 'reseller' | 'boutique' | 'wholesaler' | 'shop'

// ─── Base ────────────────────────────────────────────────────────────────────

export interface Timestamps {
  created_at: string
  updated_at: string
}

export interface PaginationMeta {
  total: number
  per_page: number
  current_page: number
  last_page: number
  from: number
  to: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

// ─── Auth ────────────────────────────────────────────────────────────────────

export interface User extends Timestamps {
  id: number
  name: string
  email: string
}

export interface AuthResponse {
  data: {
    user: User
    token: string
  }
}

// ─── Supplier ────────────────────────────────────────────────────────────────

export interface Supplier extends Timestamps {
  id: number
  name: string
  phone: string
  location: string
  notes: string | null
  shipments_count: number
  items_count: number
}

export interface CreateSupplierInput {
  name: string
  phone: string
  location: string
  notes?: string
}

export type UpdateSupplierInput = Partial<CreateSupplierInput>

// ─── Customer ────────────────────────────────────────────────────────────────

export interface Customer extends Timestamps {
  id: number
  name: string
  phone: string
  location: string
  type: CustomerType
  notes: string | null
  invoices_count: number
  total_purchased: number
}

export interface CreateCustomerInput {
  name: string
  phone: string
  location: string
  type: CustomerType
  notes?: string
}

export type UpdateCustomerInput = Partial<CreateCustomerInput>

// ─── Package (Shipment) ──────────────────────────────────────────────────────

export interface Package extends Timestamps {
  id: number
  reference: string
  supplier_id: number
  supplier?: Supplier
  status: PackageStatus
  total_items: number
  sorted_items: number
  notes: string | null
  received_at: string
}

export interface CreatePackageInput {
  reference: string
  supplier_id: number
  total_items: number
  notes?: string
  received_at: string
}

// ─── Item ────────────────────────────────────────────────────────────────────

export interface Item extends Timestamps {
  id: number
  package_id: number
  package?: Package
  sku: string
  season: Season
  gender: Gender
  type: ItemType
  condition: Condition
  price_tier: PriceTier
  price: number
  notes: string | null
  sold: boolean
  sold_at: string | null
}

export interface CreateItemInput {
  package_id: number
  season: Season
  gender: Gender
  type: ItemType
  condition: Condition
  price_tier: PriceTier
  price: number
  notes?: string
}

export interface BulkUpdateItemsInput {
  item_ids: number[]
  condition?: Condition
  price_tier?: PriceTier
  price?: number
}

// ─── Invoice ─────────────────────────────────────────────────────────────────

export interface InvoiceItem {
  id: number
  invoice_id: number
  description: string
  quantity: number
  condition: PriceTier
  unit_price: number
  total: number
}

export interface InvoiceNote {
  id: number
  invoice_id: number
  author: string
  content: string
  created_at: string
}

export interface InvoiceHistory {
  id: number
  invoice_id: number
  event: string
  created_at: string
}

export interface Invoice extends Timestamps {
  id: number
  number: string
  customer_id: number
  customer?: Customer
  status: InvoiceStatus
  issue_date: string
  due_date: string
  subtotal: number
  total: number
  notes_text: string | null
  items: InvoiceItem[]
  notes: InvoiceNote[]
  history: InvoiceHistory[]
}

export interface CreateInvoiceItemInput {
  description: string
  quantity: number
  condition: PriceTier
  unit_price: number
}

export interface CreateInvoiceInput {
  customer_id: number
  issue_date: string
  due_date: string
  status: InvoiceStatus
  items: CreateInvoiceItemInput[]
  notes_text?: string
}

export type UpdateInvoiceInput = Partial<Omit<CreateInvoiceInput, 'items'>> & {
  items?: CreateInvoiceItemInput[]
}

export interface UpdateInvoiceStatusInput {
  status: InvoiceStatus
}

export interface AddInvoiceNoteInput {
  content: string
}

// ─── Dashboard ───────────────────────────────────────────────────────────────

export interface DashboardOverview {
  total_stock: number
  sorted_items: number
  unsorted_items: number
  packages_this_month: number
  pipeline: {
    received: number
    sorted: number
    by_season: number
    by_gender: number
    by_type: number
    priced: number
  }
  by_season: Record<Season, number>
  by_gender: Record<Gender, number>
  by_condition: Record<Condition, number>
  recent_packages: Package[]
}

export interface MonthlyReport {
  month: string
  items_received: number
  items_sorted: number
  revenue: number
}

export interface ReportsData {
  monthly: MonthlyReport[]
  condition_distribution: Record<Condition, number>
  type_distribution: Record<ItemType, number>
  price_tiers: Record<PriceTier, { count: number; range: string }>
}
