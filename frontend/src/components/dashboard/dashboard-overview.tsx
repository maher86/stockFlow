'use client';

import { useEffect, useState } from 'react';

import { fetchDashboardOverview, type DashboardOverview as DashboardOverviewData } from '../../services/api';

const emptyOverview: DashboardOverviewData = {
  suppliers_count: 0,
  customers_count: 0,
  packages_count: 0,
  items_count: 0,
  invoices_count: 0,
  revenue_total: 0,
  outstanding_total: 0,
};

export function DashboardOverview() {
  const [overview, setOverview] = useState<DashboardOverviewData>(emptyOverview);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    let isMounted = true;

    fetchDashboardOverview()
      .then((data) => {
        if (isMounted) {
          setOverview(data);
        }
      })
      .finally(() => {
        if (isMounted) {
          setIsLoading(false);
        }
      });

    return () => {
      isMounted = false;
    };
  }, []);

  return (
    <section>
      <h1>Dashboard</h1>
      <p>{isLoading ? 'Loading live stats...' : 'Live inventory and invoice overview'}</p>
      <div className="dashboard-grid">
        <Metric label="Suppliers" value={overview.suppliers_count} />
        <Metric label="Customers" value={overview.customers_count} />
        <Metric label="Packages" value={overview.packages_count} />
        <Metric label="Items" value={overview.items_count} />
        <Metric label="Invoices" value={overview.invoices_count} />
        <Metric label="Revenue" value={overview.revenue_total} />
        <Metric label="Outstanding" value={overview.outstanding_total} />
      </div>
    </section>
  );
}

type MetricProps = {
  label: string;
  value: number;
};

function Metric({ label, value }: MetricProps) {
  return (
    <div className="metric">
      <span>{label}</span>
      <strong>{value.toLocaleString()}</strong>
    </div>
  );
}
