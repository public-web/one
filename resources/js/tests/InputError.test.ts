import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import InputError from '../components/InputError.vue';

describe('InputError Component', () => {
    it('no se muestra cuando no hay mensaje', () => {
        const wrapper = mount(InputError);
        
        // Verificar que el componente está vacío
        expect(wrapper.text()).toBe('');
    });

    it('se muestra cuando hay un mensaje', () => {
        const wrapper = mount(InputError, {
            props: {
                message: 'Este campo es requerido'
            }
        });
        
        expect(wrapper.text()).toContain('Este campo es requerido');
    });

    it('tiene las clases CSS correctas', () => {
        const wrapper = mount(InputError, {
            props: {
                message: 'Error de validación'
            }
        });
        
        const p = wrapper.find('p');
        expect(p.classes()).toContain('text-sm');
        expect(p.classes()).toContain('text-red-600');
    });

    it('actualiza el mensaje cuando cambia la prop', async () => {
        const wrapper = mount(InputError, {
            props: {
                message: 'Mensaje inicial'
            }
        });
        
        expect(wrapper.text()).toContain('Mensaje inicial');
        
        // Cambiar la prop
        await wrapper.setProps({ message: 'Mensaje actualizado' });
        
        expect(wrapper.text()).toContain('Mensaje actualizado');
    });
});
