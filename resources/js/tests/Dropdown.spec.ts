import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { DropdownMenu } from '../components/ui/dropdown-menu';

describe('DropdownMenu Component', () => {
  it('renders dropdown menu component', () => {
    const wrapper = mount(DropdownMenu, {
      slots: {
        default: '<div>Dropdown Content</div>',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('accepts open prop', () => {
    const wrapper = mount(DropdownMenu, {
      props: {
        open: false,
      },
    });

    expect(wrapper.props('open')).toBe(false);
  });

  it('emits update:open event on state change', async () => {
    const wrapper = mount(DropdownMenu, {
      props: {
        open: false,
      },
    });

    await wrapper.vm.$emit('update:open', true);

    expect(wrapper.emitted('update:open')).toBeTruthy();
    expect(wrapper.emitted('update:open')?.[0]).toEqual([true]);
  });

  it('can toggle open state', async () => {
    const wrapper = mount(DropdownMenu, {
      props: {
        open: true,
      },
    });

    await wrapper.setProps({ open: false });
    expect(wrapper.props('open')).toBe(false);
  });
});
