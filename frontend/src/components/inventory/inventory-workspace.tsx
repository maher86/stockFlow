'use client';

import axios from 'axios';
import { type FormEvent, useEffect, useMemo, useState } from 'react';

import {
  createPackage,
  createSupplier,
  fetchItems,
  fetchPackages,
  fetchSuppliers,
  type InventoryItem,
  type InventoryPackage,
  type ItemConditionValue,
  type PriceTier,
  type Supplier,
  updateItemConditions,
} from '../../services/api';

const conditionOptions: ItemConditionValue[] = ['perfect', 'good', 'normal', 'zero'];
const priceTierOptions: PriceTier[] = ['N3', 'N2', 'N1', 'zero'];

type ItemConditionDraft = {
  condition: ItemConditionValue;
  price_tier: PriceTier;
  quantity: number;
};

function formatLabel(value: string): string {
  return value.replaceAll('_', ' ');
}

function getErrorMessage(error: unknown): string {
  if (axios.isAxiosError(error)) {
    const message = error.response?.data?.message;

    if (typeof message === 'string') {
      return message;
    }
  }

  return 'Something went wrong. Please try again.';
}

function getInitialCondition(item: InventoryItem): ItemConditionDraft {
  const current = item.conditions[0];

  return {
    condition: current?.condition ?? 'good',
    price_tier: current?.price_tier ?? 'N2',
    quantity: current?.quantity ?? item.quantity,
  };
}

export function InventoryWorkspace() {
  const [suppliers, setSuppliers] = useState<Supplier[]>([]);
  const [packages, setPackages] = useState<InventoryPackage[]>([]);
  const [items, setItems] = useState<InventoryItem[]>([]);
  const [conditionDrafts, setConditionDrafts] = useState<Record<number, ItemConditionDraft>>({});
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [message, setMessage] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  const packageOptions = useMemo(
    () => packages.map((shipment) => ({ id: shipment.id, name: shipment.name })),
    [packages],
  );

  async function loadInventory(): Promise<void> {
    setIsLoading(true);
    setError(null);

    try {
      const [supplierData, packageData, itemData] = await Promise.all([
        fetchSuppliers(),
        fetchPackages(),
        fetchItems(),
      ]);

      setSuppliers(supplierData);
      setPackages(packageData);
      setItems(itemData);
      setConditionDrafts(
        Object.fromEntries(itemData.map((item) => [item.id, getInitialCondition(item)])),
      );
    } catch (loadError) {
      setError(getErrorMessage(loadError));
    } finally {
      setIsLoading(false);
    }
  }

  useEffect(() => {
    void loadInventory();
  }, []);

  async function handleSupplierSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setIsSaving(true);
    setError(null);
    setMessage(null);

    const form = new FormData(event.currentTarget);

    try {
      await createSupplier({
        name: String(form.get('name') ?? ''),
        phone: String(form.get('phone') ?? ''),
        location: String(form.get('location') ?? ''),
      });

      event.currentTarget.reset();
      setMessage('Supplier created.');
      await loadInventory();
    } catch (supplierError) {
      setError(getErrorMessage(supplierError));
    } finally {
      setIsSaving(false);
    }
  }

  async function handlePackageSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setIsSaving(true);
    setError(null);
    setMessage(null);

    const form = new FormData(event.currentTarget);

    try {
      await createPackage({
        supplier_id: Number(form.get('supplier_id')),
        reference: String(form.get('reference') ?? ''),
        name: String(form.get('name') ?? ''),
        total_items: Number(form.get('total_items') ?? 0),
        received_at: String(form.get('received_at') ?? ''),
        notes: String(form.get('notes') ?? ''),
      });

      event.currentTarget.reset();
      setMessage('Package received.');
      await loadInventory();
    } catch (packageError) {
      setError(getErrorMessage(packageError));
    } finally {
      setIsSaving(false);
    }
  }

  async function handleConditionSubmit(item: InventoryItem): Promise<void> {
    setIsSaving(true);
    setError(null);
    setMessage(null);

    const draft = conditionDrafts[item.id] ?? getInitialCondition(item);

    try {
      await updateItemConditions(item.id, {
        conditions: [
          {
            condition: draft.condition,
            price_tier: draft.price_tier,
            quantity: draft.quantity,
          },
        ],
      });

      setMessage(`Updated ${item.name}.`);
      await loadInventory();
    } catch (conditionError) {
      setError(getErrorMessage(conditionError));
    } finally {
      setIsSaving(false);
    }
  }

  function updateConditionDraft(itemId: number, draft: Partial<ItemConditionDraft>): void {
    setConditionDrafts((current) => ({
      ...current,
      [itemId]: {
        ...(current[itemId] ?? {
          condition: 'good',
          price_tier: 'N2',
          quantity: 1,
        }),
        ...draft,
      },
    }));
  }

  if (isLoading) {
    return <p className="helper-text">Loading inventory workspace...</p>;
  }

  return (
    <section className="workspace-stack">
      <div className="workspace-header">
        <div>
          <p className="eyebrow">Inventory</p>
          <h1>Receive, sort, and price stock.</h1>
        </div>
        <button className="button secondary" type="button" onClick={() => void loadInventory()}>
          Refresh
        </button>
      </div>

      {message ? <p className="success-text">{message}</p> : null}
      {error ? <p className="error-text">{error}</p> : null}

      <div className="section-grid">
        <section className="panel">
          <h2>Suppliers</h2>
          <form className="compact-form" onSubmit={handleSupplierSubmit}>
            <label>
              Name
              <input name="name" required />
            </label>
            <label>
              Phone
              <input name="phone" />
            </label>
            <label>
              Location
              <input name="location" />
            </label>
            <button className="button" type="submit" disabled={isSaving}>
              Add supplier
            </button>
          </form>
          <div className="list-stack">
            {suppliers.length === 0 ? <p className="helper-text">No suppliers yet.</p> : null}
            {suppliers.map((supplier) => (
              <div className="list-row" key={supplier.id}>
                <strong>{supplier.name}</strong>
                <span>{supplier.location ?? 'No location'}</span>
              </div>
            ))}
          </div>
        </section>

        <section className="panel">
          <h2>Receive Package</h2>
          <form className="compact-form" onSubmit={handlePackageSubmit}>
            <label>
              Supplier
              <select name="supplier_id" required disabled={suppliers.length === 0}>
                <option value="">Select supplier</option>
                {suppliers.map((supplier) => (
                  <option key={supplier.id} value={supplier.id}>
                    {supplier.name}
                  </option>
                ))}
              </select>
            </label>
            <label>
              Package name
              <input name="name" required />
            </label>
            <label>
              Reference
              <input name="reference" />
            </label>
            <label>
              Total items
              <input min="0" name="total_items" type="number" defaultValue="0" />
            </label>
            <label>
              Received date
              <input name="received_at" type="date" required />
            </label>
            <label>
              Notes
              <input name="notes" />
            </label>
            <button className="button" type="submit" disabled={isSaving || suppliers.length === 0}>
              Receive package
            </button>
          </form>
        </section>
      </div>

      <section className="panel">
        <div className="section-heading">
          <h2>Packages</h2>
          <span>{packages.length} records</span>
        </div>
        <div className="table-wrap">
          <table className="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Items</th>
                <th>Received</th>
              </tr>
            </thead>
            <tbody>
              {packages.map((shipment) => (
                <tr key={shipment.id}>
                  <td>{shipment.name}</td>
                  <td>{shipment.supplier?.name ?? `Supplier #${shipment.supplier_id}`}</td>
                  <td>{formatLabel(shipment.status)}</td>
                  <td>{shipment.total_items}</td>
                  <td>{shipment.received_at ?? '-'}</td>
                </tr>
              ))}
              {packages.length === 0 ? (
                <tr>
                  <td colSpan={5}>No packages received yet.</td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </section>

      <section className="panel">
        <div className="section-heading">
          <h2>Item Conditions</h2>
          <span>{items.length} records</span>
        </div>
        <div className="table-wrap">
          <table className="data-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Package</th>
                <th>Condition</th>
                <th>Price tier</th>
                <th>Qty</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              {items.map((item) => {
                const draft = conditionDrafts[item.id] ?? getInitialCondition(item);

                return (
                  <tr key={item.id}>
                    <td>{item.name}</td>
                    <td>
                      {item.package?.name ??
                        packageOptions.find((option) => option.id === item.package_id)?.name ??
                        `Package #${item.package_id}`}
                    </td>
                    <td>
                      <select
                        aria-label={`${item.name} condition`}
                        value={draft.condition}
                        onChange={(event) =>
                          updateConditionDraft(item.id, {
                            condition: event.target.value as ItemConditionValue,
                          })
                        }
                      >
                        {conditionOptions.map((condition) => (
                          <option key={condition} value={condition}>
                            {formatLabel(condition)}
                          </option>
                        ))}
                      </select>
                    </td>
                    <td>
                      <select
                        aria-label={`${item.name} price tier`}
                        value={draft.price_tier}
                        onChange={(event) =>
                          updateConditionDraft(item.id, {
                            price_tier: event.target.value as PriceTier,
                          })
                        }
                      >
                        {priceTierOptions.map((priceTier) => (
                          <option key={priceTier} value={priceTier}>
                            {priceTier}
                          </option>
                        ))}
                      </select>
                    </td>
                    <td>
                      <input
                        aria-label={`${item.name} condition quantity`}
                        min="1"
                        type="number"
                        value={draft.quantity}
                        onChange={(event) =>
                          updateConditionDraft(item.id, {
                            quantity: Number(event.target.value),
                          })
                        }
                      />
                    </td>
                    <td>
                      <button
                        className="button secondary"
                        type="button"
                        disabled={isSaving}
                        onClick={() => void handleConditionSubmit(item)}
                      >
                        Save
                      </button>
                    </td>
                  </tr>
                );
              })}
              {items.length === 0 ? (
                <tr>
                  <td colSpan={6}>No items ready for condition updates yet.</td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </section>
    </section>
  );
}
