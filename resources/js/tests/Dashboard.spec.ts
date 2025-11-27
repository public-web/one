import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Dashboard from '../Pages/Dashboard.vue';

// Mock Inertia
vi.mock('@inertiajs/vue3', () => ({
  Head: { template: '<div></div>' },
  router: { visit: vi.fn() },
}));

describe('Dashboard Page', () => {
  const mockProps = {
    canManageUsers: true,
    availableRoles: [
      { id: 1, name: 'admin' },
      { id: 2, name: 'user' },
    ],
    statistics: {
      total: 100,
      active: 80,
      expired: 5,
      with2FA: 30,
      inactive: 10,
      deleted: 5,
    },
    recentUsers: [],
    expiringUsers: [],
    roleDistribution: [
      { name: 'admin', count: 10 },
      { name: 'user', count: 90 },
    ],
  };

  it('renders dashboard component', () => {
    const wrapper = mount(Dashboard, {
      props: mockProps,
      global: {
        stubs: {
          Head: true,
          AppLayout: {
            template: '<div><slot /></div>',
          },
          Card: {
            template: '<div><slot /></div>',
          },
          CardHeader: {
            template: '<div><slot /></div>',
          },
          CardTitle: {
            template: '<div><slot /></div>',
          },
          CardDescription: {
            template: '<div><slot /></div>',
          },
          CardContent: {
            template: '<div><slot /></div>',
          },
          Button: {
            template: '<button><slot /></button>',
          },
          PlaceholderPattern: true,
        },
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('displays user statistics', () => {
    const wrapper = mount(Dashboard, {
      props: mockProps,
      global: {
        stubs: {
          Head: true,
          AppLayout: {
            template: '<div><slot /></div>',
          },
          Card: {
            template: '<div><slot /></div>',
          },
          CardHeader: {
            template: '<div><slot /></div>',
          },
          CardTitle: {
            template: '<div><slot /></div>',
          },
          CardDescription: {
            template: '<div><slot /></div>',
          },
          CardContent: {
            template: '<div><slot /></div>',
          },
          Button: true,
          PlaceholderPattern: true,
        },
      },
    });

    const html = wrapper.html();
    expect(html).toContain('100'); // total users
  });

  it('shows manage users option when user has permission', () => {
    const wrapper = mount(Dashboard, {
      props: mockProps,
      global: {
        stubs: {
          Head: true,
          AppLayout: {
            template: '<div><slot /></div>',
          },
          Card: {
            template: '<div><slot /></div>',
          },
          CardHeader: {
            template: '<div><slot /></div>',
          },
          CardTitle: {
            template: '<div><slot /></div>',
          },
          CardDescription: {
            template: '<div><slot /></div>',
          },
          CardContent: {
            template: '<div><slot /></div>',
          },
          Button: {
            template: '<button><slot /></button>',
          },
          PlaceholderPattern: true,
        },
      },
    });

    expect(wrapper.vm.canManageUsers).toBe(true);
  });

  it('computes breadcrumbs correctly', () => {
    const wrapper = mount(Dashboard, {
      props: mockProps,
      global: {
        stubs: {
          Head: true,
          AppLayout: {
            template: '<div><slot /></div>',
          },
          Card: true,
          CardHeader: true,
          CardTitle: true,
          CardDescription: true,
          CardContent: true,
          Button: true,
          PlaceholderPattern: true,
        },
      },
    });

    expect(wrapper.vm.breadcrumbs).toBeDefined();
    expect(Array.isArray(wrapper.vm.breadcrumbs)).toBe(true);
  });
});
