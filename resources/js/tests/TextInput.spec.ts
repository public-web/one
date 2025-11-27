import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import Input from '../components/ui/input/Input.vue';

describe('Input Component', () => {
  it('renders input element', () => {
    const wrapper = mount(Input);

    const input = wrapper.find('input');
    expect(input.exists()).toBe(true);
    expect(input.attributes('data-slot')).toBe('input');
  });

  it('binds modelValue correctly', async () => {
    const wrapper = mount(Input, {
      props: {
        modelValue: 'test value',
      },
    });

    const input = wrapper.find('input');
    expect(input.element.value).toBe('test value');
  });

  it('emits update:modelValue on input', async () => {
    const wrapper = mount(Input, {
      props: {
        modelValue: '',
      },
    });

    const input = wrapper.find('input');
    await input.setValue('new value');

    expect(wrapper.emitted('update:modelValue')).toBeTruthy();
    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['new value']);
  });

  it('applies custom class', () => {
    const wrapper = mount(Input, {
      props: {
        class: 'custom-class',
      },
    });

    const input = wrapper.find('input');
    expect(input.classes()).toContain('custom-class');
  });

  it('uses defaultValue when modelValue is not provided', () => {
    const wrapper = mount(Input, {
      props: {
        defaultValue: 'default text',
      },
    });

    const input = wrapper.find('input');
    expect(input.element.value).toBe('default text');
  });

  it('handles number type modelValue', async () => {
    const wrapper = mount(Input, {
      props: {
        modelValue: 42,
      },
    });

    const input = wrapper.find('input');
    expect(input.element.value).toBe('42');

    await input.setValue('100');
    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['100']);
  });
});
