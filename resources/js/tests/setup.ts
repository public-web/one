import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Welcome from '../Components/Welcome.vue';

describe('Welcome Component', () => {
    it('muestra el mensaje por defecto', () => {
        const wrapper = mount(Welcome);
        
        expect(wrapper.text()).toContain('Bienvenido a Vue Testing');
    });

    it('muestra un mensaje personalizado', () => {
        const wrapper = mount(Welcome, {
            props: {
                message: 'Hola Juan Carlos'
            }
        });
        
        expect(wrapper.text()).toContain('Hola Juan Carlos');
    });

    it('emite evento clicked cuando se hace click en el botón', async () => {
        const wrapper = mount(Welcome);
        
        await wrapper.find('button').trigger('click');
        
        expect(wrapper.emitted('clicked')).toBeTruthy();
    });
});
