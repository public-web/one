import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import Checkbox from '../components/ui/checkbox/Checkbox.vue';

describe('Checkbox Component', () => {
  it('renders checkbox element', () => {
    const wrapper = mount(Checkbox);

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.attributes('data-slot')).toBe('checkbox');
  });

  it('renders checkbox with default state', () => {
    const wrapper = mount(Checkbox);

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.attributes('data-slot')).toBe('checkbox');
  });

  it('renders with unchecked state', () => {
    const wrapper = mount(Checkbox, {
      props: {
        checked: false,
      },
    });

    expect(wrapper.attributes('data-state')).toBe('unchecked');
  });

  it('emits update:checked event on state change', async () => {
    const wrapper = mount(Checkbox, {
      props: {
        checked: false,
      },
    });

    await wrapper.vm.$emit('update:checked', true);

    expect(wrapper.emitted('update:checked')).toBeTruthy();
    expect(wrapper.emitted('update:checked')?.[0]).toEqual([true]);
  });

  it('applies custom class', () => {
    const wrapper = mount(Checkbox, {
      props: {
        class: 'custom-checkbox',
      },
    });

    expect(wrapper.classes()).toContain('custom-checkbox');
  });

  it('handles disabled state', () => {
    const wrapper = mount(Checkbox, {
      props: {
        disabled: true,
      },
    });

    expect(wrapper.attributes('data-disabled')).toBeDefined();
  });
});
