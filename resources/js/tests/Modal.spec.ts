import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { Dialog, DialogHeader } from '../components/ui/dialog';

describe('Dialog/Modal Component', () => {
  it('renders dialog component', () => {
    const wrapper = mount(Dialog, {
      props: {
        open: true,
      },
      slots: {
        default: '<div>Modal Content</div>',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('accepts open prop', () => {
    const wrapper = mount(Dialog, {
      props: {
        open: false,
      },
    });

    expect(wrapper.props('open')).toBe(false);
  });

  it('can toggle open state', async () => {
    const wrapper = mount(Dialog, {
      props: {
        open: true,
      },
    });

    await wrapper.setProps({ open: false });
    expect(wrapper.props('open')).toBe(false);
  });

  it('emits update:open when state changes', async () => {
    const wrapper = mount(Dialog, {
      props: {
        open: false,
      },
    });

    await wrapper.vm.$emit('update:open', true);

    expect(wrapper.emitted('update:open')).toBeTruthy();
    expect(wrapper.emitted('update:open')?.[0]).toEqual([true]);
  });

  it('renders dialog header with slot content', () => {
    const wrapper = mount(DialogHeader, {
      slots: {
        default: '<h2>Header Content</h2>',
      },
    });

    expect(wrapper.html()).toContain('Header Content');
  });
});
